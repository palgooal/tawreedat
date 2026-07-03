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
use UnitEnum;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | UnitEnum | null $navigationGroup = 'الإعلانات';

    protected static ?string $navigationLabel = 'الإعلانات';

    protected static ?string $modelLabel = 'إعلان';

    protected static ?string $pluralModelLabel = 'الإعلانات';

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
                            ->maxLength(255),
                        Select::make('position')
                            ->label('الموضع')
                            ->options([
                                'header' => 'أعلى الصفحة',
                                'sidebar' => 'الشريط الجانبي',
                                'footer' => 'أسفل الصفحة',
                                'home' => 'الصفحة الرئيسية',
                            ]),
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
                    ->label('الصورة'),
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('الموضع')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('تاريخ البدء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('تاريخ الانتهاء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('نشط'),
                SelectFilter::make('position')
                    ->label('الموضع')
                    ->options([
                        'header' => 'أعلى الصفحة',
                        'sidebar' => 'الشريط الجانبي',
                        'footer' => 'أسفل الصفحة',
                        'home' => 'الصفحة الرئيسية',
                    ]),
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
