<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Creates the 4 MVP roles and 13 permissions, and assigns permissions
     * to roles. Safe to re-run: permissions/roles are created with
     * firstOrCreate, and role → permission assignment uses syncPermissions
     * so re-running always converges to exactly the set defined below,
     * rather than only ever adding.
     *
     * Does NOT touch which users have which roles - that's left to
     * DatabaseSeeder (for the local demo user) and the admin UI
     * (UserResource) for everyone else.
     */
    public function run(): void
    {
        // Spatie caches permissions in memory; forget the cache before
        // (re-)creating them so this seeder behaves correctly even when
        // run multiple times in the same process (e.g. `db:seed` calling
        // multiple seeders that touch permissions).
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::all() as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => User::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permissions::all());

        // "All except manage users" - Admin manages day-to-day content and
        // operations but not who else has admin access. Only Super Admin
        // manages users (see docs/DECISIONS.md for this call).
        $admin = Role::firstOrCreate(['name' => User::ROLE_ADMIN, 'guard_name' => 'web']);
        $admin->syncPermissions(array_values(array_diff(Permissions::all(), [
            Permissions::MANAGE_USERS,
            Permissions::VIEW_USERS,
        ])));

        $editor = Role::firstOrCreate(['name' => User::ROLE_EDITOR, 'guard_name' => 'web']);
        $editor->syncPermissions([
            Permissions::VIEW_CONTENT,
            Permissions::MANAGE_NEWS,
            Permissions::MANAGE_PAGES,
            Permissions::MANAGE_NEWS_CATEGORIES,
        ]);

        $support = Role::firstOrCreate(['name' => User::ROLE_SUPPORT, 'guard_name' => 'web']);
        $support->syncPermissions([
            Permissions::VIEW_CONTACT_REQUESTS,
            Permissions::MANAGE_CONTACT_REQUESTS,
            // Company registration requests are new-business/sales leads,
            // squarely within "front-line support/sales" - see the Support
            // role description in docs/ADMIN_PANEL.md.
            Permissions::VIEW_REGISTRATION_REQUESTS,
            Permissions::MANAGE_REGISTRATION_REQUESTS,
        ]);
    }
}
