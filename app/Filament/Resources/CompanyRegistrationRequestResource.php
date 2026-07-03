<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyRegistrationRequestResource\Pages;
use App\Models\Company;
use App\Models\CompanyRegistrationRequest;
use App\Models\User;
use App\Notifications\CompanyRegistrationRequestApprovedNotification;
use App\Support\Permissions;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;
use UnitEnum;

/**
 * "سجّل شركتك" is a request/review workflow, not a self-serve signup or
 * paid plan - see docs/DECISIONS.md. Approving a request now creates (or
 * updates) a real `Company` record - see resolveOrCreateCompany()/approve()
 * below - so it *will* start appearing in the homepage's active-companies
 * section, but it does NOT publish a dedicated public company profile page
 * (Companies Directory is still deferred to Phase 2, see docs/ROADMAP.md)
 * and does NOT create a company-owner account. Payment/collection, if any,
 * happens entirely outside this panel (WhatsApp/phone/email).
 */
class CompanyRegistrationRequestResource extends Resource
{
    protected static ?string $model = CompanyRegistrationRequest::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string | UnitEnum | null $navigationGroup = 'الطلبات';

    protected static ?string $navigationLabel = 'طلبات تسجيل الشركات';

    protected static ?string $modelLabel = 'طلب تسجيل شركة';

