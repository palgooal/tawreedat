<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class SiteSettings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'إعدادات الموقع';

    protected static ?string $title = 'إعدادات الموقع';

    protected string $view = 'filament-panels::pages.page';

    /**
     * Gates both navigation visibility and direct-URL access to this page.
     * Site Settings controls site-wide SEO/robots/analytics behaviour, so
     * it's kept to a dedicated permission rather than the broader
     * is_admin/panel-role check every other page allows.
     */
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('manage settings');
    }

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    /**
     * The single source of truth for every setting this page manages: its
     * form group (matches SiteSetting.group), its stored value `type`
     * (used by SiteSetting::get() to cast booleans back correctly), and a
     * sensible empty default so a brand-new install still renders/saves
     * cleanly before the seeder has ever run.
     *
     * @var array<string, array{group: string, type: string, default: mixed}>
     */
    private const FIELDS = [
        'site_name' => ['group' => 'site', 'type' => 'string', 'default' => 'توريدات'],
        'site_description' => ['group' => 'site', 'type' => 'string', 'default' => null],
        'site_keywords' => ['group' => 'site', 'type' => 'string', 'default' => null],
        'contact_email' => ['group' => 'site', 'type' => 'string', 'default' => null],
        'contact_phone' => ['group' => 'site', 'type' => 'string', 'default' => null],
        'contact_address' => ['group' => 'site', 'type' => 'string', 'default' => null],
        'default_seo_title' => ['group' => 'seo', 'type' => 'string', 'default' => null],
        'default_seo_description' => ['group' => 'seo', 'type' => 'string', 'default' => null],
        'google_analytics_id' => ['group' => 'seo', 'type' => 'string', 'default' => null],
        'google_search_console_verification' => ['group' => 'seo', 'type' => 'string', 'default' => null],
        'default_og_image' => ['group' => 'og', 'type' => 'image', 'default' => null],
        'facebook_url' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'x_url' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'linkedin_url' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'instagram_url' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'robots_indexing_enabled' => ['group' => 'robots', 'type' => 'boolean', 'default' => false],
        'robots_extra_rules' => ['group' => 'robots', 'type' => 'string', 'default' => null],
    ];

    public function mount(): void
    {
        $values = [];

        foreach (self::FIELDS as $key => $meta) {
            $values[$key] = SiteSetting::get($key, $meta['default']);
        }

        $this->form->fill($values);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الموقع')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')
                            ->label('اسم الموقع')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_keywords')
                            ->label('الكلمات المفتاحية')
                            ->helperText('كلمات مفصولة بفواصل، تُستخدم كقيمة افتراضية لوسم meta keywords.')
                            ->maxLength(500),
                        Textarea::make('site_description')
                            ->label('وصف الموقع')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('contact_email')
                            ->label('البريد الإلكتروني للتواصل')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('contact_phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(50),
                        Textarea::make('contact_address')
                            ->label('العنوان')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
                Section::make('SEO عام')
                    ->description('هذه القيم تُستخدم كقيم افتراضية إذا لم تكن الصفحة تملك SEO خاصاً بها.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('default_seo_title')
                            ->label('عنوان SEO الافتراضي')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('default_seo_description')
                            ->label('وصف SEO الافتراضي')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('google_analytics_id')
                            ->label('معرّف Google Analytics')
                            ->helperText('مثال: G-XXXXXXXXXX. يُترك فارغاً لتعطيل التحليلات.')
                            ->maxLength(50),
                        TextInput::make('google_search_console_verification')
                            ->label('رمز تحقق Google Search Console')
                            ->helperText('محتوى وسم meta name="google-site-verification" فقط.')
                            ->maxLength(255),
                    ]),
                Section::make('Open Graph')
                    ->description('الصورة الافتراضية التي تظهر عند مشاركة روابط الموقع على وسائل التواصل.')
                    ->schema([
                        FileUpload::make('default_og_image')
                            ->label('صورة المشاركة الافتراضية')
                            ->image()
                            ->disk('public')
                            ->directory('site/og')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
                Section::make('روابط التواصل')
                    ->columns(2)
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('فيسبوك')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('x_url')
                            ->label('X (تويتر)')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('linkedin_url')
                            ->label('لينكدإن')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('instagram_url')
                            ->label('إنستغرام')
                            ->url()
                            ->maxLength(255),
                    ]),
                Section::make('إعدادات Robots')
                    ->schema([
                        Toggle::make('robots_indexing_enabled')
                            ->label('السماح لمحركات البحث بفهرسة الموقع')
                            ->helperText('تعطيل الفهرسة مفيد أثناء التطوير فقط. يجب تفعيله قبل الإطلاق الفعلي للموقع.')
                            ->default(false),
                        Textarea::make('robots_extra_rules')
                            ->label('قواعد إضافية لملف robots.txt')
                            ->helperText('تُضاف كما هي في نهاية ملف robots.txt (مثال: تعليمات Disallow لمسارات معينة).')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('حفظ الإعدادات')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (self::FIELDS as $key => $meta) {
            SiteSetting::set(
                $key,
                $data[$key] ?? null,
                $meta['group'],
                $meta['type'],
            );
        }

        Notification::make()
            ->title('تم حفظ إعدادات الموقع')
            ->success()
            ->send();
    }
}
