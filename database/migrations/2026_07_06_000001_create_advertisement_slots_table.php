<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The six well-known advertising slots the frontend renders. Seeded
     * directly inside this migration (not only via db:seed) so that a bare
     * `php artisan migrate` on a fresh or existing install always leaves the
     * slots table populated before the next migration maps legacy
     * `position` values onto them.
     *
     * @var array<int, array{key: string, name: string, description: string|null, width: int|null, height: int|null}>
     */
    public static array $slots = [
        [
            'key' => 'header_banner',
            'name' => 'بانر أعلى الموقع',
            'description' => 'يظهر أعلى كل صفحات الموقع، أسفل الشريط العلوي مباشرة.',
            'width' => 960,
            'height' => 154,
        ],
        [
            'key' => 'home_banner_1',
            'name' => 'إعلان الصفحة الرئيسية (1)',
            'description' => 'المساحة الإعلانية الرئيسية الكبيرة ضمن قسم "مساحات إعلانية" بالصفحة الرئيسية.',
            'width' => 1600,
            'height' => 900,
        ],
        [
            'key' => 'home_banner_2',
            'name' => 'إعلان الصفحة الرئيسية (2)',
            'description' => 'أول بطاقة إعلانية ضمن قسم "مساحات إعلانية" بالصفحة الرئيسية.',
            'width' => 1600,
            'height' => 900,
        ],
        [
            'key' => 'home_banner_3',
            'name' => 'إعلان الصفحة الرئيسية (3)',
            'description' => 'ثاني بطاقة إعلانية ضمن قسم "مساحات إعلانية" بالصفحة الرئيسية.',
            'width' => 320,
            'height' => 213,
        ],
        [
            'key' => 'news_sidebar',
            'name' => 'الشريط الجانبي للأخبار',
            'description' => 'يظهر في الشريط الجانبي لصفحة تفاصيل الخبر.',
            'width' => 320,
            'height' => 480,
        ],
        [
            'key' => 'news_footer',
            'name' => 'أسفل الخبر',
            'description' => 'يظهر أسفل محتوى الخبر في صفحة تفاصيل الخبر.',
            'width' => 728,
            'height' => 200,
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advertisement_slots', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        foreach (self::$slots as $slot) {
            DB::table('advertisement_slots')->updateOrInsert(
                ['key' => $slot['key']],
                [
                    'name' => $slot['name'],
                    'description' => $slot['description'],
                    'width' => $slot['width'],
                    'height' => $slot['height'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_slots');
    }
};
