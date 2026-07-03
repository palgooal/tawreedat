<?php

namespace App\Filament\Widgets;

use App\Models\CompanyRegistrationRequest;
use App\Models\ContactRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\User;
use App\Support\Permissions;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * Top-row KPI cards for the executive dashboard. Every number is a real,
 * cheap COUNT() query (no fake/placeholder data) and the whole batch is
 * cached briefly (see CACHE_KEY) so opening the dashboard doesn't fire
 * 5+ COUNT queries on every single request.
 *
 * Card-level visibility is permission-driven rather than role-hardcoded,
 * which is what actually implements the "Admin sees everything except user
 * management" / "Editor sees content only" / "Support sees contact only"
 * rules from the brief: each card independently checks the permission that
 * governs the resource it summarizes, so it naturally disappears for anyone
 * who couldn't act on it anyway. See docs/ADMIN_PANEL.md → "Dashboard".
 */
class KpiOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -30;

    protected int | string | array $columnSpan = 'full';

    private const CACHE_KEY = 'filament.dashboard.kpi.v2';

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can(Permissions::VIEW_CONTENT)
            || $user?->can(Permissions::MANAGE_NEWS)
            || $user?->can(Permissions::VIEW_CONTACT_REQUESTS)
            || $user?->can(Permissions::MANAGE_CONTACT_REQUESTS)
            || $user?->can(Permissions::VIEW_REGISTRATION_REQUESTS)
            || $user?->can(Permissions::MANAGE_REGISTRATION_REQUESTS)
            || $user?->hasRole(User::ROLE_SUPER_ADMIN));
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $data = $this->getCachedData();
        $stats = [];

        if ($user?->can(Permissions::VIEW_CONTENT) || $user?->can(Permissions::MANAGE_NEWS)) {
            $stats[] = Stat::make('إجمالي الأخبار المنشورة', $data['news_published'])
                ->description($data['news_published_this_month'] > 0
                    ? "+{$data['news_published_this_month']} هذا الشهر"
                    : 'لا يوجد جديد هذا الشهر')
                ->descriptionIcon($data['news_published_this_month'] > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-minus')
                ->icon('heroicon-o-newspaper')
                ->color('primary')
                ->extraAttributes(['class' => 'tawreedat-stat-green']);
        }

        if ($user?->can(Permissions::VIEW_CONTENT) || $user?->can(Permissions::MANAGE_NEWS_CATEGORIES)) {
            $stats[] = Stat::make('التصنيفات الإخبارية', $data['news_categories_total'])
                ->description("{$data['news_categories_active']} نشط")
                ->descriptionIcon('heroicon-o-bookmark')
                ->icon('heroicon-o-bookmark')
                ->color('accent')
                ->extraAttributes(['class' => 'tawreedat-stat-gold']);
        }

        if ($user?->can(Permissions::VIEW_CONTACT_REQUESTS) || $user?->can(Permissions::MANAGE_CONTACT_REQUESTS)) {
            $stats[] = Stat::make('طلبات التواصل الجديدة', $data['contact_new'])
                ->description($data['contact_this_week'] > 0
                    ? "+{$data['contact_this_week']} هذا الأسبوع"
                    : 'لا يوجد جديد هذا الأسبوع')
                ->descriptionIcon($data['contact_this_week'] > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-minus')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->extraAttributes(['class' => 'tawreedat-stat-gold']);
        }

        if ($user?->can(Permissions::VIEW_CONTENT) || $user?->can(Permissions::MANAGE_PAGES)) {
            $stats[] = Stat::make('إجمالي الصفحات المنشورة', $data['pages_published'])
                ->description("من إجمالي {$data['pages_total']} صفحة")
                ->descriptionIcon('heroicon-o-document-text')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->extraAttributes(['class' => 'tawreedat-stat-green']);
        }

        if ($user?->can(Permissions::VIEW_REGISTRATION_REQUESTS) || $user?->can(Permissions::MANAGE_REGISTRATION_REQUESTS)) {
            $stats[] = Stat::make('طلبات تسجيل الشركات قيد المراجعة', $data['registration_requests_pending'])
                ->description($data['registration_requests_this_week'] > 0
                    ? "+{$data['registration_requests_this_week']} هذا الأسبوع"
                    : 'لا يوجد جديد هذا الأسبوع')
                ->descriptionIcon($data['registration_requests_this_week'] > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-minus')
                ->icon('heroicon-o-building-storefront')
                ->color('warning')
                ->extraAttributes(['class' => 'tawreedat-stat-gold']);
        }

        if ($user?->hasRole(User::ROLE_SUPER_ADMIN)) {
            $stats[] = Stat::make('عدد المستخدمين الإداريين', $data['admin_users_total'])
                ->description("منهم {$data['super_admins_total']} Super Admin")
                ->descriptionIcon('heroicon-o-shield-check')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->extraAttributes(['class' => 'tawreedat-stat-gold']);
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
                'news_published' => News::query()->where('status', 'published')->count(),
                'news_published_this_month' => News::query()
                    ->where('status', 'published')
                    ->whereYear('published_at', $now->year)
                    ->whereMonth('published_at', $now->month)
                    ->count(),
                'news_categories_total' => NewsCategory::query()->count(),
                'news_categories_active' => NewsCategory::query()->where('is_active', true)->count(),
                'contact_new' => ContactRequest::query()->where('status', 'new')->count(),
                'contact_this_week' => ContactRequest::query()->where('created_at', '>=', $now->clone()->startOfWeek())->count(),
                'pages_published' => Page::query()->where('status', 'published')->count(),
                'pages_total' => Page::query()->count(),
                'registration_requests_pending' => CompanyRegistrationRequest::query()
                    ->where('status', CompanyRegistrationRequest::STATUS_PENDING)
                    ->count(),
                'registration_requests_this_week' => CompanyRegistrationRequest::query()
                    ->where('created_at', '>=', $now->clone()->startOfWeek())
                    ->count(),
                'admin_users_total' => User::query()
                    ->where(function ($query) {
                        $query->where('is_admin', true)
                            ->orWhereHas('roles', fn ($inner) => $inner->whereIn('name', User::PANEL_ROLES));
                    })
                    ->count(),
                'super_admins_total' => User::query()->role(User::ROLE_SUPER_ADMIN)->count(),
            ];
        });
    }
}
