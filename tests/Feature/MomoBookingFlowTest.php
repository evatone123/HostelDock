<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use App\Models\User;
use App\Services\MTNMoMoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MomoBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $student;

    protected $hostel;

    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mtnmomo.sandbox_base_url' => 'https://sandbox.mtn.test',
            'mtnmomo.currency' => 'GHS',
            'services.whatsapp.enabled' => false,
        ]);

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->hostel = Hostel::create([
            'name' => 'MoMo Test Hostel',
            'slug' => 'momo-test-hostel',
            'institution' => 'MoMo Test University',
            'institution_name' => 'MoMo Test University, Accra',
            'location' => 'Accra',
            'distance' => 'Near campus',
            'rating' => 4.5,
            'description' => 'Hostel used by the momo payment flow test.',
            'starting_price' => 900,
            'is_active' => true,
        ]);

        $this->booking = Booking::create([
            'booking_ref' => 'HSD-MOMO001',
            'user_id' => $this->student->id,
            'hostel_id' => $this->hostel->id,
            'student_name' => 'MoMo Student',
            'email' => 'momo@student.test',
            'phone' => '0240000000',
            'institution' => 'MoMo Test University',
            'gender' => 'Male',
            'room_type' => 'Single Room',
            'academic_year' => '2026/2027',
            'amount' => 900,
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);
    }

    protected function enableHostelMomo()
    {
        HostelPaymentDetail::create([
            'hostel_id' => $this->hostel->id,
            'is_active' => true,
            'momo_api_enabled' => true,
            'momo_api_user' => 'api-user-01',
            'momo_api_key' => 'api-key-01',
            'momo_subscription_key' => 'sub-key-01',
            'momo_target_environment' => MTNMoMoService::TARGET_SANDBOX,
        ]);
    }

    protected function fakeMomoApi(array $statusBody = null)
    {
        Http::fake([
            'sandbox.mtn.test/collection/token/*' => Http::response(['access_token' => 'token-abc'], 200),
            'sandbox.mtn.test/collection/v1_0/requesttopay/*' => Http::response($statusBody ?? [
                'status' => 'PENDING',
                'amount' => '900.0',
                'currency' => 'GHS',
            ], 200),
            'sandbox.mtn.test/collection/v1_0/requesttopay' => Http::response([], 202),
        ]);
    }

    protected function reviewFrom()
    {
        return route('booking.confirmation', $this->booking->booking_ref);
    }

    public function test_pay_with_momo_marks_booking_pending_and_stores_reference()
    {
        $this->enableHostelMomo();
        $this->fakeMomoApi();

        $response = $this->actingAs($this->student)
            ->post(route('booking.pay.momo', $this->booking), ['phone' => '0240001234'])
            ->assertRedirect($this->reviewFrom());

        $booking = $this->booking->fresh();
        $this->assertEquals(Booking::GATEWAY_MTN_MOMO, $booking->gateway);
        $this->assertEquals(Booking::PAYMENT_PENDING, $booking->payment_status);
        $this->assertSame('0240001234', $booking->momo_phone);
        $this->assertNotEmpty($booking->momo_request_reference);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://sandbox.mtn.test/collection/v1_0/requesttopay'
                && $request['externalId'] === 'HSD-MOMO001'
                && $request['payer']['partyId'] === '233240001234'
                && (float) $request['amount'] === 900.0
                && $request['currency'] === 'GHS';
        });
    }

    public function test_pay_with_momo_marks_failed_when_request_is_rejected()
    {
        $this->enableHostelMomo();

        Http::fake([
            'sandbox.mtn.test/collection/token/*' => Http::response(['access_token' => 'token-abc'], 200),
            'sandbox.mtn.test/collection/v1_0/requesttopay' => Http::response(['message' => 'Invalid payer'], 400),
        ]);

        $this->actingAs($this->student)
            ->from($this->reviewFrom())
            ->post(route('booking.pay.momo', $this->booking), ['phone' => '0240001234'])
            ->assertRedirect($this->reviewFrom())
            ->assertSessionHasErrors('payment');

        $this->assertEquals(Booking::PAYMENT_FAILED, $this->booking->fresh()->payment_status);
    }

    public function test_pay_with_momo_requires_valid_ghanaian_phone()
    {
        $this->enableHostelMomo();
        $this->fakeMomoApi();

        $this->actingAs($this->student)
            ->from($this->reviewFrom())
            ->post(route('booking.pay.momo', $this->booking), ['phone' => '12345'])
            ->assertSessionHasErrors('phone');

        $this->assertEquals(Booking::PAYMENT_UNPAID, $this->booking->fresh()->payment_status);
    }

    public function test_pay_with_momo_is_unavailable_without_api_credentials()
    {
        $this->actingAs($this->student)
            ->post(route('booking.pay.momo', $this->booking), ['phone' => '0240001234'])
            ->assertNotFound();
    }

    public function test_pay_with_momo_is_forbidden_for_another_user()
    {
        $this->enableHostelMomo();
        $this->fakeMomoApi();

        $other = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($other)
            ->post(route('booking.pay.momo', $this->booking), ['phone' => '0240001234'])
            ->assertForbidden();
    }

    public function test_status_confirms_booking_when_momo_reports_success()
    {
        $this->enableHostelMomo();
        $this->booking->markMomoPending('uuid-success', '0240001234');

        $this->fakeMomoApi([
            'status' => 'SUCCESSFUL',
            'financialTransactionId' => 'TXN-7331',
            'amount' => '900.0',
            'currency' => 'GHS',
        ]);

        $this->actingAs($this->student)
            ->post(route('booking.pay.momo.status', $this->booking))
            ->assertRedirect($this->reviewFrom());

        $this->assertTrue($this->booking->fresh()->isPaid());
        $this->assertEquals('confirmed', $this->booking->fresh()->status);
        $this->assertSame('TXN-7331', $this->booking->fresh()->provider_reference);
        $this->assertSame('MTN Mobile Money', $this->booking->fresh()->gatewayLabel());
    }

    public function test_status_marks_failed_when_momo_reports_failed()
    {
        $this->enableHostelMomo();
        $this->booking->markMomoPending('uuid-failed', '0240001234');

        $this->fakeMomoApi([
            'status' => 'FAILED',
            'amount' => '900.0',
            'currency' => 'GHS',
        ]);

        $this->actingAs($this->student)
            ->from($this->reviewFrom())
            ->post(route('booking.pay.momo.status', $this->booking))
            ->assertRedirect($this->reviewFrom())
            ->assertSessionHasErrors('payment');

        $this->assertEquals(Booking::PAYMENT_FAILED, $this->booking->fresh()->payment_status);
    }

    public function test_status_keeps_pending_while_momo_is_still_pending()
    {
        $this->enableHostelMomo();
        $this->booking->markMomoPending('uuid-pending', '0240001234');

        $this->fakeMomoApi([
            'status' => 'PENDING',
            'amount' => '900.0',
            'currency' => 'GHS',
        ]);

        $this->actingAs($this->student)
            ->from($this->reviewFrom())
            ->post(route('booking.pay.momo.status', $this->booking))
            ->assertRedirect($this->reviewFrom())
            ->assertSessionHas('info');

        $this->assertEquals(Booking::PAYMENT_PENDING, $this->booking->fresh()->payment_status);
    }

    public function test_callback_marks_booking_paid_for_valid_amount()
    {
        $this->enableHostelMomo();
        $this->booking->markMomoPending('uuid-callback', '0240001234');

        $this->postJson('/api/momo/callback', [
            'status' => 'SUCCESSFUL',
            'externalId' => 'HSD-MOMO001',
            'amount' => '900',
            'currency' => 'GHS',
            'financialTransactionId' => 'TXN-9000',
        ])->assertOk()->assertJson(['ok' => true]);

        $booking = $this->booking->fresh();
        $this->assertTrue($booking->isPaid());
        $this->assertEquals('confirmed', $booking->status);
        $this->assertSame('TXN-9000', $booking->provider_reference);
    }

    public function test_callback_rejects_amount_mismatch()
    {
        $this->enableHostelMomo();
        $this->booking->markMomoPending('uuid-mismatch', '0240001234');

        $this->postJson('/api/momo/callback', [
            'status' => 'SUCCESSFUL',
            'externalId' => 'HSD-MOMO001',
            'amount' => '899',
            'currency' => 'GHS',
            'financialTransactionId' => 'TXN-9001',
        ])->assertStatus(422);

        $this->assertFalse($this->booking->fresh()->isPaid());
    }

    public function test_callback_marks_failed_when_status_failed()
    {
        $this->enableHostelMomo();
        $this->booking->markMomoPending('uuid-cbfail', '0240001234');

        $this->postJson('/api/momo/callback', [
            'status' => 'FAILED',
            'externalId' => 'HSD-MOMO001',
            'amount' => '900',
            'currency' => 'GHS',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertEquals(Booking::PAYMENT_FAILED, $this->booking->fresh()->payment_status);
    }

    public function test_callback_for_unknown_booking_returns_404()
    {
        $this->postJson('/api/momo/callback', [
            'status' => 'SUCCESSFUL',
            'externalId' => 'HSD-UNKNOWN',
            'amount' => '900',
            'currency' => 'GHS',
        ])->assertNotFound();
    }
}