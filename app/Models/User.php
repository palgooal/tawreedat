<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Canonical role names, shared by the seeder, the User Filament
     * resource, and canAccessPanel() below - defined once here so the
     * three never drift out of sync with each other.
     */
    public const ROLE_SUPER_ADMIN = 'Super Admin';

    public const ROLE_ADMIN = 'Admin';

    public const ROLE_EDITOR = 'Editor';

    public const ROLE_SUPPORT = 'Support';

    /**
     * Every role that's allowed into the Filament panel at all (as opposed
     * to being allowed to perform specific actions once inside, which is
     * governed by permissions - see RolesAndPermissionsSeeder).
     *
     * @var array<int, string>
     */
    public const PANEL_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_EDITOR,
        self::ROLE_SUPPORT,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Gates access to the Filament admin panel. Without this, Filament's
     * default behaviour is "any authenticated user of this guard can log
     * in" - which previously meant every row in `users` had full admin
     * access.
     *
     * Panel entry itself only requires one of the four admin roles, OR
     * is_admin = true. is_admin is transitional/backward-compatible
     * (see docs/DECISIONS.md) - it predates the roles/permissions system
     * and is kept only so accounts created before roles existed aren't
     * locked out. New accounts should be given a role instead; is_admin
     * may be removed once every real admin account has a role assigned.
     * What a user can actually *do* once inside the panel (which
     * resources they see, which actions they can perform) is governed
     * separately by permissions on each Resource/Page, not by this method.
     *
     * If this model is ever made to implement MustVerifyEmail, a verified
     * email address is additionally required - written generically so it
     * activates automatically without needing to revisit this method.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! ($this->is_admin || $this->hasAnyRole(self::PANEL_ROLES))) {
            return false;
        }

        if ($this instanceof MustVerifyEmailContract && ! $this->hasVerifiedEmail()) {
            return false;
        }

        return true;
    }
}
