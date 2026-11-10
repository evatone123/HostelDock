@php($recaptchaEnabled = app(\App\Services\RecaptchaService::class)->isEnabled())
@if ($recaptchaEnabled)
    <div class="mb-3">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
    </div>
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endif