<?php

namespace App\Providers;

use App\Models\Advertisement;
use App\Models\AdvertisementSlot;
use App\Support\AdvertisementManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The site-wide header banner lives in a partial shared by every
        // page, so it needs its own view composer rather than being passed
        // from a single controller (e.g. HomeController only renders
        // pages.home). This is also the ONLY place headerBanner's impression
        // is recorded, since the partial renders on every page exactly once.
        View::composer('layouts.partials.header', function ($view): void {
            $view->with('headerBanner', $this->resolveAndRecordImpression('header_banner'));
        });

        View::composer('pages.home', function ($view): void {
            $view->with([
                // Read dimensions fresh so admin changes apply on the next render.
                'homeAdvertisementSlots' => AdvertisementSlot::query()
                    ->whereIn('key', ['home_banner_1', 'home_banner_2', 'home_banner_3', 'home_banner_4'])
                    ->get(['key', 'width', 'height'])
                    ->keyBy('key'),
                // Reused from the same slot as the sitewide header banner
                // (shown a second time as a homepage card) — impression
                // already counted by the header partial's composer above,
                // so this lookup must NOT increment views again.
                'headerBanner' => AdvertisementManager::slot('header_banner'),
                'homeBanner1' => $this->resolveAndRecordImpression('home_banner_1'),
                'homeBanner2' => $this->resolveAndRecordImpression('home_banner_2'),
                'homeBanner3' => $this->resolveAndRecordImpression('home_banner_3'),
                'homeBanner4' => $this->resolveAndRecordImpression('home_banner_4'),
            ]);
        });

        View::composer('news.show', function ($view): void {
            $view->with([
                'newsSidebarBanner' => $this->resolveAndRecordImpression('news_sidebar'),
                'newsFooterBanner' => $this->resolveAndRecordImpression('news_footer'),
            ]);
        });
    }

    /**
     * Resolve the active ad for a slot and count this render as one
     * impression. Views are only ever incremented here — never inside
     * AdvertisementManager::slot() itself, since that lookup is cached for 5
     * minutes and would otherwise undercount real page views.
     */
    private function resolveAndRecordImpression(string $slotKey): ?Advertisement
    {
        $advertisement = AdvertisementManager::slot($slotKey);

        $advertisement?->increment('views');

        return $advertisement;
    }
}
