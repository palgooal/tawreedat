<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | UnitEnum | null $navigationGroup = 'المحتوى';

    protected static ?string $navigationLabel = 'الصفحات';

    protected static ?string $modelLabel = 'صفحة';

    protected static ?string $pluralModelLabel = 'الصفحات';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('view content') || $user?->can('manage pages'));
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->can('manage pages');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage pages');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->can('manage pages');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->can('manage pages');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الصفحة')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? '', '-', null));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('الرابط المختصر (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'published' => 'منشورة',
                            ])
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('تاريخ النشر')
                            ->columnSpanFull(),
                    ]),
                Section::make('محتوى الهيدر')
                    ->description('يظهر في الجزء العلوي (Hero) من الصفحة. إن تُرك فارغاً، يُستخدم العنوان والمقتطف كبديل.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('عنوان الهيدر')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('hero_description')
                            ->label('وصف الهيدر')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->label('مقتطف')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('المحتوى')
                    ->schema([
                        RichEditor::make('content')
                            ->label('محتوى الصفحة')
                            ->columnSpanFull(),
                    ]),
                Section::make('تحسين محركات البحث (SEO)')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('عنوان SEO')
                            ->maxLength(255),
                        TextInput::make('seo_description')
                            ->label('وصف SEO')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشورة',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشورة',
                    ]),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('معاينة')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Page $record): string => $record->publicUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (Page $record): bool => $record->isPubliclyVisible())
                    ->tooltip('فتح الصفحة في الموقع'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
