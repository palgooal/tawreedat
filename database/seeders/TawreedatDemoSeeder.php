<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\ContactRequest;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TawreedatDemoSeeder extends Seeder
{
    /**
     * Seed realistic Arabic demo data for the Tawreedat platform.
     *
     * Safe to run multiple times: every record is matched by its slug
     * (or another natural unique key) via firstOrCreate/updateOrCreate.
     */
    public function run(): void
    {
        $cities = $this->seedCities();
        $categories = $this->seedCategories();
        $this->seedCompanies($cities, $categories);
        $this->seedNews();
        $this->seedAdvertisements();
        $this->seedContactRequests();
    }

    /**
     * @return array<string, int> city name => id
     */
    private function seedCities(): array
    {
        $names = ['الرياض', 'جدة', 'الدمام', 'مكة', 'المدينة', 'الخبر', 'القصيم'];

        $ids = [];

        foreach ($names as $name) {
            $city = City::firstOrCreate(
                ['slug' => Str::slug($name, '-', null)],
                ['name' => $name, 'is_active' => true],
            );

            $ids[$name] = $city->id;
        }

        return $ids;
    }

    /**
     * @return array<string, int> category name => id
     */
    private function seedCategories(): array
    {
        $names = [
            'مواد البناء',
            'الخرسانة الجاهزة',
            'الحديد والمعادن',
            'الدهانات والتشطيبات',
            'الكهرباء والإنارة',
            'السباكة والعزل',
            'المعدات والمقاولات',
        ];

        $ids = [];

        foreach ($names as $name) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($name, '-', null)],
                ['name' => $name, 'type' => 'قطاع البناء', 'is_active' => true],
            );

            $ids[$name] = $category->id;
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $cities
     * @param  array<string, int>  $categories
     */
    private function seedCompanies(array $cities, array $categories): void
    {
        $companies = [
            [
                'name' => 'شركة أساس مواد البناء',
                'city' => 'الرياض',
                'category' => 'مواد البناء',
                'phone' => '0112345671',
                'email' => 'info@asas-materials.sa',
                'website' => 'https://asas-materials.sa',
                'description' => 'توريد مواد البناء الأساسية للمشاريع السكنية والتجارية بجودة عالية وأسعار تنافسية.',
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'مؤسسة الديار لمواد البناء',
                'city' => 'جدة',
                'category' => 'مواد البناء',
                'phone' => '0122345672',
                'email' => 'info@aldiyar-materials.sa',
                'website' => 'https://aldiyar-materials.sa',
                'description' => 'مورد معتمد لمواد البناء بمختلف أنواعها لخدمة المقاولين والأفراد في غرب المملكة.',
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'مصنع صبة للخرسانة الجاهزة',
                'city' => 'جدة',
                'category' => 'الخرسانة الجاهزة',
                'phone' => '0122345673',
                'email' => 'info@sabba-concrete.sa',
                'website' => 'https://sabba-concrete.sa',
                'description' => 'خرسانة جاهزة وحلول صب متكاملة للمواقع الإنشائية والمقاولين في المنطقة الغربية.',
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'خرسانة الواحة الجاهزة',
                'city' => 'الدمام',
                'category' => 'الخرسانة الجاهزة',
                'phone' => '0132345674',
                'email' => 'info@alwaha-concrete.sa',
                'website' => 'https://alwaha-concrete.sa',
                'description' => 'إنتاج وتوريد الخرسانة الجاهزة بمختلف المواصفات لمشاريع المنطقة الشرقية.',
                'is_verified' => false,
                'is_featured' => false,
            ],
            [
                'name' => 'حديد العمران للتوريد',
                'city' => 'الدمام',
                'category' => 'الحديد والمعادن',
                'phone' => '0132345675',
                'email' => 'info@omran-steel.sa',
                'website' => 'https://omran-steel.sa',
                'description' => 'توريد حديد التسليح ومنتجات الصلب بكميات مختلفة لمشاريع البناء والمقاولات.',
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'مصنع الفولاذ السعودي للمعادن',
                'city' => 'الخبر',
                'category' => 'الحديد والمعادن',
                'phone' => '0132345676',
                'email' => 'info@saudi-steel.sa',
                'website' => 'https://saudi-steel.sa',
                'description' => 'تصنيع وتوريد منتجات الحديد والمعادن للقطاعين الصناعي والإنشائي.',
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'لمسة للتشطيبات والدهانات',
                'city' => 'الرياض',
                'category' => 'الدهانات والتشطيبات',
                'phone' => '0112345677',
                'email' => 'info@lamsa-finishes.sa',
                'website' => 'https://lamsa-finishes.sa',
                'description' => 'دهانات ومواد تشطيب داخلية وخارجية للمباني السكنية والتجارية.',
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'دار الإتقان للتشطيبات',
                'city' => 'مكة',
                'category' => 'الدهانات والتشطيبات',
                'phone' => '0122345678',
                'email' => 'info@itqan-finishes.sa',
                'website' => null,
                'description' => 'تنفيذ وتوريد مواد التشطيبات الداخلية بأعلى معايير الجودة.',
                'is_verified' => false,
                'is_featured' => false,
            ],
            [
                'name' => 'نوران للكهرباء والإنارة',
                'city' => 'الخبر',
                'category' => 'الكهرباء والإنارة',
                'phone' => '0132345679',
                'email' => 'info@nooran-electric.sa',
                'website' => 'https://nooran-electric.sa',
                'description' => 'منتجات إنارة وكهرباء للمنازل والمشاريع التجارية بمواصفات معتمدة.',
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'مؤسسة الضياء للإنارة والكهرباء',
                'city' => 'المدينة',
                'category' => 'الكهرباء والإنارة',
                'phone' => '0142345680',
                'email' => 'info@aldiya-lighting.sa',
                'website' => 'https://aldiya-lighting.sa',
                'description' => 'توريد مستلزمات الكهرباء والإنارة الداخلية والخارجية للمشاريع.',
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'مانع للعزل والسباكة',
                'city' => 'الرياض',
                'category' => 'السباكة والعزل',
                'phone' => '0112345681',
                'email' => 'info@manea-insulation.sa',
                'website' => 'https://manea-insulation.sa',
                'description' => 'مواد عزل مائي وحراري ومستلزمات سباكة للمواقع الإنشائية والتشطيبات.',
                'is_verified' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'الينابيع للسباكة والعزل',
                'city' => 'القصيم',
                'category' => 'السباكة والعزل',
                'phone' => '0162345682',
                'email' => 'info@yanabee-plumbing.sa',
                'website' => null,
                'description' => 'حلول سباكة وعزل متكاملة للمشاريع السكنية والزراعية في منطقة القصيم.',
                'is_verified' => false,
                'is_featured' => false,
            ],
            [
                'name' => 'روافع لمعدات البناء',
                'city' => 'الدمام',
                'category' => 'المعدات والمقاولات',
                'phone' => '0132345683',
                'email' => 'info@rawafea-equipment.sa',
                'website' => 'https://rawafea-equipment.sa',
                'description' => 'تأجير وتوريد معدات وأدوات البناء للمقاولين ومواقع العمل.',
                'is_verified' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'مداد للمقاولات العامة',
                'city' => 'القصيم',
                'category' => 'المعدات والمقاولات',
                'phone' => '0162345684',
                'email' => 'info@midad-contracting.sa',
                'website' => 'https://midad-contracting.sa',
                'description' => 'تنفيذ وإدارة أعمال المقاولات العامة للمشاريع السكنية والتجارية.',
                'is_verified' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['slug' => Str::slug($company['name'], '-', null)],
                [
                    'name' => $company['name'],
                    'logo' => null,
                    'description' => $company['description'],
                    'website' => $company['website'],
                    'phone' => $company['phone'],
                    'email' => $company['email'],
                    'city_id' => $cities[$company['city']],
                    'category_id' => $categories[$company['category']],
                    'is_verified' => $company['is_verified'],
                    'is_featured' => $company['is_featured'],
                    'status' => 'active',
                ],
            );
        }
    }

    private function seedNews(): void
    {
        $items = [
            [
                'title' => 'تحديث أسعار مواد البناء لهذا الأسبوع',
                'category' => 'أسعار',
                'excerpt' => 'رصد لأبرز التغيرات في أسعار مواد البناء الأساسية خلال الأسبوع الحالي.',
                'content' => 'شهدت أسعار بعض مواد البناء الأساسية تغيرات طفيفة هذا الأسبوع، حيث سجلت مواد التشييد الأساسية استقراراً نسبياً مقارنة بالأسابيع الماضية. ننصح المقاولين والموردين بمتابعة السوق بشكل دوري عبر منصة توريد.',
                'days_ago' => 1,
            ],
            [
                'title' => 'ارتفاع الطلب على موردي الخرسانة الجاهزة في المدن الكبرى',
                'category' => 'مشاريع',
                'excerpt' => 'زيادة ملحوظة في طلبات الخرسانة الجاهزة مع تسارع وتيرة المشاريع الإنشائية.',
                'content' => 'أظهرت بيانات القطاع ارتفاعاً في الطلب على موردي الخرسانة الجاهزة في الرياض وجدة والدمام، مدفوعاً بتسارع تنفيذ المشاريع السكنية والتجارية الكبرى خلال الفترة الحالية.',
                'days_ago' => 3,
            ],
            [
                'title' => 'عروض جديدة من شركات الدهانات والتشطيبات',
                'category' => 'عروض',
                'excerpt' => 'باقة من العروض والخصومات المقدمة من موردي الدهانات ومواد التشطيب.',
                'content' => 'أطلقت عدة شركات متخصصة في الدهانات والتشطيبات عروضاً خاصة على منتجاتها لهذا الموسم، وتشمل خصومات على الكميات الكبيرة الموجهة للمقاولين وشركات التطوير العقاري.',
                'days_ago' => 5,
            ],
            [
                'title' => 'انضمام شركات جديدة إلى منصة توريد في قطاع الحديد',
                'category' => 'منصة',
                'excerpt' => 'توسع قائمة الموردين المعتمدين في قطاع الحديد والمعادن على المنصة.',
                'content' => 'رحّبت منصة توريد بانضمام عدد من الموردين الجدد المتخصصين في الحديد والمعادن، في إطار جهودها المستمرة لتوسيع دليل الشركات وتسهيل الوصول إلى موردين موثوقين.',
                'days_ago' => 7,
            ],
            [
                'title' => 'نصائح لاختيار مورد مواد البناء المناسب لمشروعك',
                'category' => 'دليل',
                'excerpt' => 'دليل مختصر يساعدك على اختيار المورد الأنسب من حيث الجودة والسعر وسرعة التوريد.',
                'content' => 'عند اختيار مورد لمواد البناء، من المهم مراجعة سجل الشركة وتوثيقها، ومقارنة الأسعار بين عدة موردين، والتأكد من قدرتها على الالتزام بمواعيد التسليم المتفق عليها.',
                'days_ago' => 10,
            ],
            [
                'title' => 'توسع الطلب على حلول العزل المائي في المشاريع السكنية',
                'category' => 'تقارير',
                'excerpt' => 'اهتمام متزايد بأعمال العزل المائي والحراري ضمن المشاريع السكنية الجديدة.',
                'content' => 'يشهد قطاع العزل المائي والحراري نمواً ملحوظاً مع ازدياد وعي الملاك والمقاولين بأهمية العزل في إطالة عمر المباني وخفض استهلاك الطاقة.',
                'days_ago' => 14,
            ],
            [
                'title' => 'نمو قطاع المقاولات في المملكة خلال العام الحالي',
                'category' => 'قطاع البناء',
                'excerpt' => 'مؤشرات إيجابية لنمو قطاع المقاولات مدفوعة بمشاريع التنمية العمرانية.',
                'content' => 'أشارت تقارير القطاع إلى نمو مستمر في أنشطة المقاولات العامة، مع توسع في عدد الشركات المسجلة ومعدات البناء المتاحة لخدمة المشاريع الكبرى في مختلف مناطق المملكة.',
                'days_ago' => 20,
            ],
        ];

        foreach ($items as $item) {
            News::firstOrCreate(
                ['slug' => Str::slug($item['title'], '-', null)],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'image' => null,
                    'category' => $item['category'],
                    'published_at' => now()->subDays($item['days_ago']),
                    'status' => 'published',
                ],
            );
        }
    }

    private function seedAdvertisements(): void
    {
        $ads = [
            [
                'title' => 'بانر أعلى الصفحة الرئيسي',
                'position' => 'header',
            ],
            [
                'title' => 'بانر الصفحة الرئيسية',
                'position' => 'home',
            ],
            [
                'title' => 'بانر الشريط الجانبي',
                'position' => 'sidebar',
            ],
        ];

        foreach ($ads as $ad) {
            Advertisement::firstOrCreate(
                ['title' => $ad['title']],
                [
                    'image' => null,
                    'link' => '/contact',
                    'position' => $ad['position'],
                    'starts_at' => now()->subDays(2),
                    'ends_at' => now()->addDays(30),
                    'is_active' => true,
                ],
            );
        }
    }

    private function seedContactRequests(): void
    {
        $requests = [
            [
                'name' => 'عبدالله الشمري',
                'email' => 'abdullah.alshammari@example.com',
                'phone' => '0501234567',
                'company' => 'مؤسسة الشمري للمقاولات',
                'inquiry_type' => 'استفسار عام',
                'message' => 'أرغب بمعرفة المزيد عن خدمات التوريد المتاحة على المنصة.',
                'status' => 'new',
            ],
            [
                'name' => 'سارة العتيبي',
                'email' => 'sara.alotaibi@example.com',
                'phone' => '0559876543',
                'company' => null,
                'inquiry_type' => 'طلب عرض سعر',
                'message' => 'هل يمكن تزويدي بعرض سعر لتوريد مواد بناء لمشروع سكني في الرياض؟',
                'status' => 'new',
            ],
            [
                'name' => 'محمد القحطاني',
                'email' => 'm.alqahtani@example.com',
                'phone' => '0567891234',
                'company' => 'شركة القحطاني للتجارة',
                'inquiry_type' => 'شراكة إعلانية',
                'message' => 'نرغب بالإعلان عن منتجاتنا على منصتكم، ما هي الباقات المتاحة؟',
                'status' => 'in_progress',
            ],
            [
                'name' => 'فهد الدوسري',
                'email' => 'fahad.aldosari@example.com',
                'phone' => '0541122334',
                'company' => null,
                'inquiry_type' => 'دعم فني',
                'message' => 'واجهت مشكلة في تسجيل شركتي على المنصة، أحتاج للمساعدة.',
                'status' => 'in_progress',
            ],
            [
                'name' => 'نورة الحربي',
                'email' => 'noura.alharbi@example.com',
                'phone' => '0533344556',
                'company' => 'مصنع الحربي للحديد',
                'inquiry_type' => 'تحديث بيانات',
                'message' => 'أرغب بتحديث بيانات شركتنا المسجلة على المنصة.',
                'status' => 'resolved',
            ],
        ];

        foreach ($requests as $request) {
            ContactRequest::firstOrCreate(
                [
                    'email' => $request['email'],
                    'inquiry_type' => $request['inquiry_type'],
                ],
                [
                    'name' => $request['name'],
                    'phone' => $request['phone'],
                    'company' => $request['company'],
                    'message' => $request['message'],
                    'status' => $request['status'],
                ],
            );
        }
    }
}
