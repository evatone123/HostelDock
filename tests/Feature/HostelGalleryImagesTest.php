<?php

namespace Tests\Feature;

use App\Models\Hostel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HostelGalleryImagesTest extends TestCase
{
    use RefreshDatabase;

    private $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        $this->created = [];

        parent::tearDown();
    }

    private function fakeImage(string $relative): string
    {
        $absolute = public_path($relative);

        if (!is_dir(dirname($absolute))) {
            mkdir(dirname($absolute), 0755, true);
        }

        file_put_contents($absolute, 'fake-image-bytes');
        $this->created[] = $absolute;

        return $relative;
    }

    private function hostel(): Hostel
    {
        static $seq = 0;
        $seq++;

        return Hostel::create([
            'name' => 'Gallery Hostel ' . $seq,
            'slug' => 'gallery-hostel-' . $seq,
            'location' => 'Tarkwa', 'is_active' => true,
        ]);
    }

    public function test_gallery_uses_the_hostels_own_photos_when_the_files_exist()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => $this->fakeImage('storage/thumbnails/gallery-a.jpg'),
            'position' => 1,
        ]);
        $hostel->images()->create([
            'image_path' => $this->fakeImage('storage/thumbnails/gallery-b.jpg'),
            'position' => 2,
        ]);

        $this->assertSame([
            'storage/thumbnails/gallery-a.jpg',
            'storage/thumbnails/gallery-b.jpg',
        ], $hostel->galleryImages());
    }

    public function test_gallery_keeps_only_images_that_actually_exist()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => $this->fakeImage('storage/thumbnails/gallery-real.jpg'),
            'position' => 1,
        ]);
        $hostel->images()->create([
            'image_path' => 'storage/thumbnails/gallery-gone.jpg',
            'position' => 2,
        ]);

        $this->assertSame(
            ['storage/thumbnails/gallery-real.jpg'],
            $hostel->galleryImages()
        );
    }

    public function test_gallery_falls_back_to_folder_images_when_no_photo_exists()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => 'storage/thumbnails/gallery-missing.jpg',
            'position' => 1,
        ]);

        $this->assertSame($hostel->sliderImages(), $hostel->galleryImages());
        $this->assertSame([
            'assets/images/about/slider-imgs.jpg',
            'assets/images/about/slider-imgs2.jpg',
        ], $hostel->galleryImages());
    }

    public function test_gallery_falls_back_when_the_hostel_has_no_images_at_all()
    {
        $this->assertSame([
            'assets/images/about/slider-imgs.jpg',
            'assets/images/about/slider-imgs2.jpg',
        ], $this->hostel()->galleryImages());
    }

    public function test_cover_image_path_prefers_the_first_real_photo()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => $this->fakeImage('storage/thumbnails/cover-real.jpg'),
            'position' => 1,
        ]);

        $this->assertSame('storage/thumbnails/cover-real.jpg', $hostel->coverImagePath());
    }

    public function test_cover_image_path_falls_back_to_the_folder_slider_without_photos()
    {
        $this->assertSame('assets/images/about/slider-imgs.jpg', $this->hostel()->coverImagePath());
    }

    public function test_homepage_card_shows_the_hostels_own_photo()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => $this->fakeImage('storage/thumbnails/card-real.jpg'),
            'position' => 1,
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString(asset('storage/thumbnails/card-real.jpg'), $html);
    }

    public function test_hostel_detail_gallery_shows_the_hostels_own_photo()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => $this->fakeImage('storage/thumbnails/detail-real.jpg'),
            'position' => 1,
        ]);

        $html = $this->get(route('hostel.detail', $hostel->slug))->assertOk()->getContent();

        $this->assertStringContainsString(asset('storage/thumbnails/detail-real.jpg'), $html);
    }
}
