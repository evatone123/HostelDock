<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    protected const STATIC_PAGES = [
        ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['route' => 'about', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['route' => 'faq', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['route' => 'register', 'priority' => '0.5', 'changefreq' => 'yearly'],
        ['route' => 'login', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['route' => 'privacy', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ['route' => 'terms', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ['route' => 'residency', 'priority' => '0.2', 'changefreq' => 'yearly'],
    ];

    public function index(): Response
    {
        $urls = [];

        foreach (self::STATIC_PAGES as $page) {
            $urls[] = [
                'loc' => route($page['route']),
                'priority' => $page['priority'],
                'changefreq' => $page['changefreq'],
                'lastmod' => null,
            ];
        }

        $hostels = Hostel::where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('id')
            ->get(['slug', 'updated_at']);

        foreach ($hostels as $hostel) {
            $urls[] = [
                'loc' => route('hostel.detail', $hostel),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => optional($hostel->updated_at)->toAtomString(),
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
