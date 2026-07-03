<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CompanyRegistrationRequestResource;
use App\Models\CompanyRegistrationRequest;
use App\Support\Permissions;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * "آخر طلبات تسجيل الشركات" — the 5 most recent "سجّل شركتك" requests,
 * with a quick link into the edit form. Gated to whoever can see
 * CompanyRegistrationRequestResource (Super Admin, Admin, Support).
 */
class LatestCompanyRegistrationRequestsWidget extends TableWidget
{
    protected static ?int $sort = -18;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can(Permissions::VIEW_REGISTRATION_REQUESTS) || $user?->can(Permissions::MANAGE_REGISTRATION_REQUESTS));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('آخر طلبات تسجيل الشركات')
            ->query(CompanyRegistrationRequest::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('company_name')
                    ->label('اسم الشركة')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('contact_name')
                    ->label('مسؤول التواصل')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CompanyRegistrationRequest::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        CompanyRegistrationRequest::STATUS_PENDING => 'info',
                        CompanyRegistrationRequest::STATUS_APPROVED => 'success',
                        CompanyRegistrationRequest::STATUS_REJECTED => 'danger',
                        CompanyRegistrationRequest::STATUS_CONTACTED => 'warning',
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
                    ->url(fn (CompanyRegistrationRequest $record): string => CompanyRegistrationRequestResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (): bool => (bool) auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS)),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
