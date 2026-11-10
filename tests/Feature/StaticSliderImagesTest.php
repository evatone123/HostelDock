<?php

namespace Tests\Feature;

use App\Models\Hostel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticSliderImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function hostel(): Hostel
    {
        static $seq = 0;
        $seq++;

        return Hostel::create([
            'name' => 'Slider Hostel ' . $seq,
            'slug' => 'slider-hostel-' . $seq,
            'location' => 'Tarkwa', 'is_active' => true,
        ]);
    }

    public function test_slider_images_come_from_config_not_the_database()
    {
        config(['hosteldock.slider_images' => [
            'assets/images/about/slider-imgs.jpg',
            'assets/images/about/slider-imgs2.jpg',
        ]]);

        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => 'storage/thumbnails/does-not-exist.jpg',
            'position' => 1,
        ]);

        $this->assertSame([
            'assets/images/about/slider-imgs.jpg',
            'assets/images/about/slider-imgs2.jpg',
        ], $hostel->fresh()->sliderImages());
    }

    public function test_cover_image_path_is_deterministic_and_from_the_folder()
    {
        config(['hosteldock.slider_images' => [
            'assets/images/about/slider-imgs.jpg',
            'assets/images/about/slider-imgs2.jpg',
        ]]);

        $hostel = $this->hostel();

        $this->assertSame($hostel->coverImagePath(), $hostel->fresh()->coverImagePath());
        $this->assertContains(
            $hostel->coverImagePath(),
            config('hosteldock.slider_images')
        );
    }

    public function test_falls_back_when_config_list_is_empty_or_blank()
    {
        foreach ([[], ['', null]] as $value) {
            config(['hosteldock.slider_images' => $value]);

            $this->assertSame(
                ['assets/images/about/slider-imgs.jpg'],
                $this->hostel()->sliderImages()
            );
        }
    }

    public function test_configured_images_actually_exist_in_public()
    {
        foreach (config('hosteldock.slider_images') as $image) {
            $this->assertFileExists(public_path($image), "Missing slider image: $image");
        }
    }

    public function test_hostel_detail_renders_folder_images_not_broken_db_paths()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => 'storage/thumbnails/does-not-exist.jpg',
            'position' => 1,
        ]);

        $html = $this->get(route('hostel.detail', $hostel->slug))->assertOk()->getContent();

        $this->assertStringNotContainsString('storage/thumbnails/does-not-exist.jpg', $html);
        $this->assertStringContainsString(asset('assets/images/about/slider-imgs.jpg'), $html);
    }

    public function test_homepage_slider_renders_folder_images_not_broken_db_paths()
    {
        $hostel = $this->hostel();
        $hostel->images()->create([
            'image_path' => 'storage/thumbnails/does-not-exist.jpg',
            'position' => 1,
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('storage/thumbnails/does-not-exist.jpg', $html);
        $this->assertStringContainsString(
            asset('assets/images/about/slider-imgs.jpg'),
            $html
        );
        $this->assertStringContainsString(
            asset('assets/images/about/slider-imgs2.jpg'),
            $html
        );
    }
}
