<?php

namespace App\Filament\Widgets;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Permissions;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Operational health snapshot for whoever can manage site settings (Super
 * Admin, Admin — Editor/Support have no reason to see server/env details).
 * Every value here is a genuine runtime check (route registration, DB
 * counts, config()) — nothing is hardcoded "always green" text.
 */
class SystemStatusWidget extends Widget
{
    protected static ?int $sort = -9;

    protected int | string | array $columnSpan = 1;

    protected string $view = 'filament.widgets.system-status-widget';

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can(Permissions::MANAGE_SETTINGS);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $indexingEnabled = (bool) SiteSetting::get('robots_indexing_enabled', false);

        $contactRoute = Route::getRoutes()->getByName('contact.store');
        $throttled = $contactRoute !== null && collect($contactRoute->middleware())
            ->contains(fn (string $middleware): bool => Str::startsWith($middleware, 'throttle:'));

        $notificationRecipients = User::query()
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhereHas('roles', fn ($inner) => $inner->whereIn('name', [
                        User::ROLE_SUPER_ADMIN,
                        User::ROLE_ADMIN,
                        User::ROLE_SUPPORT,
                    ]));
            })
            ->count();

        $rolesSeeded = Role::query()->whereIn('name', User::PANEL_ROLES)->count() === count(User::PANEL_ROLES);
        $hasSuperAdmin = User::query()->role(User::ROLE_SUPER_ADMIN)->exists();

        $debugEnabled = (bool) config('app.debug');

        return [
            'seo' => [
                'sitemapReady' => Route::has('sitemap'),
                'robotsReady' => Route::has('robots'),
                'indexingEnabled' => $indexingEnabled,
            ],
            'contact' => [
                'notificationsEnabled' => $notificationRecipients > 0,
                'recipientsCount' => $notificationRecipients,
                'protectionEnabled' => $throttled,
            ],
            'security' => [
                'rolesSeeded' => $rolesSeeded,
                'hasSuperAdmin' => $hasSuperAdmin,
            ],
            'environment' => [
                'appEnv' => (string) config('app.env'),
                'appDebug' => $debugEnabled,
                'queueConnection' => (string) config('queue.default'),
                'mailMailer' => (string) config('mail.default'),
            ],
            'debugEnabled' => $debugEnabled,
        ];
    }
}
