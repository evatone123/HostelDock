<?php

namespace App\Http\Controllers\Manager;

use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use Illuminate\Http\Request;

class ManagerHostelPaymentController extends ManagerController
{
    public function edit(Hostel $hostel)
    {
        $this->authorizeHostel($hostel);

        $detail = $hostel->paymentDetail ?? new HostelPaymentDetail(['hostel_id' => $hostel->id]);

        return view('manager.hostels.payment-details', compact('hostel', 'detail'));
    }

    public function update(Hostel $hostel, Request $request)
    {
        $this->authorizeHostel($hostel);

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
        ], [
            'paystack_secret_key.max' => 'The Paystack secret key must not exceed 255 characters.',
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
            ->route('manager.hostels.payments.edit', $hostel)
            ->with('success', 'Hostel payment gateways updated.');
    }
}
