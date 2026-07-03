<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'المستخدمون';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمون';

    /**
     * User management is Super Admin-only, full stop. This single check
     * governs whether the resource appears in navigation at all
     * (Filament calls canViewAny() to decide that) and whether the
     * index/list page itself can be reached directly by URL.
     */
    public static function canViewAny(): bool
    {
        return static::actorIsSuperAdmin();
    }

    public static function canCreate(): bool
    {
        return static::actorIsSuperAdmin();
    }

    /**
     * Only a Super Admin may edit any user record. This alone already
     * guarantees a non-Super-Admin can never edit a Super Admin (they
     * can't edit anyone through this resource at all) - see also the
     * per-row ->visible() checks in table()/Pages\EditUser for a second,
     * independent layer of the same rule.
     */
    public static function canEdit(Model $record): bool
    {
        return static::actorIsSuperAdmin();
    }

    /**
     * Same Super-Admin-only gate as canEdit(), plus: nobody may delete
     * their own account, even a Super Admin.
     */
    public static function canDelete(Model $record): bool
    {
        if (! static::actorIsSuperAdmin()) {
            return false;
        }

        return static::actorIsNot($record);
    }

    public static function canDeleteAny(): bool
    {
        return static::actorIsSuperAdmin();
    }

    private static function actorIsSuperAdmin(): bool
    {
        return (bool) auth()->user()?->hasRole(User::ROLE_SUPER_ADMIN);
    }

    private static function actorIsNot(Model $record): bool
    {
        $actor = auth()->user();

        return $actor === null || $actor->isNot($record);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المستخدم')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->maxLength(255)
                            ->helperText('اتركها فارغة عند التعديل للإبقاء على كلمة المرور الحالية.'),
                        DateTimePicker::make('email_verified_at')
                            ->label('تاريخ تفعيل البريد الإلكتروني'),
                    ]),
                Section::make('الصلاحيات')
                    ->columns(2)
                    ->schema([
                        Select::make('roles')
                            ->label('الأدوار')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Toggle::make('is_admin')
                            ->label('مسؤول (صلاحية توافقية قديمة)')
                            ->helperText(
                                'صلاحية توافقية قديمة تسبق نظام الأدوار وتُبقي فقط الحسابات القديمة قادرة على الدخول. '
                                .'يُفضّل تعيين دور بدلاً منها لأي حساب جديد - راجع docs/DECISIONS.md.'
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('الأدوار')
                    ->badge()
                    ->separator(',')
                    ->placeholder('بدون دور'),
                IconColumn::make('is_admin')
                    ->label('مسؤول (توافقية)')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (User $record): bool => static::actorIsSuperAdmin()),
                DeleteAction::make()
                    ->visible(fn (User $record): bool => static::actorIsSuperAdmin() && static::actorIsNot($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        // Per-record authorization, not just *Any(): without
                        // this, the self-delete guard would only be checked
                        // once for the whole batch instead of once per
                        // selected row, and a Super Admin could delete their
                        // own account by selecting it alongside others. The
                        // closure reuses canDelete() directly rather than a
                        // Gate ability string, since no Policy is registered
                        // for User and an undefined Gate ability denies by
                        // default (which would silently block every bulk
                        // delete, not just self-delete).
                        ->authorizeIndividualRecords(fn (Model $record): bool => static::canDelete($record)),
                ])->visible(fn (): bool => static::actorIsSuperAdmin()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
