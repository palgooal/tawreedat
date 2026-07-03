<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'لوحة تحكم توريدات';

    public static function getNavigationLabel(): string
    {
        return 'لوحة التحكم';
    }
}
