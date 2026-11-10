<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use Illuminate\Http\Request;

class AdminHostelPaymentController extends Controller
{
    public function edit(Hostel $hostel)
    {
        $detail = $hostel->paymentDetail ?? new HostelPaymentDetail(['hostel_id' => $hostel->id]);

        return view('admin.hostels.payment-details', compact('hostel', 'detail'));
    }

    public function update(Hostel $hostel, Request $request)
    {
        $validated = $request->validate([
            'momo_api_enabled' => 'nullable|boolean',
            'momo_api_user' => 'nullable|string|max:191',
            'momo_api_key' => 'nullable|string|max:255',
            'momo_subscription_key' => 'nullable|string|max:255',
            'momo_target_environment' => 'nullable|in:sandbox,mtnmomo',
            'merchant_number' => 'nullable|string|max:191',
            'momo_provider' => 'nullable|string|max:191',
            'momo_number' => 'nullable|string|max:191',
            'momo_account_name' => 'nullable|string|max:191',
            'bank_name' => 'nullable|string|max:191',
            'bank_branch' => 'nullable|string|max:191',
            'bank_account_name' => 'nullable|string|max:191',
            'bank_account_number' => 'nullable|string|max:191',
            'instructions' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
            'paystack_enabled' => 'nullable|boolean',
            'paystack_public_key' => 'nullable|string|max:191',
            'paystack_secret_key' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['paystack_enabled'] = $request->boolean('paystack_enabled');
        $validated['momo_api_enabled'] = $request->boolean('momo_api_enabled');
        $validated['momo_target_environment'] = $validated['momo_target_environment'] ?? 'sandbox';

        foreach (['paystack_secret_key', 'momo_api_key', 'momo_subscription_key'] as $secretField) {
            if (empty($validated[$secretField])) {
                unset($validated[$secretField]);
            }
        }

        HostelPaymentDetail::updateOrCreate(
            ['hostel_id' => $hostel->id],
            $validated
        );

        return redirect()
            ->route('admin.hostels.payments.edit', $hostel)
            ->with('success', 'Hostel payment gateways updated.');
    }
}