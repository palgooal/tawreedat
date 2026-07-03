<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\CompanyRegistrationRequestResource;
use App\Filament\Resources\ContactRequestResource;
use App\Filament\Resources\NewsCategoryResource;
use App\Filament\Resources\NewsResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\Permissions;
use Filament\Widgets\Widget;

/**
 * Shortcut buttons to the actions an admin reaches for most. Each button
 * carries its own permission check mirroring the target Resource/Page's own
 * authorization (see canCreate()/canViewAny()/canAccess() on each), so a
 * button is only ever shown to someone who can actually complete that
 * action — no dead-end links.
 */
class QuickActionsWidget extends Widget
{
    protected static ?int $sort = -25;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.quick-actions-widget';

    public static function canView(): bool
    {
        return count(self::visibleActions()) > 0;
    }

    /**
     * @return array<int, array{label: string, url: string, icon: string}>
     */
    public static function visibleActions(): array
    {
        $user = auth()->user();
        $actions = [];

        if ($user?->can(Permissions::MANAGE_NEWS)) {
            $actions[] = [
                'label' => 'إضافة خبر',
                'url' => NewsResource::getUrl('create'),
                'icon' => 'heroicon-o-newspaper',
            ];
        }

        if ($user?->can(Permissions::MANAGE_PAGES)) {
            $actions[] = [
                'label' => 'إضافة صفحة',
                'url' => PageResource::getUrl('create'),
                'icon' => 'heroicon-o-document-text',
            ];
        }

        if ($user?->can(Permissions::MANAGE_NEWS_CATEGORIES)) {
            $actions[] = [
                'label' => 'إضافة تصنيف خبري',
                'url' => NewsCategoryResource::getUrl('create'),
                'icon' => 'heroicon-o-bookmark',
            ];
        }

        if ($user?->can(Permissions::VIEW_CONTACT_REQUESTS) || $user?->can(Permissions::MANAGE_CONTACT_REQUESTS)) {
            $actions[] = [
                'label' => 'عرض طلبات التواصل',
                'url' => ContactRequestResource::getUrl('index'),
                'icon' => 'heroicon-o-envelope',
            ];
        }

        if ($user?->can(Permissions::VIEW_REGISTRATION_REQUESTS) || $user?->can(Permissions::MANAGE_REGISTRATION_REQUESTS)) {
            $actions[] = [
                'label' => 'طلبات تسجيل الشركات',
                'url' => CompanyRegistrationRequestResource::getUrl('index'),
                'icon' => 'heroicon-o-building-storefront',
            ];
        }

        if ($user?->can(Permissions::MANAGE_SETTINGS)) {
            $actions[] = [
                'label' => 'إعدادات الموقع',
                'url' => SiteSettings::getUrl(),
                'icon' => 'heroicon-o-cog-6-tooth',
            ];
        }

        if ($user?->hasRole(User::ROLE_SUPER_ADMIN)) {
            $actions[] = [
                'label' => 'إدارة المستخدمين',
                'url' => UserResource::getUrl('index'),
                'icon' => 'heroicon-o-users',
            ];
        }

        return $actions;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'actions' => self::visibleActions(),
        ];
    }
}
