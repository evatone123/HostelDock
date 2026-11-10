<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use App\Models\User;
use App\Services\MTNMoMoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentUIRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected $student;

    protected $hostel;

    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->hostel = Hostel::create([
            'name' => 'UI Test Hostel',
            'slug' => 'ui-test-hostel',
            'institution' => 'UI Test University',
            'institution_name' => 'UI Test University, Accra',
            'location' => 'Accra',
            'distance' => 'Near campus',
            'rating' => 4.5,
            'description' => 'Hostel used by the payment UI regression test.',
            'starting_price' => 900,
            'is_active' => true,
        ]);

        HostelPaymentDetail::create([
            'hostel_id' => $this->hostel->id,
            'is_active' => true,
            'paystack_enabled' => true,
            'paystack_public_key' => 'pk_test_ui',
            'paystack_secret_key' => 'sk_test_ui',
            'momo_api_enabled' => true,
            'momo_api_user' => 'api-user-ui',
            'momo_api_key' => 'api-key-ui',
            'momo_subscription_key' => 'sub-key-ui',
            'momo_target_environment' => MTNMoMoService::TARGET_SANDBOX,
        ]);

        $this->booking = Booking::create([
            'booking_ref' => 'HSD-UI001',
            'user_id' => $this->student->id,
            'hostel_id' => $this->hostel->id,
            'student_name' => 'UI Student',
            'email' => 'ui@student.test',
            'phone' => '0240000000',
            'institution' => 'UI Test University',
            'gender' => 'Male',
            'room_type' => 'Single Room',
            'academic_year' => '2026/2027',
            'amount' => 900,
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);
    }

    public function test_admin_dashboard_shows_per_hostel_gateway_card()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Hostel Payment Gateways')
            ->assertDontSee('admin/payments');
    }

    public function test_confirmation_page_offers_both_gateways_to_authenticated_user()
    {
        $this->actingAs($this->student)
            ->get(route('booking.confirmation', $this->booking->booking_ref))
            ->assertOk()
            ->assertSee('Pay Online')
            ->assertSee('with Paystack')
            ->assertSee('with MTN MoMo')
            ->assertSee('Your MoMo number');
    }

    public function test_confirmation_page_offers_login_to_guests()
    {
        $this->get(route('booking.confirmation', $this->booking->booking_ref))
            ->assertOk()
            ->assertSee('Pay Online')
            ->assertSee('Log in to pay');
    }

    public function test_confirmation_page_shows_pending_state_with_confirm_button()
    {
        $this->booking->markMomoPending('uuid-ui-pending', '0240001234');

        $this->actingAs($this->student)
            ->get(route('booking.confirmation', $this->booking->booking_ref))
            ->assertOk()
            ->assertSee('MTN MoMo Payment Pending')
            ->assertSee('Confirm Payment')
            ->assertSee('0240001234');
    }

    public function test_confirmation_page_shows_paid_state_with_gateway_reference()
    {
        $this->booking->markPaidVia(Booking::GATEWAY_MTN_MOMO, 'TXN-UI-9987');

        $this->actingAs($this->student)
            ->get(route('booking.confirmation', $this->booking->booking_ref))
            ->assertOk()
            ->assertSee('Payment Received')
            ->assertSee('MTN Mobile Money')
            ->assertSee('TXN-UI-9987');
    }

    public function test_invoice_page_shows_gateway_forms_to_authenticated_user()
    {
        $this->actingAs($this->student)
            ->get(route('booking.invoice', $this->booking->booking_ref))
            ->assertOk()
            ->assertSee('Pay online')
            ->assertSee('with Paystack')
            ->assertSee('with MTN MoMo');
    }

    public function test_invoice_page_hides_gateway_forms_from_guests()
    {
        $this->get(route('booking.invoice', $this->booking->booking_ref))
            ->assertOk()
            ->assertSee('Log in to pay online')
            ->assertDontSee('with Paystack');
    }

    public function test_my_bookings_links_pending_payment_to_confirmation_page()
    {
        $this->actingAs($this->student)
            ->get(route('my-bookings'))
            ->assertOk()
            ->assertSee('Pay Now')
            ->assertSee(route('booking.confirmation', $this->booking->booking_ref));
    }

    public function test_my_bookings_shows_pending_badge_and_confirm_button()
    {
        $this->booking->markMomoPending('uuid-ui-mb', '0240001234');

        $this->actingAs($this->student)
            ->get(route('my-bookings'))
            ->assertOk()
            ->assertSee('Payment Pending')
            ->assertSee('Confirm Payment');
    }

    public function test_legacy_global_gateway_routes_no_longer_exist()
    {
        $this->expectException(\Symfony\Component\Routing\Exception\RouteNotFoundException::class);

        route('admin.payments.index');
    }
}