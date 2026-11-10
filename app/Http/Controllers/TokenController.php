<?php

namespace App\Http\Controllers;

use App\Models\BookingToken;
use App\Services\PaystackService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TokenController extends Controller
{
    protected $paystack;

    protected $whatsapp;

    public function __construct(PaystackService $paystack, WhatsAppService $whatsapp)
    {
        $this->paystack = $paystack;
        $this->whatsapp = $whatsapp;
    }

    public function buy()
    {
        $user = auth()->user();
        $active = BookingToken::activeFor($user);
        $intended = Session::get('booking.intended');

        return view('pages.token-buy', compact('user', 'active', 'intended'));
    }

    public function pay()
    {
        $user = auth()->user();

        if (BookingToken::activeFor($user)) {
            return redirect()->route('token.buy')->with('info', 'You already have an active booking access token.');
        }

        $pending = BookingToken::latestOpenFor($user);
        if ($pending && $pending->isPending() && $pending->paystack_reference) {
            $result = $this->paystack->initialize(
                $user->email,
                $pending->paystack_reference,
                BookingToken::PRICE_GHS,
                ['user_id' => $user->id, 'token_ref' => $pending->token_ref]
            );

            if ($result['ok']) {
                return redirect()->away($result['authorization_url']);
            }

            $pending->update(['status' => BookingToken::STATUS_FAILED]);

            return back()->withErrors(['token' => $result['message']])->withInput();
        }

        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $user->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
            'paystack_reference' => BookingToken::paystackReference(),
        ]);

        $result = $this->paystack->initialize(
            $user->email,
            $token->paystack_reference,
            BookingToken::PRICE_GHS,
            ['user_id' => $user->id, 'token_ref' => $token->token_ref]
        );

        if (! $result['ok']) {
            $token->update(['status' => BookingToken::STATUS_FAILED]);

            return back()->withErrors(['token' => $result['message']])->withInput();
        }

        return redirect()->away($result['authorization_url']);
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference', $request->input('reference'));

        if (! $reference) {
            return redirect()->route('token.buy')->withErrors(['token' => 'We did not receive a payment reference. Please try again.']);
        }

        $token = BookingToken::where('paystack_reference', $reference)->first();

        if (! $token) {
            return redirect()->route('token.buy')->withErrors(['token' => 'We could not find that payment reference.']);
        }

        if (auth()->guest() && $token->user) {
            auth()->login($token->user);
        }

        if ($token->isActive()) {
            return $this->continueOrSuccess('Your booking access token is active.');
        }

        $result = $this->paystack->verify($reference);

        if ($result['ok'] && $result['status'] === 'success') {
            $token->activate($result['transaction_ref']);

            if (auth()->guest() && $token->user) {
                auth()->login($token->user);
            }

            $this->whatsapp->notifyTokenActivated($token->load('user'));

            return $this->continueOrSuccess('Payment verified. Your booking access token is now active.');
        }

        $token->update(['status' => BookingToken::STATUS_FAILED]);

        return redirect()->route('token.buy')->withErrors(['token' => $result['message'] ?? 'Payment could not be verified.']);
    }

    public function success()
    {
        if (Session::has('booking.intended')) {
            return redirect(Session::pull('booking.intended'));
        }

        $active = BookingToken::activeFor(auth()->user());
        $intended = Session::get('booking.intended');

        return view('pages.token-success', compact('active', 'intended'));
    }

    protected function continueOrSuccess(string $message)
    {
        if (Session::has('booking.intended')) {
            return redirect(Session::pull('booking.intended'))->with('success', $message);
        }

        return redirect()->route('token.success')->with('success', $message);
    }
}
