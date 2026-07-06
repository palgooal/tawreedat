<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementSlotResource\Pages;
use App\Models\AdvertisementSlot;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AdvertisementSlotResource extends Resource
{
    protected static ?string $model = AdvertisementSlot::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-view-columns';

    protected static string | UnitEnum | null $navigationGroup = 'الإعلانات';

    protected static ?string $navigationLabel = 'مساحات الإعلانات';

    protected static ?string $modelLabel = 'مساحة إعلانية';

    protected static ?string $pluralModelLabel = 'مساحات الإعلانات';

    protected static ?int $navigationSort = -1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('view ads') || $user?->can('manage ads'));
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->can('manage ads');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage ads');
    }

    public static function canDelete(Model $record): bool
    {
        // Slot keys are hardcoded into the Blade views (see
        // AppServiceProvider / AdvertisementManager) — deleting one from
        // here would silently break a real spot on the site, so this
        // resource intentionally offers no delete action anywhere.
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المساحة الإعلانية')
                    ->description('المفاتيح (key) ثابتة ويعتمد عليها الموقع مباشرة — لا تُنشئ مساحات جديدة إلا بالتنسيق مع فريق التطوير.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('key')
                            ->label('المفتاح (key)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('يُستخدم من الكود مباشرة لعرض الإعلان في مكانه الصحيح، مثال: home_banner_1'),
                        Toggle::make('is_active')
                            ->label('مفعّلة')
                            ->default(true)
                            ->helperText('عند التعطيل لن يظهر أي إعلان في هذه المساحة حتى لو كان هناك إعلان نشط مرتبط بها.'),
                        TextInput::make('width')
                            ->label('العرض المقترح (px)')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('height')
                            ->label('الارتفاع المقترح (px)')
                            ->numeric()
                            ->minValue(1),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(2)
                            ->columnSpanFull(),
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
                TextColumn::make('key')
                    ->label('المفتاح')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('dimensions')
                    ->label('الأبعاد')
                    ->state(fn (AdvertisementSlot $record): string => $record->width && $record->height
                        ? "{$record->width} × {$record->height}"
                        : '—'),
                TextColumn::make('advertisements_count')
                    ->label('عدد الإعلانات')
                    ->counts('advertisements')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('مفعّلة')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('مفعّلة'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvertisementSlots::route('/'),
            'create' => Pages\CreateAdvertisementSlot::route('/create'),
            'edit' => Pages\EditAdvertisementSlot::route('/{record}/edit'),
        ];
    }
}
