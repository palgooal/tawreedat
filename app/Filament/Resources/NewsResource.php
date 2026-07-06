<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | UnitEnum | null $navigationGroup = 'المحتوى';

    protected static ?string $navigationLabel = 'الأخبار';

    protected static ?string $modelLabel = 'خبر';

    protected static ?string $pluralModelLabel = 'الأخبار';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('view content') || $user?->can('manage news'));
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->can('manage news');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage news');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage news');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->can('manage news');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            Section::make('بيانات الخبر')
                                ->description('العنوان والرابط المختصر ومقتطف مختصر يظهر في القوائم')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    TextInput::make('title')
                                        ->label('العنوان')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                            if ($operation === 'create') {
                                                $set('slug', Str::slug($state ?? '', '-', null));
                                            }
                                        })
                                        ->columnSpanFull(),
                                    TextInput::make('slug')
                                        ->label('الرابط المختصر (Slug)')
                                        ->helperText('يُنشأ تلقائياً من العنوان، ويمكن تعديله يدوياً')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->columnSpanFull(),
                                    Textarea::make('excerpt')
                                        ->label('مقتطف')
                                        ->helperText('نص قصير يظهر في قائمة الأخبار ونتائج المشاركة (اختياري)')
                                        ->maxLength(300)
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),
                            Section::make('محتوى الخبر')
                                ->description('نص الخبر كاملاً بكل تفاصيله')
                                ->icon('heroicon-o-pencil-square')
                                ->schema([
                                    RichEditor::make('content')
                                        ->label('المحتوى')
                                        ->hiddenLabel()
                                        ->extraInputAttributes([
                                            'style' => 'min-height: 30rem',
                                            'aria-label' => 'محتوى الخبر بالتفصيل',
                                        ])
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['lg' => 2]),
                        Group::make([
                            Section::make('حالة النشر')
                                ->icon('heroicon-o-globe-alt')
                                ->schema([
                                    Select::make('status')
                                        ->label('الحالة')
                                        ->options([
                                            'draft' => 'مسودة',
                                            'published' => 'منشور',
                                        ])
                                        ->default('draft')
                                        ->required(),
                                    DateTimePicker::make('published_at')
                                        ->label('تاريخ النشر')
                                        ->seconds(false)
                                        ->default(now()),
                                ]),
                            Section::make('التصنيف')
                                ->icon('heroicon-o-tag')
                                ->schema([
                                    Select::make('news_category_id')
                                        ->label('التصنيف')
                                        ->relationship('categoryRelation', 'name')
                                        ->native()
                                        ->required(),
                                ]),
                            Section::make('الصورة الرئيسية')
                                ->icon('heroicon-o-photo')
                                ->schema([
                                    FileUpload::make('image')
                                        ->label('الصورة الرئيسية للخبر')
                                        ->image()
                                        ->disk('public')
                                        ->directory('news/images')
                                        ->visibility('public')
                                        ->imageEditor()
                                        ->imagePreviewHeight('180')
                                        ->maxSize(10240)
                                        ->helperText('الحد الأقصى 10 ميجابايت (JPG, PNG, WEBP)')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['lg' => 1]),
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
                    ->sortable()
                    ->limit(50),
                TextColumn::make('categoryRelation.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d')
                    ->sortable(),
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
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                    ]),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('معاينة')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (News $record): string => $record->publicUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (News $record): bool => $record->isPubliclyVisible())
                    ->tooltip('فتح الخبر في الموقع'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
