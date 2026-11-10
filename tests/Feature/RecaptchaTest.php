<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RecaptchaTest extends TestCase
{
    use RefreshDatabase;

    protected $passwordUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->passwordUser = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'email' => 'captcha@student.test',
            'password' => bcrypt('secret123'),
        ]);
    }

    protected function enableRecaptcha(bool $verificationResult = true)
    {
        config([
            'services.recaptcha.enabled' => true,
            'services.recaptcha.site_key' => '6L-test-site-key',
            'services.recaptcha.secret_key' => '6L-test-secret-key',
        ]);

        Http::fake([
            'google.com/recaptcha/*' => Http::response(['success' => $verificationResult], 200),
        ]);
    }

    public function test_login_is_unaffected_when_recaptcha_is_disabled()
    {
        $this->post(route('login'), [
            'email' => 'captcha@student.test',
            'password' => 'secret123',
        ])->assertRedirect(route('profile'));

        $this->assertAuthenticatedAs($this->passwordUser);
    }

    public function test_login_is_rejected_when_recaptcha_fails_verification()
    {
        $this->enableRecaptcha(false);

        $this->post(route('login'), [
            'email' => 'captcha@student.test',
            'password' => 'secret123',
            'g-recaptcha-response' => 'invalid-token',
        ])->assertSessionHasErrors('recaptcha');

        $this->assertGuest();
    }

    public function test_login_is_rejected_without_recaptcha_token()
    {
        $this->enableRecaptcha(false);

        $this->post(route('login'), [
            'email' => 'captcha@student.test',
            'password' => 'secret123',
        ])->assertSessionHasErrors('recaptcha');

        $this->assertGuest();
    }

    public function test_login_succeeds_when_recaptcha_token_is_verified()
    {
        $this->enableRecaptcha(true);

        $this->post(route('login'), [
            'email' => 'captcha@student.test',
            'password' => 'secret123',
            'g-recaptcha-response' => 'valid-token',
        ])->assertRedirect(route('profile'));

        $this->assertAuthenticatedAs($this->passwordUser);
    }

    public function test_register_is_rejected_when_recaptcha_fails_verification()
    {
        $this->enableRecaptcha(false);

        $this->post(route('register'), [
            'name' => 'New Captcha User',
            'email' => 'newcaptcha@student.test',
            'phone' => '0241234567',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'g-recaptcha-response' => 'invalid-token',
        ])->assertSessionHasErrors('recaptcha');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'newcaptcha@student.test']);
    }

    public function test_register_succeeds_when_recaptcha_token_is_verified()
    {
        $this->enableRecaptcha(true);

        $this->post(route('register'), [
            'name' => 'New Captcha User',
            'email' => 'newcaptcha@student.test',
            'phone' => '0241234567',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'g-recaptcha-response' => 'valid-token',
        ])->assertRedirect(route('profile'));

        $this->assertDatabaseHas('users', ['email' => 'newcaptcha@student.test']);
        $this->assertAuthenticated();
    }

    public function test_login_page_shows_recaptcha_widget_when_enabled()
    {
        $this->enableRecaptcha(true);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('g-recaptcha')
            ->assertSee('recaptcha/api.js');
    }

    public function test_login_page_hides_recaptcha_widget_when_disabled()
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('g-recaptcha')
            ->assertDontSee('recaptcha/api.js');
    }
}