    protected static ?string $pluralModelLabel = 'طلبات تسجيل الشركات';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can(Permissions::VIEW_REGISTRATION_REQUESTS) || $user?->can(Permissions::MANAGE_REGISTRATION_REQUESTS));
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS);
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS);
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الشركة المرسلة')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company_name')
                            ->label('اسم الشركة')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_name')
                            ->label('اسم مسؤول التواصل')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('رقم الجوال')
                            ->tel()
                            ->required()
                            ->maxLength(50),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                        Select::make('city_id')
                            ->label('المدينة')
                            ->relationship('cityRelation', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText(fn (?CompanyRegistrationRequest $record): ?string => $record?->city ? "النص الأصلي المُرسل: {$record->city}" : null),
                        Select::make('category_id')
                            ->label('التصنيف / مجال العمل')
                            ->relationship('categoryRelation', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText(fn (?CompanyRegistrationRequest $record): ?string => $record?->category ? "النص الأصلي المُرسل: {$record->category}" : null),
                        TextInput::make('website')
                            ->label('الموقع الإلكتروني')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('logo')
                            ->label('شعار الشركة')
                            ->image()
                            ->disk('public')
                            ->directory('company-registration-logos')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('وصف مختصر عن الشركة')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('ملاحظات إضافية (من مقدّم الطلب)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('المراجعة الإدارية')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options(CompanyRegistrationRequest::STATUSES)
                            ->default(CompanyRegistrationRequest::STATUS_PENDING)
                            ->required(),
                        Textarea::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->rows(3)
                            ->columnSpanFull(),
                        Placeholder::make('reviewed_by_display')
                            ->label('تمت المراجعة بواسطة')
                            ->content(fn (?CompanyRegistrationRequest $record): string => $record?->reviewedBy?->name ?? '—'),
                        Placeholder::make('reviewed_at_display')
                            ->label('تاريخ المراجعة')
                            ->content(fn (?CompanyRegistrationRequest $record): string => $record?->reviewed_at?->format('Y-m-d H:i') ?? '—'),
                        Placeholder::make('company_link_display')
                            ->label('سجل الشركة')
                            ->columnSpanFull()
                            ->content(function (?CompanyRegistrationRequest $record) {
                                if (! $record?->company_id) {
                                    return 'لم يتم إنشاء شركة بعد — تُنشأ تلقائياً عند الضغط على "قبول".';
                                }

                                return new HtmlString(sprintf(
                                    '<a href="%s" class="text-primary-600 underline" target="_blank">%s ←</a>',
                                    CompanyResource::getUrl('edit', ['record' => $record->company_id]),
                                    e($record->company?->name ?? 'فتح سجل الشركة')
                                ));
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('الشعار')
                    ->disk('public')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('company_name')
                    ->label('اسم الشركة')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('contact_name')
                    ->label('مسؤول التواصل')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('الجوال')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('cityRelation.name')
                    ->label('المدينة')
                    ->placeholder('—'),
                TextColumn::make('categoryRelation.name')
                    ->label('التصنيف')
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
                    ->label('تاريخ الإرسال')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(CompanyRegistrationRequest::STATUSES),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('سيتم إنشاء أو تحديث سجل شركة مرتبط بهذا الطلب.')
                    ->visible(fn (CompanyRegistrationRequest $record): bool => ! $record->isApproved()
                        && auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS)
                        && auth()->user()?->can(Permissions::MANAGE_COMPANIES))
                    ->action(fn (CompanyRegistrationRequest $record) => static::approve($record)),
                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CompanyRegistrationRequest $record): bool => ! $record->isRejected()
                        && auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS))
                    ->action(fn (CompanyRegistrationRequest $record) => static::markAs($record, CompanyRegistrationRequest::STATUS_REJECTED)),
                Action::make('markContacted')
                    ->label('تم التواصل')
                    ->icon('heroicon-o-phone')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (CompanyRegistrationRequest $record): bool => ! $record->isContacted()
                        && auth()->user()?->can(Permissions::MANAGE_REGISTRATION_REQUESTS))
                    ->action(fn (CompanyRegistrationRequest $record) => static::markAs($record, CompanyRegistrationRequest::STATUS_CONTACTED)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Shared status-transition logic for the reject/markContacted row
     * actions: update the status and stamp who reviewed it and when.
     * Deliberately does NOT touch Company - only approve() does that
     * (see approve() below). No public profile is published, no
     * company-owner account is created, no payment is recorded either way
     * (see the class-level docblock).
     */
    private static function markAs(CompanyRegistrationRequest $record, string $status): void
    {
        $record->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);
    }

    /**
     * Approving a request converts it into a real Company record. This is
     * the one place in the whole "سجّل شركتك" flow that touches the
     * `companies` table - see resolveOrCreateCompany() for how it avoids
     * creating duplicates on a repeat approval.
     *
     * Deliberately does NOT set is_verified/is_featured/status on an
     * *existing* Company being re-linked - those are admin-curated flags on
     * the Company itself and shouldn't be silently reset by a registration
     * request being approved again. They're only set (to false/false/
     * 'active') the first time a Company is created from a request.
     */
    private static function approve(CompanyRegistrationRequest $record): void
    {
        $company = static::resolveOrCreateCompany($record);
        $isNewCompany = ! $company->exists;

        $company->fill([
            'name' => $record->company_name,
            'description' => $record->description,
            'website' => $record->website,
            'phone' => $record->phone,
            'email' => $record->email,
            'city_id' => $record->city_id,
            'category_id' => $record->category_id,
        ]);

        if (filled($record->logo)) {
            $company->logo = $record->logo;
        }

        if ($isNewCompany) {
            $company->is_verified = false;
            $company->is_featured = false;
            $company->status = 'active';
        }

        $company->save();

        $record->update([
            'company_id' => $company->id,
            'status' => CompanyRegistrationRequest::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        static::notifyAdminsOfApproval($record);
    }

    /**
     * Informational-only notice to admin-capable users that a request was
     * converted into a Company. Failure here must never undo or block the
     * approval that already happened above - same failure-isolation
     * contract as CompanyRegistrationRequestController::notifyAdmins().
     */
    private static function notifyAdminsOfApproval(CompanyRegistrationRequest $record): void
    {
        try {
            $recipients = User::query()->get()->filter(
                fn (User $user): bool => $user->is_admin
                    || $user->can(Permissions::VIEW_REGISTRATION_REQUESTS)
                    || $user->can(Permissions::MANAGE_REGISTRATION_REQUESTS)
            );

            if ($recipients->isEmpty()) {
                return;
            }

            Notification::send($recipients, new CompanyRegistrationRequestApprovedNotification($record));
        } catch (Throwable $e) {
            Log::error('Failed to send company registration approval notification.', [
                'company_registration_request_id' => $record->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Finds the Company this request should create/update:
     *   1. If the request is already linked (company_id set - e.g. a
     *      previously-approved request being re-approved after its status
     *      was manually reset), reuse that exact Company. This is what
     *      guarantees repeated approval never duplicates a Company.
     *   2. Otherwise, fall back to matching an existing Company by the slug
     *      the company_name would generate, or by email, in case a company
     *      was already onboarded another way.
     *   3. Otherwise, a fresh, unsaved Company instance.
     */
    private static function resolveOrCreateCompany(CompanyRegistrationRequest $record): Company
    {
        if ($record->company_id) {
            $existing = Company::query()->find($record->company_id);

            if ($existing) {
                return $existing;
            }
        }

        $slug = Str::slug($record->company_name, '-', null);

        $existing = Company::query()
            ->where('slug', $slug)
            ->when(filled($record->email), fn ($query) => $query->orWhere('email', $record->email))
            ->first();

        return $existing ?? new Company();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyRegistrationRequests::route('/'),
            'create' => Pages\CreateCompanyRegistrationRequest::route('/create'),
            'edit' => Pages\EditCompanyRegistrationRequest::route('/{record}/edit'),
        ];
    }
}
