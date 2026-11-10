<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminHostelUploadTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public_root');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
    }

    protected function hostelPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Upload Test Hostel',
            'institution' => 'UMaT',
            'institution_name' => 'University of Mines and Technology',
            'location' => 'Tarkwa',
            'distance' => '1.0km from campus',
            'rating' => '4.0',
            'description' => 'A hostel used to test picture uploads.',
            'is_active' => '1',
        ], $overrides);
    }

    protected function persistedHostel(): Hostel
    {
        return Hostel::create([
            'name' => 'Existing Hostel',
            'slug' => 'existing-hostel',
            'institution' => 'TTU',
            'institution_name' => 'Takoradi Technical University',
            'location' => 'Takoradi',
            'distance' => 'Near campus',
            'rating' => 3.5,
            'description' => 'Hostel used as an edit target.',
            'starting_price' => 500,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_upload_images_when_creating_hostel()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.hostels.store'), $this->hostelPayload([
            'images_upload' => [UploadedFile::fake()->image('room-a.jpg')],
        ]));

        $response->assertRedirect(route('admin.hostels.index'));

        $hostel = Hostel::where('slug', 'upload-test-hostel')->firstOrFail();

        $this->assertSame(1, $hostel->images()->count());
        $path = $hostel->images()->first()->image_path;

        $this->assertStringStartsWith('uploads/hostels/upload-test-hostel/', $path);
        Storage::disk('public_root')->assertExists(substr($path, strlen('uploads/')));
        $this->assertEquals(0, $hostel->images()->first()->position);
    }

    public function test_uploads_come_before_library_images()
    {
        $hostel = $this->persistedHostel();

        $response = $this->actingAs($this->admin)->put(route('admin.hostels.update', $hostel), $this->hostelPayload([
            'name' => $hostel->name,
            'images_upload' => [UploadedFile::fake()->image('cover.jpg')],
            'images' => ['storage/thumbnails/library.jpg'],
        ]));

        $response->assertRedirect(route('admin.hostels.index'));

        $paths = $hostel->fresh()->images()->pluck('image_path')->all();

        $this->assertStringStartsWith('uploads/hostels/', $paths[0]);
        $this->assertSame('storage/thumbnails/library.jpg', $paths[1]);
    }

    public function test_admin_can_upload_images_when_updating_hostel()
    {
        $hostel = $this->persistedHostel();

        $response = $this->actingAs($this->admin)->put(route('admin.hostels.update', $hostel), $this->hostelPayload([
            'name' => $hostel->name,
            'images_upload' => [UploadedFile::fake()->image('exterior.png')],
        ]));

        $response->assertRedirect(route('admin.hostels.index'));

        $path = $hostel->fresh()->images()->first()->image_path;
        Storage::disk('public_root')->assertExists(substr($path, strlen('uploads/')));
    }

    public function test_manager_can_upload_images_for_managed_hostel()
    {
        $hostel = $this->persistedHostel();
        $hostel->managers()->attach($this->manager);

        $response = $this->actingAs($this->manager)->put(route('manager.hostels.update', $hostel), $this->hostelPayload([
            'name' => $hostel->name,
            'images_upload' => [UploadedFile::fake()->image('room-b.png')],
        ]));

        $response->assertRedirect(route('manager.hostels.index'));

        $path = $hostel->fresh()->images()->first()->image_path;
        Storage::disk('public_root')->assertExists(substr($path, strlen('uploads/')));
    }

    public function test_manager_cannot_upload_images_for_unmanaged_hostel()
    {
        $hostel = $this->persistedHostel();

        $response = $this->actingAs($this->manager)->put(route('manager.hostels.update', $hostel), $this->hostelPayload([
            'name' => $hostel->name,
            'images_upload' => [UploadedFile::fake()->image('sneaky.jpg')],
        ]));

        $response->assertForbidden();
        $this->assertSame(0, $hostel->images()->count());
    }

    public function test_non_image_upload_is_rejected()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.hostels.store'), $this->hostelPayload([
            'images_upload' => [UploadedFile::fake()->create('notes.txt', 10)],
        ]));

        $response->assertSessionHasErrors('images_upload.0');
        $this->assertSame(0, Hostel::count());
    }

    public function test_uploaded_hostel_image_is_served_by_image_route()
    {
        $file = UploadedFile::fake()->image('photo.jpg');
        Storage::disk('public_root')->putFileAs('hostels/mm', $file, 'photo.jpg');

        $response = $this->get(route('uploads.hostel', ['hostel' => 'mm', 'file' => 'photo.jpg']));

        $response->assertOk();
        $this->assertGreaterThan(0, $response->baseResponse->getFile()->getSize());
        $this->assertSame(0, strpos($response->headers->get('Content-Type'), 'image/'));
    }

    public function test_missing_hostel_image_returns_404()
    {
        Storage::disk('public_root')->delete('hostels/mm/photo.jpg');

        $response = $this->get(route('uploads.hostel', ['hostel' => 'mm', 'file' => 'photo.jpg']));

        $response->assertNotFound();
    }
}