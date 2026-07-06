<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            حالة الإعلانات
        </x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2 sm:col-span-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">المساحات
                    الإعلانية</h3>
                <ul class="space-y-1.5 text-sm">
                    @foreach ($slots as $slot)
                        <li class="flex items-center justify-between gap-x-2">
                            <span class="flex items-center gap-x-1.5">
                                <x-filament::icon
                                    :icon="$slot['hasActive'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'"
                                    class="h-4 w-4 shrink-0 {{ $slot['hasActive'] ? 'text-success-600' : 'text-warning-600' }}"
                                />
                                <span>{{ $slot['name'] }}</span>
                            </span>

                            @unless ($slot['hasActive'])
                                <span
                                    class="rounded-full bg-warning-50 px-2 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-950 dark:text-warning-300">
                                    لا يوجد إعلان نشط
                                </span>
                            @endunless
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">الإحصائيات
                </h3>
                <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">إعلانات نشطة</span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $totalActiveAds }}</code>
                    </li>
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">إجمالي المشاهدات</span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $totalImpressions }}</code>
                    </li>
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">إجمالي النقرات</span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $totalClicks }}</code>
                    </li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
