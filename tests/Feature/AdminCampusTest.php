<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\Hostel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCampusTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_guest_is_redirected_from_campuses_index()
    {
        $this->get(route('admin.campuses.index'))->assertRedirect('/login');
    }

    public function test_non_admin_is_forbidden_from_campuses()
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($manager)
            ->get(route('admin.campuses.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_campuses_index()
    {
        Campus::create([
            'name' => 'University of Cape Coast',
            'slug' => 'university-of-cape-coast',
            'latitude' => 5.103600,
            'longitude' => -1.282430,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.campuses.index'))
            ->assertOk()
            ->assertSee('University of Cape Coast')
            ->assertSee('university-of-cape-coast');
    }

    public function test_admin_can_create_a_campus()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.campuses.store'), [
                'name' => 'University of Cape Coast',
                'short_name' => 'UCC',
                'latitude' => 5.103600,
                'longitude' => -1.282430,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.campuses.index'));

        $this->assertDatabaseHas('campuses', [
            'name' => 'University of Cape Coast',
            'slug' => 'university-of-cape-coast',
            'short_name' => 'UCC',
            'is_active' => true,
        ]);
    }

    public function test_duplicate_campus_name_gets_a_unique_slug()
    {
        Campus::create(['name' => 'University of Cape Coast', 'slug' => 'university-of-cape-coast']);

        $this->actingAs($this->admin)
            ->post(route('admin.campuses.store'), [
                'name' => 'University of Cape Coast',
            ])
            ->assertRedirect(route('admin.campuses.index'));

        $this->assertDatabaseHas('campuses', ['slug' => 'university-of-cape-coast-2']);
    }

    public function test_admin_can_update_a_campus()
    {
        $campus = Campus::create(['name' => 'University of Cape Coast', 'slug' => 'university-of-cape-coast']);

        $this->actingAs($this->admin)
            ->put(route('admin.campuses.update', $campus), [
                'name' => 'University of Ghana, Legon',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.campuses.index'));

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id,
            'name' => 'University of Ghana, Legon',
            'slug' => 'university-of-ghana-legon',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_an_unused_campus()
    {
        $campus = Campus::create(['name' => 'Old Campus', 'slug' => 'old-campus']);

        $this->actingAs($this->admin)
            ->delete(route('admin.campuses.destroy', $campus))
            ->assertRedirect();

        $this->assertDatabaseMissing('campuses', ['id' => $campus->id]);
    }

    public function test_campus_linked_to_hostels_cannot_be_deleted()
    {
        $campus = Campus::create(['name' => 'University of Cape Coast', 'slug' => 'university-of-cape-coast']);

        Hostel::create([
            'name' => 'Linked Hostel',
            'slug' => 'linked-hostel',
            'institution' => $campus->slug,
            'institution_name' => $campus->name,
            'location' => 'Cape Coast',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.campuses.destroy', $campus))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('campuses', ['id' => $campus->id]);
    }

    public function test_hostel_institutions_list_is_database_driven()
    {
        Campus::create(['name' => 'University of Cape Coast', 'slug' => 'university-of-cape-coast']);
        Campus::create(['name' => 'KNUST Kumasi Campus', 'slug' => 'knust-kumasi-campus']);

        $institutions = Hostel::institutions();

        $this->assertSame('Select Institution', $institutions['']);
        $this->assertSame('University of Cape Coast', $institutions['university-of-cape-coast']);
        $this->assertSame('KNUST Kumasi Campus', $institutions['knust-kumasi-campus']);
    }

    public function test_campus_coordinates_come_from_the_campus_record()
    {
        $campus = Campus::create([
            'name' => 'University of Cape Coast',
            'slug' => 'university-of-cape-coast',
            'latitude' => 5.103600,
            'longitude' => -1.282430,
        ]);

        $hostel = Hostel::create([
            'name' => 'Linked Hostel',
            'slug' => 'linked-hostel',
            'institution' => $campus->slug,
            'institution_name' => $campus->name,
            'location' => 'Cape Coast',
        ]);

        $coordinates = $hostel->campusCoordinates();

        $this->assertSame($campus->name, $coordinates['name']);
        $this->assertSame(5.1036, $coordinates['lat']);
        $this->assertSame(-1.28243, $coordinates['lng']);
    }
}