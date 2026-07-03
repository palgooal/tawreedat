<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\NewsResource;
use App\Models\News;
use App\Support\Permissions;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * "أحدث الأخبار" — the 5 most recently created news items, with a quick
 * link into the edit form. Gated to whoever can see NewsResource (Super
 * Admin, Admin, Editor). Eager-loads categoryRelation so the category
 * column doesn't trigger a query per row.
 */
class LatestNewsWidget extends TableWidget
{
    protected static ?int $sort = -20;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can(Permissions::VIEW_CONTENT) || $user?->can(Permissions::MANAGE_NEWS));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('أحدث الأخبار')
            ->query(News::query()->with('categoryRelation')->latest()->limit(5))
            ->columns([
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),
                TextColumn::make('categoryRelation.name')
                    ->label('التصنيف')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d')
                    ->placeholder('—'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('تعديل الخبر')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->url(fn (News $record): string => NewsResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (): bool => (bool) auth()->user()?->can(Permissions::MANAGE_NEWS)),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
