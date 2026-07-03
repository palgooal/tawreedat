<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Roles/permissions must exist before anything below tries to
        // assign a role to a user.
        $this->call(RolesAndPermissionsSeeder::class);

        // WARNING: never seed demo/test users outside local. This block is
        // gated on app()->environment('local') so it cannot create the
        // demo admin account against staging/production, even if db:seed
        // is run there by mistake. test@example.com uses a well-known
        // factory password ("password") and is granted is_admin (plus the
        // Super Admin role) so it can reach the Filament panel locally -
        // it must never exist anywhere reachable outside a developer's
        // own machine. See docs/DEPLOYMENT_CHECKLIST.md for the full
        // seeding policy.
        if (app()->environment('local')) {
            // updateOrCreate (not factory()->create()) so re-running
            // db:seed doesn't hit a duplicate-email constraint violation,
            // and so is_admin is force-set to true even for a row that was
            // seeded before this column/policy existed.
            $demoUser = User::updateOrCreate(
                ['email' => 'test@example.com'],
                [
                    ...User::factory()->raw(['name' => 'Test User', 'email' => 'test@example.com']),
                    'is_admin' => true,
                ],
            );

            // assignRole() is idempotent - safe to call on every db:seed run.
            $demoUser->assignRole(User::ROLE_SUPER_ADMIN);
        }

        $this->call(SiteSettingSeeder::class);
        $this->call(TawreedatDemoSeeder::class);
        $this->call(NewsCategorySeeder::class);
        $this->call(PageSeeder::class);
    }
}
