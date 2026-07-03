<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use App\Models\Company;
use App\Models\ContactRequest;
use App\Models\News;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TawreedatStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الشركات', Company::query()->count())
                ->icon('heroicon-o-building-office-2')
                ->color('primary')
                ->extraAttributes(['class' => 'tawreedat-stat-green']),

            Stat::make('الشركات المميزة', Company::query()->where('is_featured', true)->count())
                ->icon('heroicon-o-star')
                ->color('accent')
                ->extraAttributes(['class' => 'tawreedat-stat-gold']),

            Stat::make('الأخبار المنشورة', News::query()->where('status', 'published')->count())
                ->icon('heroicon-o-newspaper')
                ->color('info')
                ->extraAttributes(['class' => 'tawreedat-stat-green']),

            Stat::make('طلبات التواصل الجديدة', ContactRequest::query()->where('status', 'new')->count())
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->extraAttributes(['class' => 'tawreedat-stat-gold']),

            Stat::make('الإعلانات النشطة', Advertisement::query()->where('is_active', true)->count())
                ->icon('heroicon-o-megaphone')
                ->color('success')
                ->extraAttributes(['class' => 'tawreedat-stat-green']),
        ];
    }
}
