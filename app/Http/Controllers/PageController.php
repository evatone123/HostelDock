<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request, WhatsAppService $whatsapp)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'subject' => 'required|string|max:191',
            'message' => 'required|string|max:5000',
        ]);

        DB::table('contacts')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $whatsapp->notifyAdminContact($validated);

        return back()->with('success', 'Your message has been sent. We will get back to you soon.');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function residency()
    {
        return view('pages.residency');
    }
}