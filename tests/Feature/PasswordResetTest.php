<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_loads()
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Forgot Password?')
            ->assertSee('Send Password Reset Link');
    }

    public function test_user_can_request_password_reset_link()
    {
        Notification::fake();

        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_request_does_not_reveal_whether_email_exists()
    {
        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHas('status');
    }

    public function test_manager_can_request_password_reset_link()
    {
        Notification::fake();

        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->post(route('password.email'), ['email' => $manager->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($manager, ResetPassword::class);
    }

    public function test_reset_password_page_loads_with_token()
    {
        $this->get(route('password.reset', 'some-token'))
            ->assertOk()
            ->assertSee('Reset Password')
            ->assertSee('Update Password');
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'new-password-123',
        ])->assertRedirect(route('profile'));
    }

    public function test_reset_password_rejects_invalid_token()
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $oldHash = $user->password;

        $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
            ->assertSessionHasErrors('email');

        $this->assertSame($oldHash, $user->fresh()->password);
    }

    public function test_reset_password_requires_minimum_length()
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }
}