<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use App\Models\Room;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $student;

    protected $hostel;

    protected $room;

    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->hostel = Hostel::create([
            'name' => 'Payment Test Hostel',
            'slug' => 'payment-test-hostel',
            'institution' => 'Payment Test University',
            'institution_name' => 'Payment Test University, Accra',
            'location' => 'Accra',
            'distance' => 'Near campus',
            'rating' => 4.5,
            'description' => 'Hostel used by the booking payment flow test.',
            'starting_price' => 900,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'hostel_id' => $this->hostel->id,
            'name' => 'Two in a room',
            'room_type' => 'Two in a room',
            'gender_category' => 'Unisex',
            'price' => 900,
            'total_rooms' => 6,
        ]);

        $this->booking = Booking::create([
            'booking_ref' => 'SRB-PAYTEST01',
            'user_id' => $this->student->id,
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->room->id,
            'student_name' => 'Payment Student',
            'email' => 'pay@student.test',
            'phone' => '0240000000',
            'institution' => 'Payment Test University',
            'gender' => 'Female',
            'room_type' => 'Two in a room',
            'academic_year' => '2026/2027',
            'amount' => 900,
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);
    }

    protected function enableHostelPaystack()
    {
        HostelPaymentDetail::create([
            'hostel_id' => $this->hostel->id,
            'is_active' => true,
            'paystack_enabled' => true,
            'paystack_public_key' => 'pk_test_hostel',
            'paystack_secret_key' => 'sk_test_hostel',
        ]);
    }

    protected function mockInitialize(string $secretKey = 'sk_test_hostel', bool $ok = true)
    {
        $this->mock(PaystackService::class, function ($mock) use ($secretKey, $ok) {
            $mock->shouldReceive('initialize')
                ->once()
                ->with(
                    'pay@student.test',
                    \Mockery::on(fn ($ref) => is_string($ref) && substr($ref, 0, 5) === 'PSKB-'),
                    900.0,
                    \Mockery::type('array'),
                    $secretKey,
                    route('booking.payment.callback')
                )
                ->andReturn([
                    'ok' => $ok,
                    'authorization_url' => 'https://paystack.test/authorize/hostel',
                    'message' => $ok ? 'Authorization URL created.' : 'Authorization failed.',
                ]);
        });
    }

    protected function mockVerify(bool $success = true)
    {
        $this->mock(PaystackService::class, function ($mock) use ($success) {
            $mock->shouldReceive('verify')
                ->once()
                ->with(\Mockery::on(fn ($ref) => is_string($ref)), 'sk_test_hostel')
                ->andReturn([
                    'ok' => $success,
                    'status' => $success ? 'success' : 'failed',
                    'transaction_ref' => 'PSK-TXN-90001',
                    'paid_at' => now()->toIso8601String(),
                    'amount' => 900.0,
                    'message' => $success ? 'Transaction verified.' : 'Transaction failed.',
                ]);
        });
    }

    public function test_pay_route_is_unavailable_when_hostel_has_no_paystack()
    {
        $this->actingAs($this->student)
            ->post(route('booking.pay', $this->booking))
            ->assertNotFound();
    }

    public function test_pay_initializes_with_hostel_secret_key_and_redirects_to_paystack()
    {
        $this->enableHostelPaystack();
        $this->mockInitialize();

        $this->actingAs($this->student)
            ->post(route('booking.pay', $this->booking))
            ->assertRedirect('https://paystack.test/authorize/hostel');

        $this->assertNotNull($this->booking->fresh()->paystack_reference);
        $this->assertEquals(Booking::PAYMENT_UNPAID, $this->booking->fresh()->payment_status);
    }

    public function test_pay_marks_booking_failed_when_initialization_fails()
    {
        $this->enableHostelPaystack();
        $this->mockInitialize('sk_test_hostel', false);

        $this->actingAs($this->student)
            ->post(route('booking.pay', $this->booking))
            ->assertRedirect();

        $this->assertEquals(Booking::PAYMENT_FAILED, $this->booking->fresh()->payment_status);
    }

    public function test_callback_marks_booking_paid_and_confirmed_after_success()
    {
        $this->enableHostelPaystack();

        $this->booking->paystack_reference = 'PSKB-CALLBACK001';
        $this->booking->save();

        $this->mockVerify();

        $this->actingAs($this->student)
            ->get(route('booking.payment.callback', ['reference' => 'PSKB-CALLBACK001']))
            ->assertRedirect(route('booking.confirmation', $this->booking->booking_ref));

        $booking = $this->booking->fresh();
        $this->assertEquals(Booking::PAYMENT_PAID, $booking->payment_status);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('PSK-TXN-90001', $booking->paystack_transaction_ref);
        $this->assertNotNull($booking->paid_at);
    }

    public function test_callback_is_idempotent_for_already_paid_booking()
    {
        $this->enableHostelPaystack();

        $this->booking->paystack_reference = 'PSKB-ALREADYPAID';
        $this->booking->payment_status = Booking::PAYMENT_PAID;
        $this->booking->paid_at = now();
        $this->booking->save();

        $this->mock(PaystackService::class, function ($mock) {
            $mock->shouldReceive('verify')->never();
        });

        $this->actingAs($this->student)
            ->get(route('booking.payment.callback', ['reference' => 'PSKB-ALREADYPAID']))
            ->assertRedirect(route('booking.confirmation', $this->booking->booking_ref));
    }

    public function test_callback_marks_failed_and_keeps_pending_on_verification_failure()
    {
        $this->enableHostelPaystack();

        $this->booking->paystack_reference = 'PSKB-FAILED001';
        $this->booking->save();

        $this->mockVerify(false);

        $this->actingAs($this->student)
            ->get(route('booking.payment.callback', ['reference' => 'PSKB-FAILED001']))
            ->assertRedirect(route('booking.confirmation', $this->booking->booking_ref))
            ->assertSessionHasErrors('payment');

        $booking = $this->booking->fresh();
        $this->assertEquals(Booking::PAYMENT_FAILED, $booking->payment_status);
        $this->assertEquals('pending', $booking->status);
    }

    public function test_callback_with_unknown_reference_redirects_home_with_error()
    {
        $this->get(route('booking.payment.callback', ['reference' => 'PSKB-UNKNOWN']))
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('payment');
    }

    public function test_pay_is_forbidden_for_another_user()
    {
        $this->enableHostelPaystack();

        $other = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($other)
            ->post(route('booking.pay', $this->booking))
            ->assertForbidden();
    }
}
