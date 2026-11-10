<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class QuickBookingTest extends TestCase
{
    use RefreshDatabase;

    protected $hostel;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hostel = Hostel::create([
            'name' => 'Quick Hostel',
            'slug' => 'quick-hostel',
            'location' => 'Tarkwa',
            'is_active' => true,
        ]);

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);
    }

    protected function makeRoom(array $overrides = []): Room
    {
        static $seq = 0;
        $seq++;

        return Room::create(array_merge([
            'hostel_id' => $this->hostel->id,
            'name' => 'Room ' . $seq,
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
            'booking_ref' => 'SRB-QB' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT),
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

    protected function quickBookingHtml(): string
    {
        $html = $this->get(route('hostel.detail', $this->hostel))
            ->assertOk()
            ->getContent();

        // Scope every assertion to the card so unrelated page copy cannot
        // satisfy a search string by coincidence.
        $start = strpos($html, 'Quick Booking');
        $this->assertNotFalse($start, 'Quick Booking card was not rendered.');

        $end = strpos($html, '</form>', $start);

        return substr($html, $start, $end - $start);
    }

    public function test_quick_booking_offers_a_category_selector()
    {
        $this->makeRoom(['room_type' => 'Two in a room']);
        $this->makeRoom(['room_type' => 'Three in a room']);

        $html = $this->quickBookingHtml();

        $this->assertStringContainsString('name="room_type"', $html);
        $this->assertStringContainsString('All room categories', $html);
        $this->assertStringContainsString('value="Two in a room"', $html);
        $this->assertStringContainsString('value="Three in a room"', $html);
    }

    public function test_every_room_option_carries_its_category_for_filtering()
    {
        $this->makeRoom(['room_type' => 'Two in a room']);
        $this->makeRoom(['room_type' => 'Three in a room']);

        $this->assertStringContainsString('data-room-type="Two in a room"', $this->quickBookingHtml());
        $this->assertStringContainsString('data-room-type="Three in a room"', $this->quickBookingHtml());
    }

    public function test_room_option_names_the_specific_room()
    {
        $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Two in a room']);
        $this->makeRoom(['name' => 'Room 6', 'room_type' => 'Two in a room']);

        $html = $this->quickBookingHtml();

        // Without the name the two options are indistinguishable, which is the
        // whole point of letting someone choose a specific room.
        $this->assertStringContainsString('Room 2', $html);
        $this->assertStringContainsString('Room 6', $html);
    }

    public function test_available_room_shows_its_free_bed_count()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'total_rooms' => 3]);

        // 3 units x 2 beds = 6 beds, minus 1 confirmed occupant.
        $this->makeBooking($room);

        $this->assertStringContainsString('5 beds left', $this->quickBookingHtml());
    }

    public function test_pending_booking_reduces_the_shown_bed_count()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'total_rooms' => 3]);

        $this->makeBooking($room);
        $this->makeBooking($room, ['status' => 'pending', 'expires_at' => null]);

        $this->assertStringContainsString('4 beds left', $this->quickBookingHtml());
    }

    public function test_expired_booking_does_not_reduce_the_shown_bed_count()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'total_rooms' => 3]);

        $this->makeBooking($room, ['expires_at' => Carbon::now()->subMinute()]);

        $this->assertStringContainsString('6 beds left', $this->quickBookingHtml());
    }

    public function test_sold_out_room_cannot_be_selected()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'room_type' => 'One in a room', 'total_rooms' => 1]);
        $this->makeBooking($room);

        $html = $this->quickBookingHtml();

        $this->assertSame(0, $room->fresh()->availableSpace());
        $this->assertMatchesRegularExpression(
            '/<option value="' . $room->id . '"[^>]*disabled/s',
            $html,
            'A sold out room must not be selectable in Quick Booking.'
        );
    }

    public function test_sold_out_room_is_labelled_rather_than_shown_as_zero_beds()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'room_type' => 'One in a room', 'total_rooms' => 1]);
        $this->makeBooking($room);

        $html = $this->quickBookingHtml();

        $this->assertStringContainsString('Sold out', $html);
        $this->assertStringNotContainsString('0 beds left', $html);
    }

    public function test_a_room_with_one_bed_left_is_still_selectable()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'room_type' => 'One in a room', 'total_rooms' => 2]);
        $this->makeBooking($room);

        $html = $this->quickBookingHtml();

        $this->assertStringContainsString('1 bed left', $html);
        $this->assertDoesNotMatchRegularExpression(
            '/<option value="' . $room->id . '"[^>]*disabled/s',
            $html
        );
    }

    public function test_inactive_room_is_not_offered()
    {
        $room = $this->makeRoom(['name' => 'Hidden Room', 'is_active' => false]);

        $this->assertStringNotContainsString($room->name, $this->quickBookingHtml());
    }

    public function test_quick_booking_and_booking_page_agree_on_the_bed_count()
    {
        $room = $this->makeRoom(['name' => 'Room 2', 'room_type' => 'Two in a room', 'total_rooms' => 4]);
        $this->makeBooking($room);
        $this->makeBooking($room, ['status' => 'pending', 'expires_at' => null]);

        $expected = $room->fresh()->availableSpace();
        $this->assertSame(6, $expected);

        $this->assertStringContainsString($expected . ' beds left', $this->quickBookingHtml());

        // The two booking surfaces must never drift apart on the same room.
        \App\Models\BookingToken::create([
            'token_ref' => \App\Models\BookingToken::generateReference(),
            'user_id' => $this->student->id,
            'amount' => \App\Models\BookingToken::PRICE_GHS,
            'status' => \App\Models\BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-QB-0001',
            'paid_at' => Carbon::now(),
        ]);

        $this->actingAs($this->student)
            ->get(route('booking.create', $this->hostel))
            ->assertOk()
            ->assertSee($expected . ' beds left');
    }
}
