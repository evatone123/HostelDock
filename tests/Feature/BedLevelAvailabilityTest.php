<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BedLevelAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected $hostel;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hostel = Hostel::create([
            'name' => 'Bed Hostel',
            'slug' => 'bed-hostel',
            'location' => 'Tarkwa',
            'is_active' => true,
        ]);

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $this->grantBookingToken();
    }

    protected function makeRoom(array $overrides = []): Room
    {
        return Room::create(array_merge([
            'hostel_id' => $this->hostel->id,
            'name' => 'Room 1',
            'room_type' => 'Two in a room',
            'gender_category' => 'Unisex',
            'price' => 900,
            'total_rooms' => 3,
            'is_active' => true,
        ], $overrides));
    }

    protected function makeBooking(Room $room, array $overrides = []): Booking
    {
        static $seq = 0;
        $seq++;

        return Booking::create(array_merge([
            'booking_ref' => 'SRB-BED' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT),
            'user_id' => $this->student->id,
            'hostel_id' => $room->hostel_id,
            'room_id' => $room->id,
            'student_name' => $this->student->name,
            'email' => $this->student->email,
            'phone' => '0240000000',
            'room_type' => $room->room_type,
            'amount' => $room->price,
            'status' => 'confirmed',
            'payment_status' => Booking::PAYMENT_PAID,
            'expires_at' => Carbon::now()->addDays(300),
        ], $overrides));
    }

    /**
     * The booking routes sit behind the booking.token middleware, so the
     * student needs an active token before they can post a booking.
     */
    protected function grantBookingToken(): BookingToken
    {
        return BookingToken::firstOrCreate(
            ['user_id' => $this->student->id, 'paystack_reference' => 'PSK-BED-0001'],
            [
                'token_ref' => BookingToken::generateReference(),
                'amount' => BookingToken::PRICE_GHS,
                'status' => BookingToken::STATUS_ACTIVE,
                'gateway' => 'paystack',
                'paid_at' => Carbon::now(),
            ]
        );
    }

    public function test_capacity_is_derived_from_the_room_category()
    {
        $room = $this->makeRoom();

        $this->assertSame(2, $room->capacity);
        $this->assertSame(6, $room->totalBeds());
    }

    public function test_single_occupancy_room_type_has_one_bed_per_unit()
    {
        $room = $this->makeRoom(['room_type' => 'One in a room', 'total_rooms' => 3]);

        $this->assertSame(1, $room->capacity);
        $this->assertSame(3, $room->totalBeds());
    }

    public function test_each_booking_consumes_exactly_one_bed()
    {
        $room = $this->makeRoom();

        $this->assertSame(6, $room->availableSpace());

        $this->makeBooking($room);
        $this->assertSame(5, $room->fresh()->availableSpace());

        $this->makeBooking($room);
        $this->assertSame(4, $room->fresh()->availableSpace());
    }

    public function test_pending_bookings_hold_a_bed()
    {
        $room = $this->makeRoom();

        $this->makeBooking($room, ['status' => 'pending']);

        $this->assertSame(1, $room->fresh()->occupiedSpace());
        $this->assertSame(5, $room->fresh()->availableSpace());
    }

    public function test_cancelled_and_failed_bookings_release_their_bed()
    {
        $room = $this->makeRoom();

        $this->makeBooking($room);
        $this->makeBooking($room, ['status' => 'cancelled']);
        $this->makeBooking($room, ['status' => 'failed']);

        $this->assertSame(1, $room->fresh()->occupiedSpace());
        $this->assertSame(5, $room->fresh()->availableSpace());
    }

    public function test_expired_occupancy_frees_the_bed()
    {
        $room = $this->makeRoom();

        $this->makeBooking($room, ['expires_at' => Carbon::now()->subMinute()]);

        $this->assertSame(0, $room->fresh()->occupiedSpace());
        $this->assertSame(6, $room->fresh()->availableSpace());
    }

    public function test_occupancy_just_inside_window_still_holds_the_bed()
    {
        $room = $this->makeRoom();

        $this->makeBooking($room, ['expires_at' => Carbon::now()->addHour()]);

        $this->assertSame(1, $room->fresh()->occupiedSpace());
        $this->assertSame(5, $room->fresh()->availableSpace());
    }

    public function test_available_space_never_goes_negative()
    {
        $room = $this->makeRoom(['total_rooms' => 1]);

        for ($i = 0; $i < 4; $i++) {
            $this->makeBooking($room);
        }

        $this->assertSame(0, $room->fresh()->availableSpace());
    }

    public function test_has_availability_scope_agrees_with_available_space()
    {
        $room = $this->makeRoom();

        $this->assertTrue(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));

        for ($i = 0; $i < 6; $i++) {
            $this->makeBooking($room);
        }

        $this->assertSame(0, $room->fresh()->availableSpace());
        $this->assertFalse(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));
    }

    public function test_has_availability_scope_agrees_for_single_bed_rooms()
    {
        $room = $this->makeRoom(['room_type' => 'One in a room', 'total_rooms' => 1]);

        $this->assertTrue(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));

        $this->makeBooking($room);

        $this->assertSame(0, $room->fresh()->availableSpace());
        $this->assertFalse(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));
    }

    public function test_expired_booking_restores_has_availability()
    {
        $room = $this->makeRoom(['room_type' => 'One in a room', 'total_rooms' => 1]);

        $booking = $this->makeBooking($room, ['expires_at' => Carbon::now()->addHour()]);
        $this->assertFalse(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));

        $booking->update(['expires_at' => Carbon::now()->subMinute()]);

        $this->assertTrue(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));
    }

    public function test_pending_booking_holds_a_bed_even_when_expiry_has_passed()
    {
        $room = $this->makeRoom(['room_type' => 'One in a room', 'total_rooms' => 1]);

        $this->makeBooking($room, [
            'status' => 'pending',
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        $this->assertFalse(Room::hasAvailability()->pluck('rooms.id')->contains($room->id));
    }

    public function test_booking_is_rejected_once_every_bed_is_taken()
    {
        $room = $this->makeRoom(['room_type' => 'One in a room', 'total_rooms' => 1]);

        $this->makeBooking($room);

        $this->actingAs($this->student)
            ->post(route('booking.store', $this->hostel), [
                'room' => $room->id,
                'student_name' => 'Late Student',
                'email' => 'late@student.test',
                'phone' => '0240000000',
                'institution' => 'Late University',
                'gender' => 'Female',
                'academic_year' => '2026/2027',
            ])
            ->assertSessionHasErrors('room');
    }

    public function test_booking_succeeds_while_a_bed_remains()
    {
        $room = $this->makeRoom(['room_type' => 'One in a room', 'total_rooms' => 2]);

        $this->makeBooking($room);

        $this->assertSame(1, $room->fresh()->availableSpace());

        $this->actingAs($this->student)
            ->post(route('booking.store', $this->hostel), [
                'room' => $room->id,
                'student_name' => 'Second Student',
                'email' => 'second@student.test',
                'phone' => '0240000000',
                'institution' => 'Second University',
                'gender' => 'Female',
                'academic_year' => '2026/2027',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, $room->fresh()->availableSpace());
    }

    public function test_booking_page_offers_every_category_the_hostel_has()
    {
        $this->makeRoom(['name' => 'Room 1', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 3', 'room_type' => 'Three in a room']);

        $this->actingAs($this->student)
            ->get(route('booking.create', $this->hostel))
            ->assertOk()
            ->assertSee('Two in a room')
            ->assertSee('Three in a room')
            ->assertSee('All room categories');
    }

    public function test_booking_page_lists_each_individual_room_so_one_can_be_picked()
    {
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 6', 'room_type' => 'Two in a room']);

        $response = $this->actingAs($this->student)
            ->get(route('booking.create', $this->hostel))
            ->assertOk();

        $response->assertSee('Room 2');
        $response->assertSee('Room 6');
    }

    public function test_selected_category_is_preselected_in_the_filter()
    {
        $this->makeRoom(['name' => 'Room 1', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Three in a room']);

        $this->actingAs($this->student)
            ->get(route('booking.create', [$this->hostel, 'room_type' => 'Three in a room']))
            ->assertOk()
            ->assertSee('value="Three in a room" selected', false);
    }

    public function test_unknown_category_falls_back_to_showing_everything()
    {
        $this->makeRoom(['name' => 'Room 1', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Three in a room']);

        $response = $this->actingAs($this->student)
            ->get(route('booking.create', [$this->hostel, 'room_type' => 'Ninety in a room']))
            ->assertOk();

        // Falls back rather than hiding every room behind a category that is not there.
        $response->assertDontSee('value="Ninety in a room" selected', false);
        $response->assertSee('Room 1');
        $response->assertSee('Room 2');
    }

    public function test_preselected_room_activates_its_own_category()
    {
        $first = $this->makeRoom(['name' => 'Room 1', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Three in a room']);

        $this->actingAs($this->student)
            ->get(route('booking.create', [$this->hostel, 'room' => $first->id]))
            ->assertOk()
            ->assertSee('value="Two in a room" selected', false);
    }

    public function test_sold_out_room_is_not_chosen_as_the_preselection()
    {
        $full = $this->makeRoom(['name' => 'Room 1', 'room_type' => 'One in a room', 'total_rooms' => 1]);
        $this->makeBooking($full);
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Two in a room']);

        $this->actingAs($this->student)
            ->get(route('booking.create', [$this->hostel, 'room' => $full->id]))
            ->assertOk()
            ->assertSee('Sold out')
            ->assertDontSee('value="One in a room" selected', false);
    }

    public function test_room_from_another_hostel_cannot_be_preselected()
    {
        $other = Hostel::create([
            'name' => 'Other Hostel',
            'slug' => 'other-hostel',
            'location' => 'Cape Coast',
            'is_active' => true,
        ]);
        $foreign = $this->makeRoom(['hostel_id' => $other->id, 'name' => 'Room 9', 'room_type' => 'One in a room']);
        $room = $this->makeRoom(['name' => 'Room 1', 'room_type' => 'Two in a room']);

        $this->actingAs($this->student)
            ->get(route('booking.create', [$this->hostel, 'room' => $foreign->id]))
            ->assertOk()
            ->assertDontSee('value="' . $foreign->id . '"', false)
            ->assertSee('value="' . $room->id . '"', false);
    }

    public function test_available_rooms_column_is_no_longer_written()
    {
        $room = $this->makeRoom();

        $before = $room->fresh()->available_rooms;

        $this->makeBooking($room);
        $this->makeBooking($room);

        $this->assertSame($before, $room->fresh()->available_rooms);
        $this->assertSame(4, $room->fresh()->availableSpace());
    }
}
