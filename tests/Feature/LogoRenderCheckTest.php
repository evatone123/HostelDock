<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoRenderCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_renders_both_logo_variants()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $html = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString(
            '<img class="h-40px sidebar-logo-expanded" src="' . hd_asset('assets/images/logo.png') . '"',
            $html
        );
        $this->assertStringContainsString(
            '<img class="h-40px sidebar-logo-collapsed" src="' . hd_asset('assets/images/logo2.png') . '"',
            $html
        );
    }

    public function test_admin_style_block_never_nests_a_media_query()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $html = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->getContent();

        preg_match('#<style>(.*?)</style>#s', $html, $m);
        $css = $m[1] ?? '';

        // A @media inside another @media is silently dropped by browsers, so the
        // collapsed-sidebar logo swap would never apply. Track brace depth at
        // each @media and fail if one opens while another is still open.
        $depth = 0;
        $mediaDepths = [];
        $length = strlen($css);

        for ($i = 0; $i < $length; $i++) {
            $char = $css[$i];

            if ($char === '{') {
                $depth++;
                if (preg_match('/@media[^{}]*$/', substr($css, max(0, $i - 200), min(200, $i)))) {
                    $mediaDepths[] = $depth;
                }
            } elseif ($char === '}') {
                $depth--;
            }
        }

        $this->assertNotEmpty($mediaDepths, 'Expected at least one @media rule.');
        $this->assertSame(
            [1],
            array_values(array_unique($mediaDepths)),
            'A @media rule is nested inside another @media rule.'
        );
    }

    public function test_public_header_and_footer_logos_reference_the_folder_files()
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString(hd_asset('assets/images/logo.png'), $html);
        $this->assertStringNotContainsString('storage/thumbnails', $html);
    }

    public function test_homepage_and_about_reference_the_folder_slider_images()
    {
        $home = $this->get('/')->assertOk()->getContent();
        $about = $this->get(route('about'))->assertOk()->getContent();

        $this->assertStringContainsString(hd_asset('assets/images/about/slider-imgs.jpg'), $home);
        $this->assertStringContainsString(hd_asset('assets/images/about/slider-imgs.jpg'), $about);
        $this->assertStringNotContainsString('storage/thumbnails', $home);
    }

    public function test_hostel_detail_avatar_uses_the_square_mark()
    {
        $hostel = Hostel::create([
            'name' => 'Logo Hostel', 'slug' => 'logo-hostel',
            'location' => 'Tarkwa', 'is_active' => true,
        ]);

        $html = $this->get(route('hostel.detail', $hostel))->assertOk()->getContent();

        // .avatar is 3rem square with object-fit:cover, so a 4.33:1 wordmark
        // would be centre-cropped into an unreadable sliver.
        $this->assertMatchesRegularExpression(
            '#<img class="avatar-img rounded-circle" src="' . preg_quote(hd_asset('assets/images/logo2.png'), '#') . '"#',
            $html
        );
    }
}
