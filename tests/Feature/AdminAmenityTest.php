<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAmenityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    protected function makeAmenity(array $overrides = []): Amenity
    {
        return Amenity::create(array_merge([
            'name' => '24/7 Security',
            'icon' => 'bi-shield-check',
            'is_active' => true,
        ], $overrides));
    }

    public function test_guest_is_redirected_from_amenities_index()
    {
        $this->get(route('admin.amenities.index'))->assertRedirect('/login');
    }

    public function test_non_admin_is_forbidden_from_amenities()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($manager)
            ->get(route('admin.amenities.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_amenities_index()
    {
        $this->makeAmenity();

        $this->actingAs($this->admin)
            ->get(route('admin.amenities.index'))
            ->assertOk()
            ->assertSee('24/7 Security');
    }

    public function test_admin_can_create_an_amenity()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.amenities.store'), [
                'name' => 'Air Conditioning',
                'icon' => 'bi-snow',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.amenities.index'));

        $this->assertDatabaseHas('amenities', [
            'name' => 'Air Conditioning',
            'icon' => 'bi-snow',
            'is_active' => true,
        ]);
    }

    public function test_invalid_icon_is_rejected()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.amenities.store'), [
                'name' => 'Bad Icon',
                'icon' => 'bi-nope-nothing',
            ])
            ->assertSessionHasErrors('icon');

        $this->assertDatabaseMissing('amenities', ['name' => 'Bad Icon']);
    }

    public function test_admin_can_update_an_amenity()
    {
        $amenity = $this->makeAmenity();

        $this->actingAs($this->admin)
            ->put(route('admin.amenities.update', $amenity), [
                'name' => 'Wi-Fi',
                'icon' => 'bi-wifi',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.amenities.index'));

        $this->assertDatabaseHas('amenities', [
            'id' => $amenity->id,
            'name' => 'Wi-Fi',
            'icon' => 'bi-wifi',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_an_unused_amenity()
    {
        $amenity = $this->makeAmenity();

        $this->actingAs($this->admin)
            ->delete(route('admin.amenities.destroy', $amenity))
            ->assertRedirect();

        $this->assertDatabaseMissing('amenities', ['id' => $amenity->id]);
    }

    public function test_amenity_used_by_rooms_cannot_be_deleted()
    {
        $amenity = $this->makeAmenity();

        Room::create([
            'hostel_id' => $this->makeHostel()->id,
            'name' => 'Room 1',
            'room_type' => 'One in a room',
            'gender_category' => 'Unisex',
            'price' => 1500,
            'total_rooms' => 1,
        ])->amenities()->attach($amenity);

        $this->actingAs($this->admin)
            ->delete(route('admin.amenities.destroy', $amenity))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('amenities', ['id' => $amenity->id]);
    }

    public function test_admin_can_toggle_amenity_active_state()
    {
        $amenity = $this->makeAmenity();

        $this->actingAs($this->admin)
            ->post(route('admin.amenities.toggle', $amenity))
            ->assertRedirect();

        $this->assertFalse($amenity->fresh()->is_active);
    }

    public function test_inactive_amenity_is_not_offered_on_room_forms()
    {
        $this->makeAmenity();
        $this->makeAmenity(['name' => 'Hidden Amenity', 'icon' => 'bi-droplet', 'is_active' => false]);

        $hostel = $this->makeHostel();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.hostels.rooms.create', $hostel));

        $response->assertOk();
        $response->assertSee('24/7 Security');
        $response->assertDontSee('Hidden Amenity');
    }

    protected function makeHostel(array $overrides = [])
    {
        return \App\Models\Hostel::create(array_merge([
            'name' => 'Amenity Test Hostel',
            'slug' => 'amenity-test-hostel',
            'location' => 'Tarkwa',
            'is_active' => true,
        ], $overrides));
    }
}
