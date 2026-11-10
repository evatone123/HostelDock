<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAmenityAssignmentTest extends TestCase
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
            'name' => 'Amenity Assignment Hostel',
            'slug' => 'amenity-assignment-hostel',
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

    protected function makeAmenity(string $name = '24/7 Security'): Amenity
    {
        return Amenity::create([
            'name' => $name,
            'icon' => 'bi-shield-check',
            'is_active' => true,
        ]);
    }

    protected function roomPayload(Hostel $hostel, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Room 1',
            'room_type' => 'One in a room',
            'gender_category' => 'Unisex',
            'price' => 1500,
            'total_rooms' => 2,
            'is_active' => '1',
        ], $overrides);
    }

    public function test_admin_can_assign_amenities_when_creating_room()
    {
        $hostel = $this->makeHostel();
        $wifi = $this->makeAmenity('Wi-Fi');
        $security = $this->makeAmenity('24/7 Security');

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.rooms.store', $hostel), $this->roomPayload($hostel, [
                'amenities' => [$wifi->id, $security->id],
            ]))
            ->assertRedirect(route('admin.hostels.rooms.index', $hostel));

        $room = Room::where('hostel_id', $hostel->id)->firstOrFail();
        $this->assertCount(2, $room->amenities);
        $this->assertDatabaseHas('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $wifi->id,
        ]);
        $this->assertDatabaseHas('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $security->id,
        ]);
    }

    public function test_admin_can_change_amenities_when_updating_room()
    {
        $hostel = $this->makeHostel();
        $wifi = $this->makeAmenity('Wi-Fi');
        $security = $this->makeAmenity('24/7 Security');
        $room = $this->makeRoom($hostel);
        $room->amenities()->attach($wifi);

        $this->actingAs($this->admin)
            ->put(route('admin.hostels.rooms.update', [$hostel, $room]), $this->roomPayload($hostel, [
                'name' => 'Room 1',
                'amenities' => [$security->id],
            ]))
            ->assertRedirect(route('admin.hostels.rooms.index', $hostel));

        $this->assertCount(1, $room->fresh()->amenities);
        $this->assertDatabaseHas('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $security->id,
        ]);
        $this->assertDatabaseMissing('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $wifi->id,
        ]);
    }

    public function test_admin_room_create_form_shows_amenity_checkboxes()
    {
        $hostel = $this->makeHostel();
        $this->makeAmenity('Wi-Fi');

        $this->actingAs($this->admin)
            ->get(route('admin.hostels.rooms.create', $hostel))
            ->assertOk()
            ->assertSee('Wi-Fi')
            ->assertSee('name="amenities[]"', false);
    }

    public function test_manager_can_assign_amenities_for_managed_hostel()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $manager->managedHostels()->attach($hostel);
        $amenity = $this->makeAmenity('Ceiling Fan');

        $this->actingAs($manager)
            ->post(route('manager.hostels.rooms.store', $hostel), $this->roomPayload($hostel, [
                'amenities' => [$amenity->id],
            ]))
            ->assertRedirect(route('manager.hostels.rooms.index', $hostel));

        $room = Room::where('hostel_id', $hostel->id)->firstOrFail();
        $this->assertDatabaseHas('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $amenity->id,
        ]);
    }

    public function test_manager_can_change_amenities_when_updating_room()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $manager->managedHostels()->attach($hostel);
        $room = $this->makeRoom($hostel);
        $wifi = $this->makeAmenity('Wi-Fi');
        $tomato = $this->makeAmenity('Perpetual Care');

        $this->actingAs($manager)
            ->put(route('manager.hostels.rooms.update', [$hostel, $room]), $this->roomPayload($hostel, [
                'amenities' => [$tomato->id],
            ]))
            ->assertRedirect(route('manager.hostels.rooms.index', $hostel));

        $this->assertDatabaseHas('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $tomato->id,
        ]);
        $this->assertDatabaseMissing('room_amenity', [
            'room_id' => $room->id,
            'amenity_id' => $wifi->id,
        ]);
    }

    public function test_manager_room_form_shows_amenity_checkboxes()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $manager->managedHostels()->attach($hostel);
        $this->makeAmenity('Study Desk');

        $this->actingAs($manager)
            ->get(route('manager.hostels.rooms.create', $hostel))
            ->assertOk()
            ->assertSee('Study Desk')
            ->assertSee('name="amenities[]"', false);
    }

    public function test_manager_cannot_assign_amenities_for_unmanaged_hostel()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel = $this->makeHostel();
        $amenity = $this->makeAmenity();

        $this->actingAs($manager)
            ->post(route('manager.hostels.rooms.store', $hostel), $this->roomPayload($hostel, [
                'amenities' => [$amenity->id],
            ]))
            ->assertForbidden();

        $this->assertSame(0, Room::where('hostel_id', $hostel->id)->count());
    }

    public function test_unknown_amenity_id_is_rejected()
    {
        $hostel = $this->makeHostel();

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.rooms.store', $hostel), $this->roomPayload($hostel, [
                'amenities' => [999],
            ]))
            ->assertSessionHasErrors('amenities.0');

        $this->assertSame(0, Room::where('hostel_id', $hostel->id)->count());
    }

    public function test_room_without_amenities_has_none_assigned()
    {
        $hostel = $this->makeHostel();

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.rooms.store', $hostel), $this->roomPayload($hostel))
            ->assertRedirect(route('admin.hostels.rooms.index', $hostel));

        $room = Room::where('hostel_id', $hostel->id)->firstOrFail();
        $this->assertCount(0, $room->amenities);
    }

    public function test_hostel_detail_page_shows_room_amenity_chips()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);
        $this->makeAmenity('En-suite Bathroom');
        $room->amenities()->attach(Amenity::where('name', 'En-suite Bathroom')->first());

        $this->get(route('hostel.detail', $hostel))
            ->assertOk()
            ->assertSee('En-suite Bathroom');
    }

    public function test_hostel_detail_amenities_block_uses_database_records()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);
        $this->makeAmenity('Ceiling Fan');
        $room->amenities()->attach(Amenity::where('name', 'Ceiling Fan')->first());

        $this->get(route('hostel.detail', $hostel))
            ->assertOk()
            ->assertSee('What this hostel offers')
            ->assertSee('Ceiling Fan');
    }

    public function test_hostel_detail_amenities_block_falls_back_when_none_assigned()
    {
        $hostel = $this->makeHostel();
        $this->makeRoom($hostel);

        $this->get(route('hostel.detail', $hostel))
            ->assertOk()
            ->assertSee('What this hostel offers')
            ->assertSee('24/7 Security');
    }

    public function test_booking_page_shows_room_amenities()
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        BookingToken::factory()->active()->create(['user_id' => $student->id]);

        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel);
        $this->makeAmenity('Study Desk');
        $room->amenities()->attach(Amenity::where('name', 'Study Desk')->first());

        $this->actingAs($student)
            ->get(route('booking.create', $hostel))
            ->assertOk()
            ->assertSee('Study Desk');
    }
}
