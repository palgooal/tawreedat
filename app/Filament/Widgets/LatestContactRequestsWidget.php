<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactRequestResource;
use App\Models\ContactRequest;
use App\Support\Permissions;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * "آخر طلبات التواصل" — the 5 most recent contact requests, with a quick
 * link straight into the edit form. Gated to whoever can actually see the
 * ContactRequestResource (Super Admin, Admin, Support) so Editor never sees
 * a widget it has no permission to act on.
 */
class LatestContactRequestsWidget extends TableWidget
{
    protected static ?int $sort = -19;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can(Permissions::VIEW_CONTACT_REQUESTS) || $user?->can(Permissions::MANAGE_CONTACT_REQUESTS));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('آخر طلبات التواصل')
            ->query(ContactRequest::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('company')
                    ->label('الشركة')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('inquiry_type')
                    ->label('نوع الطلب')
                    ->placeholder('—'),
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
            ->recordActions([
                Action::make('open')
                    ->label('فتح الطلب')
                    ->icon('heroicon-o-arrow-left')
                    ->color('gray')
                    ->url(fn (ContactRequest $record): string => ContactRequestResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (): bool => (bool) auth()->user()?->can(Permissions::MANAGE_CONTACT_REQUESTS)),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
