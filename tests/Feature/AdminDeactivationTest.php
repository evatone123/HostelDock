<?php

namespace Tests\Feature;

use App\Models\BookingToken;
use App\Models\Campus;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeactivationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    protected function makeHostel(array $overrides = []): Hostel
    {
        return Hostel::create(array_merge([
            'name' => 'Deactivation Test Hostel',
            'slug' => 'deactivation-test-hostel',
            'location' => 'Tarkwa',
            'is_active' => true,
        ], $overrides));
    }

    protected function makeRoom(Hostel $hostel, array $overrides = []): Room
    {
        return Room::create(array_merge([
            'hostel_id' => $hostel->id,
            'name' => 'Room 1',
            'room_type' => 'One in a room',
            'gender_category' => 'Unisex',
            'price' => 1500,
            'total_rooms' => 2,
        ], $overrides));
    }

    public function test_admin_can_deactivate_and_reactivate_hostel()
    {
        $hostel = $this->makeHostel();

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.toggle', $hostel))
            ->assertRedirect();

        $this->assertFalse($hostel->fresh()->is_active);

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.toggle', $hostel))
            ->assertRedirect();

        $this->assertTrue($hostel->fresh()->is_active);
    }

    public function test_admin_can_deactivate_campus_and_it_vanishes_from_institutions()
    {
        $campus = Campus::create(['name' => 'University of Cape Coast', 'slug' => 'university-of-cape-coast']);

        $this->actingAs($this->admin)
            ->post(route('admin.campuses.toggle', $campus))
            ->assertRedirect();

        $this->assertFalse($campus->fresh()->is_active);
        $this->assertArrayNotHasKey('university-of-cape-coast', Hostel::institutions());

        $this->actingAs($this->admin)
            ->post(route('admin.campuses.toggle', $campus))
            ->assertRedirect();

        $this->assertTrue($campus->fresh()->is_active);
        $this->assertArrayHasKey('university-of-cape-coast', Hostel::institutions());
    }

    public function test_admin_can_toggle_room()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.rooms.toggle', [$hostel, $room]))
            ->assertRedirect();

        $this->assertFalse($room->fresh()->is_active);

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.rooms.toggle', [$hostel, $room]))
            ->assertRedirect();

        $this->assertTrue($room->fresh()->is_active);
    }

    public function test_manager_can_toggle_room_for_managed_hostel()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $manager->managedHostels()->attach($hostel);
        $room = $this->makeRoom($hostel);

        $this->actingAs($manager)
            ->post(route('manager.hostels.rooms.toggle', [$hostel, $room]))
            ->assertRedirect();

        $this->assertFalse($room->fresh()->is_active);
    }

    public function test_manager_cannot_toggle_room_for_unmanaged_hostel()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->actingAs($manager)
            ->post(route('manager.hostels.rooms.toggle', [$hostel, $room]))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_toggle_routes()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);

        $this->post(route('admin.hostels.rooms.toggle', [$hostel, $room]))->assertRedirect('/login');
        $this->post(route('admin.hostels.toggle', $hostel))->assertRedirect('/login');
    }

    public function test_inactive_room_is_hidden_from_hostel_detail_page()
    {
        $hostel = $this->makeHostel();
        $active = $this->makeRoom($hostel, ['name' => 'Visible Room']);
        $inactive = $this->makeRoom($hostel, ['name' => 'Hidden Room', 'is_active' => false]);

        $this->get(route('hostel.detail', $hostel))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($inactive->name);
    }

    public function test_inactive_room_is_not_bookable()
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, ['is_active' => false]);

        BookingToken::create([
            'token_ref' => BookingToken::generateReference(),
            'user_id' => $student->id,
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_ACTIVE,
            'gateway' => 'paystack',
            'paystack_reference' => 'PSK-REF-INACTIVE-1',
        ]);

        $this->actingAs($student)
            ->post(route('booking.store', $hostel), [
                'room' => $room->id,
                'student_name' => 'Inactive Room Student',
                'email' => 'inactive@student.test',
                'phone' => '0240000000',
                'institution' => 'Test University',
            ])
            ->assertNotFound();
    }

    public function test_deactivated_room_is_excluded_from_available_counts()
    {
        $hostel = $this->makeHostel();
        $this->makeRoom($hostel, ['total_rooms' => 2, 'is_active' => false]);
        $this->makeRoom($hostel, ['total_rooms' => 3, 'is_active' => true]);

        $this->assertSame(3, $hostel->availableRooms());
    }
}
