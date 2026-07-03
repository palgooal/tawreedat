<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentCompaniesWidget extends TableWidget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('أحدث الشركات')
            ->query(Company::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الشركة')
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label('المدينة'),
                TextColumn::make('category.name')
                    ->label('التصنيف'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد المراجعة',
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'rejected' => 'مرفوض',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'inactive' => 'gray',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d'),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
