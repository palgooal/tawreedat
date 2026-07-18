<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerLogoResource\Pages;
use App\Models\PartnerLogo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class PartnerLogoResource extends Resource
{
    protected static ?string $model = PartnerLogo::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string | UnitEnum | null $navigationGroup = 'المحتوى';

    protected static ?string $navigationLabel = 'شعارات الشركاء';

    protected static ?string $modelLabel = 'شعار شريك';

    protected static ?string $pluralModelLabel = 'شعارات الشركاء';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الشعار')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم الشريك')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('logo')
                            ->label('الشعار')
                            ->image()
                            ->disk('public')
                            ->directory('partner-logos')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('المقاس الموصى به: 300×120px تقريبًا (نسبة عرض إلى ارتفاع حوالي 2.5:1)، بخلفية شفافة PNG أو SVG، بدون هامش فارغ داخل الصورة نفسها.'),
                        TextInput::make('sort_order')
                            ->label('ترتيب الظهور')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('الأصغر يظهر أولاً داخل الكاروسيل.'),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->helperText('الشعارات غير النشطة لا تظهر في كاروسيل الصفحة الرئيسية.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('الشعار')
                    ->disk('public'),
                TextColumn::make('name')
                    ->label('اسم الشريك')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('نشط'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerLogos::route('/'),
            'create' => Pages\CreatePartnerLogo::route('/create'),
            'edit' => Pages\EditPartnerLogo::route('/{record}/edit'),
        ];
    }
}
