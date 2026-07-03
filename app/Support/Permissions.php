<?php

namespace App\Support;

/**
 * Canonical permission name strings, shared by RolesAndPermissionsSeeder
 * (which creates them and assigns them to roles) and every gated Filament
 * Resource/Page (which checks them). Centralised here so a typo in one
 * place can't silently create a permission string that never matches what
 * a Resource actually checks for.
 */
final class Permissions
{
    // Companies
    public const VIEW_COMPANIES = 'view companies';

    public const MANAGE_COMPANIES = 'manage companies';

    // Content (news, pages, news categories)
    public const VIEW_CONTENT = 'view content';

    public const MANAGE_NEWS = 'manage news';

    public const MANAGE_PAGES = 'manage pages';

    public const MANAGE_NEWS_CATEGORIES = 'manage news categories';

    // Advertisements
    public const VIEW_ADS = 'view ads';

    public const MANAGE_ADS = 'manage ads';

    // Contact requests
    public const VIEW_CONTACT_REQUESTS = 'view contact requests';

    public const MANAGE_CONTACT_REQUESTS = 'manage contact requests';

    // Company registration requests ("سجّل شركتك") - deliberately a
    // separate permission pair from contact requests, matching every other
    // resource in this app (one permission pair per resource domain)
    // rather than overloading "manage contact requests" for a conceptually
    // different queue. See docs/DECISIONS.md.
    public const VIEW_REGISTRATION_REQUESTS = 'view registration requests';

    public const MANAGE_REGISTRATION_REQUESTS = 'manage registration requests';

    // Site settings
    public const MANAGE_SETTINGS = 'manage settings';

    // Admin users
    public const VIEW_USERS = 'view users';

    public const MANAGE_USERS = 'manage users';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::VIEW_COMPANIES,
            self::MANAGE_COMPANIES,
            self::VIEW_CONTENT,
            self::MANAGE_NEWS,
            self::MANAGE_PAGES,
            self::MANAGE_NEWS_CATEGORIES,
            self::VIEW_ADS,
            self::MANAGE_ADS,
            self::VIEW_CONTACT_REQUESTS,
            self::MANAGE_CONTACT_REQUESTS,
            self::VIEW_REGISTRATION_REQUESTS,
            self::MANAGE_REGISTRATION_REQUESTS,
            self::MANAGE_SETTINGS,
            self::VIEW_USERS,
            self::MANAGE_USERS,
        ];
    }
}
