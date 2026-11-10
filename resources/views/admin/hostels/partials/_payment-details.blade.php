<div class="row g-3">
    <p class="small text-muted mb-0">
        Configure how students can pay for rooms at <strong>{{ $hostel->displayName() }}</strong>.
        The manual methods below are shown on the booking confirmation and invoice; when a gateway
        (MTN MoMo API or Paystack) is enabled, students can also pay online directly into this hostel&rsquo;s account.
    </p>

    <div class="col-12"><hr></div>

    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0"><i class="bi bi-phone me-1"></i>MTN Mobile Money (automatic)</h6>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="momo_api_enabled" value="1" id="momo_api_enabled"
                @checked(old('momo_api_enabled', $detail->momo_api_enabled ?? false))>
            <label class="form-check-label" for="momo_api_enabled">Enable MTN MoMo API for this hostel</label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">API User</label>
        <input type="text" class="form-control font-monospace" name="momo_api_user" maxlength="191" value="{{ old('momo_api_user', $detail->momo_api_user ?? '') }}" placeholder="e.g. 3d9e3e6b-..." autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label">API Key</label>
        <input type="password" class="form-control font-monospace" name="momo_api_key" maxlength="191" value="" placeholder="Leave blank to keep the current key" autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label">Product Subscription Key</label>
        <input type="password" class="form-control font-monospace" name="momo_subscription_key" maxlength="191" value="" placeholder="Leave blank to keep the current key" autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label">Target Environment</label>
        <select class="form-select" name="momo_target_environment">
            <option value="sandbox" @selected(old('momo_target_environment', $detail->momo_target_environment ?? 'sandbox') === 'sandbox')>Sandbox (test)</option>
            <option value="mtnmomo" @selected(old('momo_target_environment', $detail->momo_target_environment ?? 'sandbox') === 'mtnmomo')>Live / mtnmomo</option>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Merchant Number <small class="text-muted">(shown to students as the payee)</small></label>
        <input type="text" class="form-control" name="merchant_number" maxlength="191" value="{{ old('merchant_number', $detail->merchant_number ?? '') }}" placeholder="e.g. 0241111111">
    </div>
    <div class="col-12">
        <p class="small text-muted mb-0">
            Requires an <strong>MTN Open API &ldquo;Collection&rdquo; subscription</strong> in the hostel&rsquo;s MTN name.
            Get the API user, API key and product subscription key from the MTN Open API portal.
            On the subscription, set the <strong>Callback URL</strong> to <code class="text-body">{{ config('app.url') }}/api/momo/callback</code>.
            Students will receive a payment prompt on their phone (*170# or the MTN MoMo app) which they approve.
        </p>
    </div>

    <div class="col-12"><hr></div>

    <h6 class="mb-2"><i class="bi bi-phone me-1"></i>Manual Mobile Money</h6>
    <div class="col-md-4">
        <label class="form-label">Provider</label>
        <select class="form-select" name="momo_provider">
            <option value="">Select provider</option>
            @foreach (['MTN', 'Telecel', 'AT'] as $provider)
                <option value="{{ $provider }}" @selected(old('momo_provider', $detail->momo_provider ?? '') === $provider)>{{ $provider }} MoMo</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">MoMo Number</label>
        <input type="text" class="form-control" name="momo_number" maxlength="191" value="{{ old('momo_number', $detail->momo_number ?? '') }}" placeholder="e.g. 0201234567">
    </div>
    <div class="col-md-4">
        <label class="form-label">MoMo Account Name</label>
        <input type="text" class="form-control" name="momo_account_name" maxlength="191" value="{{ old('momo_account_name', $detail->momo_account_name ?? '') }}">
    </div>

    <div class="col-12"><hr></div>

    <h6 class="mb-2"><i class="bi bi-bank me-1"></i>Bank Transfer</h6>
    <div class="col-md-3">
        <label class="form-label">Bank Name</label>
        <input type="text" class="form-control" name="bank_name" maxlength="191" value="{{ old('bank_name', $detail->bank_name ?? '') }}" placeholder="e.g. GCB Bank">
    </div>
    <div class="col-md-3">
        <label class="form-label">Branch</label>
        <input type="text" class="form-control" name="bank_branch" maxlength="191" value="{{ old('bank_branch', $detail->bank_branch ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Account Name</label>
        <input type="text" class="form-control" name="bank_account_name" maxlength="191" value="{{ old('bank_account_name', $detail->bank_account_name ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Account Number</label>
        <input type="text" class="form-control" name="bank_account_number" maxlength="191" value="{{ old('bank_account_number', $detail->bank_account_number ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Payment Instructions <small class="text-muted">(shown to students)</small></label>
        <textarea class="form-control" name="instructions" rows="3" maxlength="2000" placeholder="e.g. Send proof of payment to 024 000 0000 for confirmation.">{{ old('instructions', $detail->instructions ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label d-block">Show manual payment methods</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $detail->is_active ?? true))>
            <label class="form-check-label" for="is_active">Display the manual MoMo / bank details to students</label>
        </div>
    </div>

    <div class="col-12"><hr></div>

    <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="mb-0"><i class="bi bi-credit-card me-1"></i>Paystack Online Payment</h6>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="paystack_enabled" value="1" id="paystack_enabled"
                @checked(old('paystack_enabled', $detail->paystack_enabled ?? false))>
            <label class="form-check-label" for="paystack_enabled">Enable Paystack for this hostel</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Paystack Public Key</label>
        <input type="text" class="form-control font-monospace" name="paystack_public_key" maxlength="191" value="{{ old('paystack_public_key', $detail->paystack_public_key ?? '') }}" placeholder="pk_live_... / pk_test_...">
    </div>
    <div class="col-md-6">
        <label class="form-label">Paystack Secret Key</label>
        <input type="password" class="form-control font-monospace" name="paystack_secret_key" maxlength="191" value="" placeholder="Leave blank to keep the current key" autocomplete="off">
    </div>
    <div class="col-12">
        <p class="small text-muted mb-0">
            The hostel must have its own Paystack account. Generate the keys under
            <strong>Paystack Dashboard &rarr; Settings &rarr; API Keys &amp; Webhooks</strong>.
            Use <code>sk_test_...</code> keys for testing. Room payments go directly to this hostel&rsquo;s Paystack merchant.
            On the Paystack dashboard, set the callback URL to <code>{{ url('booking/payment/callback') }}</code>.
        </p>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary mb-0">
            <i class="bi bi-check-lg me-1"></i>Save Gateway Settings
        </button>
        <a href="{{ route($backRoute) }}" class="btn btn-light mb-0">Cancel</a>
    </div>
</div>