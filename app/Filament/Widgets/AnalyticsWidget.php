<?php

namespace App\Filament\Widgets;

use App\Models\ContactRequest;
use App\Models\News;
use App\Models\Page;
use App\Models\User;
use App\Support\Permissions;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * Simple period-based activity snapshot — deliberately no charts, per the
 * brief ("No charts yet. Use simple stats cards."). Same permission-driven
 * visibility pattern as KpiOverviewWidget: each card only appears for a
 * user who can act on the thing it counts.
 */
class AnalyticsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 1;

    private const CACHE_KEY = 'filament.dashboard.analytics.v1';

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can(Permissions::VIEW_CONTENT)
            || $user?->can(Permissions::MANAGE_NEWS)
            || $user?->can(Permissions::MANAGE_PAGES)
            || $user?->can(Permissions::VIEW_CONTACT_REQUESTS)
            || $user?->can(Permissions::MANAGE_CONTACT_REQUESTS)
            || $user?->hasRole(User::ROLE_SUPER_ADMIN));
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $data = $this->getCachedData();
        $stats = [];

        if ($user?->can(Permissions::VIEW_CONTENT) || $user?->can(Permissions::MANAGE_NEWS)) {
            $stats[] = Stat::make('أخبار هذا الشهر', $data['news_this_month'])
                ->icon('heroicon-o-newspaper')
                ->color('primary');
        }

        if ($user?->can(Permissions::VIEW_CONTACT_REQUESTS) || $user?->can(Permissions::MANAGE_CONTACT_REQUESTS)) {
            $stats[] = Stat::make('طلبات التواصل هذا الأسبوع', $data['contact_this_week'])
                ->icon('heroicon-o-envelope')
                ->color('warning');
        }

        if ($user?->hasRole(User::ROLE_SUPER_ADMIN)) {
            $stats[] = Stat::make('المستخدمون الإداريون', $data['admin_users_total'])
                ->icon('heroicon-o-users')
                ->color('gray');
        }

        if ($user?->can(Permissions::VIEW_CONTENT) || $user?->can(Permissions::MANAGE_PAGES)) {
            $stats[] = Stat::make('الصفحات المنشورة', $data['pages_published'])
                ->icon('heroicon-o-document-text')
                ->color('info');
        }

        return $stats;
    }

    /**
     * @return array<string, int>
     */
    private function getCachedData(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(5), function (): array {
            $now = now();

            return [
                'news_this_month' => News::query()
                    ->whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->count(),
                'contact_this_week' => ContactRequest::query()
                    ->where('created_at', '>=', $now->clone()->startOfWeek())
                    ->count(),
                'admin_users_total' => User::query()
                    ->where(function ($query) {
                        $query->where('is_admin', true)
                            ->orWhereHas('roles', fn ($inner) => $inner->whereIn('name', User::PANEL_ROLES));
                    })
                    ->count(),
                'pages_published' => Page::query()->where('status', 'published')->count(),
            ];
        });
    }
}
