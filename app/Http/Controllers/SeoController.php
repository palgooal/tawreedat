<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SeoController extends Controller
{
    /**
     * /robots.txt — driven entirely by the robots_indexing_enabled and
     * robots_extra_rules settings from the admin Site Settings page, so
     * indexing can be toggled off during development without a deploy.
     */
    public function robots(): Response
    {
        $indexingEnabled = (bool) SiteSetting::get('robots_indexing_enabled', false);
        $extraRules = trim((string) SiteSetting::get('robots_extra_rules', ''));

        $lines = ['User-agent: *'];
        $lines[] = $indexingEnabled ? 'Allow: /' : 'Disallow: /';

        if ($indexingEnabled) {
            $lines[] = '';
            $lines[] = 'Sitemap: '.url('/sitemap.xml');
        }

        if ($extraRules !== '') {
            $lines[] = '';
            $lines[] = $extraRules;
        }

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain');
    }

    /**
     * /sitemap.xml — every currently-public page on the site. Companies
     * directory/profile URLs are intentionally excluded: that section is
     * deferred to Phase 2 and isn't meant to be discoverable/indexed yet
     * (see docs/DECISIONS.md).
     */
    public function sitemap(): Response
    {
        $urls = collect();

        $urls->push($this->url(url('/')));
        $urls->push($this->url(route('news.index')));
        $urls->push($this->url(route('contact')));

        foreach (['about', 'plans', 'privacy', 'terms'] as $slug) {
            $page = Page::query()->published()->where('slug', $slug)->first();

            if (! $page) {
                continue;
            }

            $urls->push($this->url($page->publicUrl(), $page->updated_at));
        }

        News::query()
            ->published()
            ->orderByDesc('published_at')
            ->get(['slug', 'published_at', 'updated_at'])
            ->each(function (News $news) use ($urls): void {
                $urls->push($this->url(
                    route('news.show', ['slug' => $news->slug]),
                    $news->published_at ?? $news->updated_at,
                ));
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml)->header('Content-Type', 'application/xml');
    }

    /**
     * @return array{loc: string, lastmod: ?Carbon}
     */
    private function url(string $loc, ?Carbon $lastmod = null): array
    {
        return ['loc' => $loc, 'lastmod' => $lastmod];
    }
}
