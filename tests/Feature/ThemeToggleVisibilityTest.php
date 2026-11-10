<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeToggleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Classes on the element that wraps the theme toggle button.
     */
    protected function toggleWrapperClasses(string $html): string
    {
        $matched = preg_match(
            '#<li class="([^"]*)"[^>]*>\s*<button[^>]*class="[^"]*mc-theme-toggle[^"]*"#',
            $html,
            $matches
        );

        $this->assertSame(1, $matched, 'Theme toggle wrapper not found.');

        return $matches[1];
    }

    public function test_theme_toggle_is_not_hidden_on_small_screens()
    {
        $classes = $this->toggleWrapperClasses($this->get('/')->assertOk()->getContent());

        $this->assertStringNotContainsString('d-none', $classes);
        $this->assertStringNotContainsString('d-sm-block', $classes);
        $this->assertStringNotContainsString('d-md-', $classes);
        $this->assertStringNotContainsString('d-lg-', $classes);
    }

    public function test_theme_toggle_sits_inside_the_mobile_navigation_collapse()
    {
        $html = $this->get('/')->assertOk()->getContent();

        $collapseAt = strpos($html, 'id="navbarCollapse"');
        $toggleAt = strpos($html, 'js-theme-toggle');

        $this->assertNotFalse($collapseAt, 'Navbar collapse region not found.');
        $this->assertNotFalse($toggleAt, 'Theme toggle not found.');
        $this->assertGreaterThan(
            $collapseAt,
            $toggleAt,
            'Theme toggle must live inside the collapsible mobile menu.'
        );
    }

    public function test_theme_toggle_renders_both_icons()
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#js-theme-toggle.*?theme-icon-light.*?theme-icon-dark#s',
            $html
        );
    }

    public function test_layout_declares_a_theme_and_loads_the_theme_script()
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-bs-theme="light"', $html);
        $this->assertStringContainsString(asset('assets/js/theme.js'), $html);
    }

    public function test_admin_topbar_theme_toggle_is_not_hidden()
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#<button[^>]*class="[^"]*js-theme-toggle[^"]*"#',
            $html
        );
        $this->assertStringNotContainsString('d-none d-sm-block', $html);
    }
}
