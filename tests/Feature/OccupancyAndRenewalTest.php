<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OccupancyAndRenewalTest extends TestCase
{
    use RefreshDatabase;

    protected function ref(): string
    {
        do {
            $ref = 'HSD-TEST-' . strtoupper(Str::random(8));
        } while (Booking::where('booking_ref', $ref)->exists());

        return $ref;
    }

    protected function makeHostel(array $overrides = []): Hostel
    {
        return Hostel::create(array_merge([
            'name' => 'Loso Residency',
            'slug' => 'loso-residency',
            'location' => 'Takoradi',
            'is_active' => true,
        ], $overrides));
    }

    protected function makeRoom(Hostel $hostel, array $overrides = []): Room
    {
        return Room::create(array_merge([
            'hostel_id' => $hostel->id,
            'name' => 'Room A',
            'room_type' => 'Single',
            'gender_category' => 'Unisex',
            'price' => 1000,
            'total_rooms' => 3,
            'is_active' => true,
        ], $overrides));
    }

    protected function makeConfirmedBooking(User $user, Room $room, array $overrides = []): Booking
    {
        $booking = Booking::create(array_merge([
            'booking_ref' => $this->ref(),
            'user_id' => $user->id,
            'hostel_id' => $room->hostel_id,
            'room_id' => $room->id,
            'student_name' => $user->name,
            'email' => $user->email,
            'phone' => '0240000000',
            'room_type' => $room->room_type,
            'amount' => $room->price,
            'status' => 'confirmed',
            'payment_status' => Booking::PAYMENT_PAID,
            'gateway' => Booking::GATEWAY_MTN_MOMO,
            'provider_reference' => 'TX-0001',
            'paid_at' => Carbon::now(),
        ], $overrides));

        return $booking;
    }

    public function test_confirming_sets_a_365_day_occupancy_expiry()
    {
        $user = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $booking = Booking::create([
            'booking_ref' => $this->ref(),
            'user_id' => $user->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'student_name' => $user->name,
            'email' => $user->email,
            'phone' => '0240000000',
            'room_type' => $room->room_type,
            'amount' => $room->price,
            'status' => 'pending',
        ]);

        $booking->status = 'confirmed';
        $booking->payment_status = Booking::PAYMENT_PAID;
        $booking->save();
        $booking->ensureOccupancyExpiry();

        $this->assertNotNull($booking->expires_at);
        $this->assertTrue($booking->expires_at->gt(Carbon::now()->addDays(Booking::OCCUPANCY_DAYS - 1)));
        $this->assertTrue($booking->expires_at->lte(Carbon::now()->addDays(Booking::OCCUPANCY_DAYS)));
    }

    public function test_indefinite_paid_booking_still_occupies()
    {
        $user = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->makeConfirmedBooking($user, $room, ['expires_at' => null]);

        $this->assertSame(2, $room->fresh()->availableSpace());
        $this->assertSame(2, Room::withAvailability()->find($room->id)->availableSpace());
    }

    public function test_expired_occupancy_releases_space()
    {
        $user = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->makeConfirmedBooking($user, $room, [
            'expires_at' => Carbon::now()->subDay(),
        ]);

        $this->assertSame(3, $room->fresh()->availableSpace());
    }

    public function test_pending_bookings_hold_space()
    {
        $user = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        Booking::create([
            'booking_ref' => $this->ref(),
            'user_id' => $user->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'student_name' => $user->name,
            'email' => $user->email,
            'phone' => '0240000000',
            'room_type' => $room->room_type,
            'amount' => $room->price,
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $this->assertSame(2, $room->fresh()->availableSpace());
    }

    public function test_cancelled_and_failed_bookings_do_not_hold_space()
    {
        $user = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        Booking::create([
            'booking_ref' => $this->ref(),
            'user_id' => $user->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'student_name' => $user->name,
            'email' => $user->email,
            'phone' => '0240000000',
            'room_type' => $room->room_type,
            'amount' => $room->price,
            'status' => 'cancelled',
            'payment_status' => Booking::PAYMENT_FAILED,
        ]);

        $this->assertSame(3, $room->fresh()->availableSpace());

        $this->assertSame(3, $hostel->fresh()->availableRooms());
    }

    public function test_scope_has_availability_only_returns_rooms_with_free_units()
    {
        $user = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, ['total_rooms' => 1]);

        $free = Room::query()->hasAvailability()->pluck('id');
        $this->assertContains($room->id, $free);

        $this->makeConfirmedBooking($user, $room);

        $this->assertNotContains($room->id, Room::query()->hasAvailability()->pluck('id'));
    }

    public function test_store_blocks_booking_for_a_computed_full_room()
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, ['total_rooms' => 1]);

        $this->makeConfirmedBooking($student, $room);

        \App\Models\BookingToken::create([
            'token_ref' => \App\Models\BookingToken::generateReference(),
            'user_id' => $student->id,
            'amount' => \App\Models\BookingToken::PRICE_GHS,
            'status' => \App\Models\BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-FULL-1',
        ]);

        $this->actingAs($student)
            ->post(route('booking.store', $hostel), [
                'room' => $room->id,
                'student_name' => 'Second Student',
                'email' => 'second@student.test',
                'phone' => '0240000001',
                'institution' => 'Test University',
            ])
            ->assertSessionHasErrors('room');
    }

    public function test_lock_is_only_allowed_for_owner_within_window()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $farFuture = $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(Booking::OCCUPANCY_DAYS),
        ]);

        $this->actingAs($owner)
            ->post(route('booking.renewal.lock', $farFuture))
            ->assertStatus(409);

        $inWindow = $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        $this->actingAs($other)
            ->post(route('booking.renewal.lock', $inWindow))
            ->assertStatus(403);

        $this->actingAs($owner)
            ->post(route('booking.renewal.lock', $inWindow))
            ->assertRedirect();

        $this->assertTrue($inWindow->fresh()->isRenewalLocked());

        $this->actingAs($owner)
            ->post(route('booking.renewal.lock', $inWindow))
            ->assertSessionHas('info');
    }

    public function test_renewal_payment_extends_occupancy_and_clears_lock()
    {
        $owner = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, ['price' => 2500]);

        $booking = $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(20),
        ]);

        $booking->lockForRenewal();
        $booking->startRenewalPaystack('RENW-TEST-001');

        $booking->applyRenewal('TX-RENEW-1', Booking::GATEWAY_PAYSTACK);

        $this->assertSame(1, $booking->fresh()->renewal_count);
        $this->assertNull($booking->fresh()->renewal_locked_at);
        $this->assertSame('TX-RENEW-1', $booking->fresh()->renewal_paystack_transaction_ref);
        $this->assertTrue($booking->fresh()->expires_at->gt(Carbon::now()->addDays(Booking::OCCUPANCY_DAYS - 1)));

        $this->assertSame(1, $booking->fresh()->renewal_count);
        $this->assertSame(2500.0, $booking->fresh()->renewalPrice());
    }

    public function test_renewal_is_per_booking_independently()
    {
        $ownerA = User::factory()->create();
        $ownerB = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, ['total_rooms' => 2]);

        $bookingA = $this->makeConfirmedBooking($ownerA, $room, ['expires_at' => Carbon::now()->addDays(10)]);
        $bookingB = $this->makeConfirmedBooking($ownerB, $room, ['expires_at' => Carbon::now()->addDays(10)]);

        $bookingA->lockForRenewal();
        $bookingA->applyRenewal('TX-A', Booking::GATEWAY_PAYSTACK);

        $this->assertSame(1, $bookingA->fresh()->renewal_count);
        $this->assertSame(0, $bookingB->fresh()->renewal_count);
        $this->assertFalse($bookingB->fresh()->isRenewalLocked());
        $this->assertTrue($bookingB->fresh()->isWithinRenewalWindow());
    }

    public function test_momo_renewal_flow_marks_pending_and_clears_on_failure()
    {
        $owner = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $booking = $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(20),
        ]);

        $booking->lockForRenewal();
        $booking->startRenewalMomo('MOMO-REF-9', '0240000000');

        $this->assertTrue($booking->fresh()->isRenewalMomoPending());

        $booking->clearRenewalMomoPending();

        $this->assertFalse($booking->fresh()->isRenewalMomoPending());
        $this->assertNull($booking->fresh()->renewal_momo_reference);

        $booking->startRenewalMomo('MOMO-REF-10', '0240000000');
        $booking->applyRenewal('FIN-TX-1', Booking::GATEWAY_MTN_MOMO);

        $this->assertSame('FIN-TX-1', $booking->fresh()->renewal_momo_transaction_ref);
        $this->assertFalse($booking->fresh()->isRenewalMomoPending());
        $this->assertSame(1, $booking->fresh()->renewal_count);
    }

    public function test_my_bookings_page_shows_renewal_window_ui()
    {
        $owner = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $inWindow = $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(20),
        ]);

        $this->actingAs($owner)
            ->get(route('my-bookings'))
            ->assertOk()
            ->assertSee('Lock & Rebook')
            ->assertSee('renewal');

        $inWindow->lockForRenewal();

        $this->actingAs($owner)
            ->get(route('my-bookings'))
            ->assertOk()
            ->assertSee('locked for renewal');
    }

    public function test_reminder_command_stamps_notified_flag_only_once()
    {
        Mail::fake();

        $owner = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(20),
            'renewal_window_notified_at' => null,
        ]);

        $this->artisan('occupancy:renewal-reminders')
            ->assertExitCode(0);

        $booking = Booking::first();
        $this->assertNotNull($booking->renewal_window_notified_at);

        $this->artisan('occupancy:renewal-reminders')
            ->assertExitCode(0);

        $this->assertNotNull($booking->fresh()->renewal_window_notified_at);
    }

    public function test_reminder_command_skips_bookings_outside_window()
    {
        Mail::fake();

        $owner = User::factory()->create();
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->makeConfirmedBooking($owner, $room, [
            'expires_at' => Carbon::now()->addDays(Booking::OCCUPANCY_DAYS),
        ]);

        $this->artisan('occupancy:renewal-reminders')
            ->assertExitCode(0);

        $this->assertNull(Booking::first()->renewal_window_notified_at);
    }
}
