<?php

namespace Tests\Feature;

use App\Models\BookingToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTokenIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $manager;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->user = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'name' => 'Token Buyer Student',
            'email' => 'tokenbuyer@student.test',
        ]);
    }

    public function test_admin_can_view_all_tokens()
    {
        $token = BookingToken::factory()->active()->create([
            'user_id' => $this->user->id,
            'paid_at' => now()->subDay(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.tokens.index'))
            ->assertOk()
            ->assertSee('Unlock Access Tokens')
            ->assertSee($token->token_ref)
            ->assertSee('Token Buyer Student');
    }

    public function test_tokens_index_requires_admin_role()
    {
        $this->actingAs($this->manager)
            ->get(route('admin.tokens.index'))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->get(route('admin.tokens.index'))
            ->assertForbidden();

        \Illuminate\Support\Facades\Auth::logout();

        $this->get(route('admin.tokens.index'))
            ->assertRedirect(route('login'));
    }

    public function test_tokens_index_can_filter_and_search()
    {
        $active = BookingToken::factory()->active()->create([
            'user_id' => $this->user->id,
            'paid_at' => now(),
        ]);
        $failed = BookingToken::factory()->failed()->create([
            'user_id' => $this->user->id,
            'paid_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.tokens.index', ['status' => 'failed']))
            ->assertOk()
            ->assertSee($failed->token_ref)
            ->assertDontSee($active->token_ref);

        $this->actingAs($this->admin)
            ->get(route('admin.tokens.index', ['q' => 'Token Buyer']))
            ->assertOk()
            ->assertSee($active->token_ref)
            ->assertSee($failed->token_ref);
    }

    public function test_admin_dashboard_links_to_tokens_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.tokens.index'), false);
    }
}