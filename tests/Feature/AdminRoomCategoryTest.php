<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoomCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    protected function makeHostel(): Hostel
    {
        return Hostel::create([
            'name' => 'Category Hostel',
            'slug' => 'category-hostel',
            'location' => 'Tarkwa',
            'is_active' => true,
        ]);
    }

    protected function makeRoom(Hostel $hostel, string $roomType = 'Two in a room', int $totalRooms = 3): Room
    {
        return Room::create([
            'hostel_id' => $hostel->id,
            'name' => 'Room 1',
            'room_type' => $roomType,
            'gender_category' => 'Unisex',
            'price' => 900,
            'total_rooms' => $totalRooms,
        ]);
    }

    public function test_default_categories_are_seeded_with_capacities()
    {
        $this->assertSame(6, RoomCategory::count());

        $this->assertSame(1, RoomCategory::where('name', 'One in a room')->value('capacity'));
        $this->assertSame(6, RoomCategory::where('name', 'Six in a room')->value('capacity'));
    }

    public function test_guest_is_redirected_from_room_categories_index()
    {
        $this->get(route('admin.room-categories.index'))->assertRedirect('/login');
    }

    public function test_manager_is_forbidden_from_room_categories()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($manager)
            ->get(route('admin.room-categories.index'))
            ->assertForbidden();
    }

    public function test_student_is_forbidden_from_creating_room_categories()
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($student)
            ->post(route('admin.room-categories.store'), [
                'name' => 'Seven in a room',
                'capacity' => 7,
            ])
            ->assertForbidden();
    }

    public function test_admin_can_view_room_categories_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.room-categories.index'))
            ->assertOk()
            ->assertSee('Two in a room');
    }

    public function test_admin_can_create_a_room_category()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.room-categories.store'), [
                'name' => 'Seven in a room',
                'capacity' => 7,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.room-categories.index'));

        $this->assertDatabaseHas('room_categories', [
            'name' => 'Seven in a room',
            'capacity' => 7,
            'is_active' => true,
        ]);
    }

    public function test_room_category_name_must_be_unique()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.room-categories.store'), [
                'name' => 'Two in a room',
                'capacity' => 2,
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_room_category_capacity_must_be_at_least_one()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.room-categories.store'), [
                'name' => 'Zero in a room',
                'capacity' => 0,
            ])
            ->assertSessionHasErrors('capacity');
    }

    public function test_admin_can_update_a_room_category()
    {
        $category = RoomCategory::where('name', 'Two in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.room-categories.update', $category), [
                'name' => 'Two in a room',
                'capacity' => 2,
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.room-categories.index'));

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_changing_capacity_propagates_to_rooms_using_it()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, 'Two in a room');

        $this->assertSame(2, $room->capacity);
        $this->assertSame(6, $room->totalBeds());

        $category = RoomCategory::where('name', 'Two in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.room-categories.update', $category), [
                'name' => 'Two in a room',
                'capacity' => 4,
            ])
            ->assertRedirect(route('admin.room-categories.index'));

        $this->assertSame(4, $room->fresh()->capacity);
        $this->assertSame(12, $room->fresh()->totalBeds());
    }

    public function test_capacity_cannot_be_reduced_below_existing_occupancy()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, 'Two in a room', 2);

        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        foreach (range(1, 3) as $i) {
            Booking::create([
                'booking_ref' => 'SRB-GUARD' . $i,
                'user_id' => $student->id,
                'hostel_id' => $hostel->id,
                'room_id' => $room->id,
                'student_name' => 'Guard Student ' . $i,
                'email' => 'guard' . $i . '@student.test',
                'phone' => '024000000' . $i,
                'institution' => 'Guard University',
                'gender' => 'Female',
                'room_type' => 'Two in a room',
                'academic_year' => '2026/2027',
                'amount' => 900,
                'status' => 'confirmed',
                'payment_status' => Booking::PAYMENT_PAID,
            ]);
        }

        $this->assertSame(4, $room->totalBeds());
        $this->assertSame(1, $room->availableSpace());

        $category = RoomCategory::where('name', 'Two in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.room-categories.update', $category), [
                'name' => 'Two in a room',
                'capacity' => 1,
            ])
            ->assertSessionHas('error');

        $this->assertSame(2, $category->fresh()->capacity);
        $this->assertSame(2, $room->fresh()->capacity);
        $this->assertSame(4, $room->fresh()->totalBeds());
    }

    public function test_capacity_can_be_reduced_when_no_occupants_would_be_stranded()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, 'Two in a room', 2);

        $this->assertSame(4, $room->totalBeds());

        $category = RoomCategory::where('name', 'Two in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.room-categories.update', $category), [
                'name' => 'Two in a room',
                'capacity' => 1,
            ])
            ->assertRedirect(route('admin.room-categories.index'));

        $this->assertSame(1, $category->fresh()->capacity);
        $this->assertSame(1, $room->fresh()->capacity);
        $this->assertSame(2, $room->fresh()->totalBeds());
    }

    public function test_room_category_used_by_rooms_cannot_be_deleted()
    {
        $hostel = $this->makeHostel();
        $this->makeRoom($hostel, 'Two in a room');

        $category = RoomCategory::where('name', 'Two in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('admin.room-categories.destroy', $category))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('room_categories', ['name' => 'Two in a room']);
    }

    public function test_unused_room_category_can_be_deleted()
    {
        $category = RoomCategory::where('name', 'Six in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('admin.room-categories.destroy', $category))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('room_categories', ['name' => 'Six in a room']);
    }

    public function test_admin_can_toggle_a_room_category()
    {
        $category = RoomCategory::where('name', 'Six in a room')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.room-categories.toggle', $category))
            ->assertSessionHas('success');

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_inactive_category_stays_selectable_when_editing_its_room()
    {
        $hostel = $this->makeHostel();
        $room = $this->makeRoom($hostel, 'Two in a room');

        $category = RoomCategory::where('name', 'Two in a room')->firstOrFail();
        $category->update(['is_active' => false]);

        $this->actingAs($this->admin)
            ->get(route('admin.hostels.rooms.edit', [$hostel, $room]))
            ->assertOk()
            ->assertSee('Two in a room (hidden)');
    }

    public function test_room_type_must_match_a_known_category()
    {
        $hostel = $this->makeHostel();

        $this->actingAs($this->admin)
            ->post(route('admin.hostels.rooms.store', $hostel), [
                'name' => 'Room 1',
                'room_type' => 'Forty in a room',
                'gender_category' => 'Unisex',
                'price' => 900,
                'total_rooms' => 2,
            ])
            ->assertSessionHasErrors('room_type');

        $this->assertSame(0, Room::where('hostel_id', $hostel->id)->count());
    }

    public function test_manager_can_apply_a_category_to_a_room()
    {
        $hostel = $this->makeHostel();
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $hostel->managers()->attach($manager);

        $this->actingAs($manager)
            ->post(route('manager.hostels.rooms.store', $hostel), [
                'name' => 'Room 1',
                'room_type' => 'Three in a room',
                'gender_category' => 'Unisex',
                'price' => 700,
                'total_rooms' => 4,
            ])
            ->assertRedirect(route('manager.hostels.rooms.index', $hostel));

        $room = Room::where('hostel_id', $hostel->id)->firstOrFail();

        $this->assertSame('Three in a room', $room->room_type);
        $this->assertSame(3, $room->capacity);
        $this->assertSame(12, $room->totalBeds());
    }
}
