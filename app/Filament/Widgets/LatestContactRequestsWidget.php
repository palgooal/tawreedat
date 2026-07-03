<?php

namespace App\Filament\Widgets;

use App\Models\ContactRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestContactRequestsWidget extends TableWidget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('أحدث طلبات التواصل')
            ->query(ContactRequest::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                TextColumn::make('inquiry_type')
                    ->label('نوع الاستفسار'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'جديد',
                        'in_progress' => 'قيد المعالجة',
                        'resolved' => 'تم الحل',
                        'closed' => 'مغلق',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d'),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
