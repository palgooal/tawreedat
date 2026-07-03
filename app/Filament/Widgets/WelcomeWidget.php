<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -3;

    protected string $view = 'filament.widgets.welcome-widget';
}
