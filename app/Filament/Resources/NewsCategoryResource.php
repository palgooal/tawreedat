<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsCategoryResource\Pages;
use App\Models\NewsCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class NewsCategoryResource extends Resource
{
    protected static ?string $model = NewsCategory::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bookmark';

    protected static string | UnitEnum | null $navigationGroup = 'المحتوى';

    protected static ?string $navigationLabel = 'التصنيفات الإخبارية';

    protected static ?string $modelLabel = 'تصنيف خبري';

    protected static ?string $pluralModelLabel = 'التصنيفات الإخبارية';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('view content') || $user?->can('manage news categories'));
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->can('manage news categories');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage news categories');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage news categories');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->can('manage news categories');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التصنيف')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم التصنيف')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? '', '-', null));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('الرابط المختصر (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم التصنيف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('news_count')
                    ->label('عدد الأخبار')
                    ->counts('news'),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
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
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsCategories::route('/'),
            'create' => Pages\CreateNewsCategory::route('/create'),
            'edit' => Pages\EditNewsCategory::route('/{record}/edit'),
        ];
    }
}
