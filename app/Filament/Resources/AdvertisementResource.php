<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\Advertisement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | UnitEnum | null $navigationGroup = 'الإعلانات';

    protected static ?string $navigationLabel = 'الإعلانات';

    protected static ?string $modelLabel = 'إعلان';

    protected static ?string $pluralModelLabel = 'الإعلانات';

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
        return (bool) auth()->user()?->can('manage ads');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->can('manage ads');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الإعلان')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('link')
                            ->label('الرابط')
                            ->url()
                            ->maxLength(255)
                            ->helperText('روابط الموقع تستخدم هذا الرابط عبر إعادة توجيه تُحصي النقرات تلقائياً.'),
                        Select::make('advertisement_slot_id')
                            ->label('المساحة الإعلانية')
                            ->relationship('slot', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('المكان الذي سيظهر فيه هذا الإعلان على الموقع.'),
                        TextInput::make('priority')
                            ->label('الأولوية')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('عند وجود أكثر من إعلان نشط لنفس المساحة، يُعرض الأعلى أولوية.'),
                        DateTimePicker::make('starts_at')
                            ->label('تاريخ البدء'),
                        DateTimePicker::make('ends_at')
                            ->label('تاريخ الانتهاء'),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                        FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->disk('public')
                            ->directory('advertisements')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->disk('public'),
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slot.name')
                    ->label('المساحة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('تاريخ البدء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('تاريخ الانتهاء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('الأولوية')
                    ->sortable(),
                TextColumn::make('views')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('clicks')
                    ->label('النقرات')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('ctr')
                    ->label('CTR')
                    ->state(fn (Advertisement $record): string => $record->ctr === null ? '—' : "{$record->ctr}%")
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('نشط'),
                SelectFilter::make('advertisement_slot_id')
                    ->label('المساحة')
                    ->relationship('slot', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'edit' => Pages\EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
