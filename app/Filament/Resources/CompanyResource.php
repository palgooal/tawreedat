<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | UnitEnum | null $navigationGroup = 'إدارة الشركات';

    protected static ?string $navigationLabel = 'الشركات';

    protected static ?string $modelLabel = 'شركة';

    protected static ?string $pluralModelLabel = 'الشركات';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الشركة')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم الشركة')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('الرابط المختصر (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('city_id')
                            ->label('المدينة')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('category_id')
                            ->label('التصنيف')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('website')
                            ->label('الموقع الإلكتروني')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                    ]),
                Section::make('الوصف والشعار')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label('الشعار')
                            ->image()
                            ->directory('companies/logos')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                Section::make('الحالة')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'قيد المراجعة',
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'rejected' => 'مرفوض',
                            ])
                            ->default('pending')
                            ->required(),
                        Toggle::make('is_verified')
                            ->label('موثقة'),
                        Toggle::make('is_featured')
                            ->label('مميزة'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('الشعار')
                    ->circular(),
                TextColumn::make('name')
                    ->label('اسم الشركة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city.name')
                    ->label('المدينة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                IconColumn::make('is_verified')
                    ->label('موثقة')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('مميزة')
                    ->boolean(),
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
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد المراجعة',
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'rejected' => 'مرفوض',
                    ]),
                TernaryFilter::make('is_verified')
                    ->label('موثقة'),
                TernaryFilter::make('is_featured')
                    ->label('مميزة'),
                SelectFilter::make('city_id')
                    ->label('المدينة')
                    ->relationship('city', 'name')
                    ->searchable(),
                SelectFilter::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name')
                    ->searchable(),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
