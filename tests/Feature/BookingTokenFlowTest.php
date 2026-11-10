<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTokenFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $student;

    protected $hostel;

    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->hostel = Hostel::create([
            'name' => 'Flow Test Hostel',
            'slug' => 'flow-test-hostel',
            'institution' => 'Flow Test University',
            'institution_name' => 'Flow Test University, Accra',
            'location' => 'Accra',
            'distance' => 'Near campus',
            'rating' => 4.5,
            'description' => 'Hostel used by the booking token flow test.',
            'starting_price' => 700,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'hostel_id' => $this->hostel->id,
            'name' => 'Two in a room',
            'room_type' => 'Two in a room',
            'gender_category' => 'Unisex',
            'price' => 700,
            'total_rooms' => 6,
        ]);
    }

    protected function mockInitialize(bool $ok = true)
    {
        $this->mock(PaystackService::class, function ($mock) use ($ok) {
            $mock->shouldReceive('initialize')->once()->andReturn([
                'ok' => $ok,
                'authorization_url' => 'https://paystack.test/authorize/abc',
                'message' => $ok ? 'Authorization URL created.' : 'Authorization failed.',
            ]);
        });
    }

    protected function mockVerify(bool $success = true)
    {
        $this->mock(PaystackService::class, function ($mock) use ($success) {
            $mock->shouldReceive('verify')->once()->andReturn([
                'ok' => $success,
                'status' => $success ? 'success' : 'failed',
                'transaction_ref' => 'PSK-TXN-0001',
                'paid_at' => now()->toIso8601String(),
                'amount' => BookingToken::PRICE_GHS,
                'message' => $success ? 'Transaction verified.' : 'Transaction failed.',
            ]);
        });
    }

    public function test_user_without_active_token_is_redirected_to_buy_page()
    {
        $this->actingAs($this->student)
            ->get(route('booking.create', $this->hostel))
            ->assertRedirect(route('token.buy'))
            ->assertSessionHas('booking.intended', route('booking.create', $this->hostel));

        $this->assertDatabaseMissing('booking_tokens', ['user_id' => $this->student->id]);
    }

    public function test_buy_page_shows_price_and_pay_form()
    {
        $this->actingAs($this->student)
            ->get(route('token.buy'))
            ->assertOk()
            ->assertSee(number_format(BookingToken::PRICE_GHS, 2));
    }

    public function test_pay_creates_pending_token_and_redirects_to_paystack()
    {
        $this->mockInitialize();

        $this->actingAs($this->student)
            ->post(route('token.pay'))
            ->assertRedirect('https://paystack.test/authorize/abc');

        $this->assertDatabaseHas('booking_tokens', [
            'user_id' => $this->student->id,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
        ]);
    }

    public function test_callback_activates_token_after_successful_payment()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-0001',
        ]);

        $this->mockVerify();

        $this->actingAs($this->student)
            ->get(route('token.callback', ['reference' => 'PSK-REF-0001']))
            ->assertRedirect(route('token.success'));

        $token->refresh();
        $this->assertEquals(BookingToken::STATUS_ACTIVE, $token->status);
        $this->assertEquals('PSK-TXN-0001', $token->paystack_transaction_ref);
        $this->assertNotNull($token->paid_at);
    }

    public function test_callback_redirects_to_intended_room_after_payment()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-INT-1',
        ]);

        $this->mockVerify();

        $intendedUrl = route('booking.create', ['hostel' => $this->hostel, 'room' => $this->room->id]);

        $this->actingAs($this->student)
            ->withSession(['booking.intended' => $intendedUrl])
            ->get(route('token.callback', ['reference' => 'PSK-REF-INT-1']))
            ->assertRedirect($intendedUrl)
            ->assertSessionMissing('booking.intended');

        $token->refresh();
        $this->assertEquals(BookingToken::STATUS_ACTIVE, $token->status);
    }

    public function test_callback_respects_intended_url_for_already_active_token()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-AI-1',
            'paid_at' => now(),
        ]);

        $intendedUrl = route('booking.create', ['hostel' => $this->hostel, 'room' => $this->room->id]);

        $this->actingAs($this->student)
            ->withSession(['booking.intended' => $intendedUrl])
            ->get(route('token.callback', ['reference' => 'PSK-REF-AI-1']))
            ->assertRedirect($intendedUrl)
            ->assertSessionMissing('booking.intended');
    }

    public function test_booking_form_preselects_intended_room()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-PR-1',
            'paid_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->get(route('booking.create', ['hostel' => $this->hostel, 'room' => $this->room->id]))
            ->assertOk()
            ->assertSee('id="room_' . $this->room->id . '"', false)
            ->assertSee('class="form-check-input mt-0" checked', false);
    }

    public function test_callback_activates_token_when_user_is_guest()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-GUEST-1',
        ]);

        $this->mockVerify();

        $this->get(route('token.callback', ['reference' => 'PSK-REF-GUEST-1']))
            ->assertRedirect(route('token.success'));

        $token->refresh();
        $this->assertEquals(BookingToken::STATUS_ACTIVE, $token->status);
        $this->assertAuthenticatedAs($this->student);
    }

    public function test_active_token_allows_booking_and_is_consumed_on_store()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-AC-1',
        ]);

        $this->actingAs($this->student)
            ->get(route('booking.create', $this->hostel))
            ->assertOk();

        $this->actingAs($this->student)
            ->post(route('booking.store', $this->hostel), [
                'room' => $this->room->id,
                'student_name' => 'Flow Student',
                'email' => 'flow@student.test',
                'phone' => '0240000000',
                'institution' => 'Flow Test University',
                'gender' => 'Female',
                'academic_year' => '2026/2027',
            ])
            ->assertRedirect(route('booking.confirmation', [
                'booking' => Booking::where('user_id', $this->student->id)->latest('id')->value('booking_ref'),
            ]));

        $room = $this->room->fresh();
        $this->assertSame(12, $room->totalBeds());
        $this->assertSame(11, $room->availableSpace());

        $token->refresh();
        $this->assertEquals(BookingToken::STATUS_USED, $token->status);
        $this->assertNotNull($token->used_at);
        $this->assertNotNull($token->used_by_booking_id);
    }

    public function test_used_token_no_longer_grants_booking_access()
    {
        $token = BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_USED,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-US-1',
            'used_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->get(route('booking.create', $this->hostel))
            ->assertRedirect(route('token.buy'));
    }
}
