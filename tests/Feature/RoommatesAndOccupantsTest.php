<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoommatesAndOccupantsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function makeHostel(array $overrides = []): Hostel
    {
        return Hostel::create(array_merge([
            'name' => 'Roommate Hostel',
            'slug' => 'roommate-hostel',
            'location' => 'Tarkwa',
            'institution' => 'Tarkwa Test University',
            'institution_name' => 'Tarkwa Test University, Tarkwa',
            'distance' => 'Near campus',
            'rating' => 4.2,
            'description' => 'Hostel used by the roommates feature test.',
            'starting_price' => 1500,
            'is_active' => true,
        ], $overrides));
    }

    protected function makeRoom(Hostel $hostel): Room
    {
        return Room::create([
            'hostel_id' => $hostel->id,
            'name' => 'Room 1',
            'room_type' => 'Four in a room',
            'gender_category' => 'Unisex',
            'price' => 1500,
            'total_rooms' => 4,
            'is_active' => true,
        ]);
    }

    protected function makeBooking(Hostel $hostel, Room $room, User $user, array $overrides = []): Booking
    {
        static $seq = 0;
        $seq++;

        return Booking::create(array_merge([
            'booking_ref' => 'HSD-RM-' . $seq,
            'user_id' => $user->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'student_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?: '0240000000',
            'institution' => $user->name,
            'gender' => null,
            'room_type' => $room->room_type,
            'academic_year' => '2026/2027',
            'amount' => $room->price,
            'status' => 'confirmed',
        ], $overrides));
    }

    protected function makeStudent(string $name, string $phone, string $email): User
    {
        static $seq = 0;
        $seq++;

        return User::factory()->create([
            'name' => $name,
            'phone' => $phone,
            'email' => $seq . '-' . $email,
            'role' => User::ROLE_STUDENT,
        ]);
    }

    public function test_student_sees_confirmed_roommates_on_booking_confirmation()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $owner = $this->makeStudent('Owner Student', '0240000000', 'owner@example.com');
        $mate = $this->makeStudent('Ama Asante', '0240000001', 'ama@example.com');
        $pending = $this->makeStudent('Kofi Pending', '0240000002', 'kofi@example.com');
        $cancelled = $this->makeStudent('Aba Cancelled', '0240000003', 'aba@example.com');

        $ownerBooking = $this->makeBooking($hostel, $room, $owner);
        $this->makeBooking($hostel, $room, $mate);
        $this->makeBooking($hostel, $room, $pending, ['status' => 'pending']);
        $this->makeBooking($hostel, $room, $cancelled, ['status' => 'cancelled']);

        $this->get(route('booking.confirmation', $ownerBooking->booking_ref))
            ->assertOk()
            ->assertSee('Your Roommates')
            ->assertSee('Ama Asante')
            ->assertSee('0240000001')
            ->assertSee('ama@example.com')
            ->assertDontSee('Kofi Pending')
            ->assertDontSee('Aba Cancelled');
    }

    public function test_student_does_not_see_roommates_from_other_rooms_or_hostels()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);
        $otherRoom = $this->makeRoom($hostel);
        $otherHostel = $this->makeHostel(['name' => 'Other Hostel', 'slug' => 'other-hostel']);

        $owner = $this->makeStudent('Owner Student', '0240000000', 'owner@example.com');
        $otherRoomMate = $this->makeStudent('Zara Other Room', '0240000009', 'zara@example.com');
        $otherHostelMate = $this->makeStudent('Ben Other Hostel', '0240000010', 'ben@example.com');

        $ownerBooking = $this->makeBooking($hostel, $room, $owner);
        $this->makeBooking($hostel, $otherRoom, $otherRoomMate);

        $hostelB = $otherHostel;
        $roomB = $this->makeRoom($hostelB);
        $this->makeBooking($hostelB, $roomB, $otherHostelMate);

        $this->get(route('booking.confirmation', $ownerBooking->booking_ref))
            ->assertOk()
            ->assertDontSee('Zara Other Room')
            ->assertDontSee('Ben Other Hostel');
    }

    public function test_student_sees_roommates_on_my_bookings_page()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $owner = $this->makeStudent('Owner Student', '0240000000', 'owner@example.com');
        $mate = $this->makeStudent('Efua Roommate', '0240000005', 'efua@example.com');
        $cancelled = $this->makeStudent('Dan Cancelled', '0240000006', 'dan@example.com');

        $this->makeBooking($hostel, $room, $owner);
        $this->makeBooking($hostel, $room, $mate);
        $this->makeBooking($hostel, $room, $cancelled, ['status' => 'cancelled']);

        $this->actingAs($owner)
            ->get(route('my-bookings'))
            ->assertOk()
            ->assertSee('Roommates (1)')
            ->assertSee('Efua Roommate')
            ->assertSee('0240000005')
            ->assertDontSee('Dan Cancelled');
    }

    public function test_my_bookings_renders_booking_whose_hostel_was_deleted()
    {
        $student = $this->makeStudent('Orphaned Student', '0240000011', 'orphan@example.com');
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->makeBooking($hostel, $room, $student, [
            'booking_ref' => 'HSD-ORPHAN-1',
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        Schema::disableForeignKeyConstraints();
        $hostel->delete();
        Schema::enableForeignKeyConstraints();

        $this->actingAs($student)
            ->get(route('my-bookings'))
            ->assertOk()
            ->assertSee('Hostel no longer available')
            ->assertSee('HSD-ORPHAN-1')
            ->assertSee('Cancel');
    }

    public function test_booking_form_shows_occupants_for_selected_room()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $student = $this->makeStudent('New Student', '0240000000', 'new@example.com');
        BookingToken::factory()->active()->create(['user_id' => $student->id]);
        $occupant = $this->makeStudent('Kwame Aureal', '0240000007', 'kwame@example.com');
        $this->makeBooking($hostel, $room, $occupant);

        $this->actingAs($student)
            ->get(route('booking.create', ['hostel' => $hostel->slug, 'room' => $room->id]))
            ->assertOk()
            ->assertSee('Already in this room')
            ->assertSee('Kwame Aureal')
            ->assertSee('0240000007');
    }

    public function test_manager_sees_pending_and_confirmed_occupants_on_room_index()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $hostel = $this->makeHostel();
        $manager->managedHostels()->attach($hostel);
        $room = $this->makeRoom($hostel);

        $confirmed = $this->makeStudent('Grace Confirmed', '0240000011', 'grace@example.com');
        $pending = $this->makeStudent('Yaw Pending', '0240000012', 'yaw@example.com');

        $this->makeBooking($hostel, $room, $confirmed);
        $this->makeBooking($hostel, $room, $pending, ['status' => 'pending']);
        $this->makeBooking($hostel, $room, $this->makeStudent('Sena Cancelled', '0240000013', 'sena@example.com'), ['status' => 'cancelled']);

        $this->actingAs($manager)
            ->get(route('manager.hostels.rooms.index', $hostel))
            ->assertOk()
            ->assertSee('Occupants')
            ->assertSee('Grace Confirmed')
            ->assertSee('0240000011')
            ->assertSee('Yaw Pending')
            ->assertSee('0240000012')
            ->assertDontSee('Sena Cancelled');
    }

    public function test_manager_cannot_view_rooms_of_unmanaged_hostel()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $this->makeRoom($hostel);

        $this->actingAs($manager)
            ->get(route('manager.hostels.rooms.index', $hostel))
            ->assertStatus(403);
    }

    public function test_admin_sees_occupants_on_room_index()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $occupant = $this->makeStudent('Nana Occupant', '0240000014', 'nana@example.com');
        $this->makeBooking($hostel, $room, $occupant);

        $this->actingAs($admin)
            ->get(route('admin.hostels.rooms.index', $hostel))
            ->assertOk()
            ->assertSee('Occupants')
            ->assertSee('Nana Occupant')
            ->assertSee('0240000014');
    }
}
