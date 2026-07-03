<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * The executive dashboard. Widget selection/ordering lives in
 * AdminPanelProvider::panel()->widgets() and each widget's own
 * canView()/$sort; this class only controls page-level chrome (title,
 * nav label, grid layout). See docs/ADMIN_PANEL.md → "Dashboard".
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'لوحة تحكم توريدات';

    public static function getNavigationLabel(): string
    {
        return 'لوحة التحكم';
    }

    /**
     * Explicit responsive breakpoints (rather than relying on the default
     * flat "2") so the grid genuinely collapses to a single column on
     * small screens instead of squeezing 2-up cards, per the "mobile
     * layout remains usable" requirement.
     *
     * @return array<string, int>
     */
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }
}
