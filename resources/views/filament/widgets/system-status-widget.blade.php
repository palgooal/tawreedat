<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            حالة النظام
        </x-slot>

        @if ($debugEnabled)
            <div class="mb-4 flex items-center gap-x-2 rounded-lg border border-danger-300 bg-danger-50 px-3 py-2 text-sm font-medium text-danger-700 dark:border-danger-800 dark:bg-danger-950 dark:text-danger-300">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4 shrink-0" />
                <span>تحذير: APP_DEBUG مفعّل — لا يجب تشغيله في بيئة الإنتاج.</span>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">SEO</h3>
                <ul class="space-y-1.5 text-sm">
                    <li class="flex items-center gap-x-1.5">
                        <x-filament::icon
                            :icon="$seo['sitemapReady'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'"
                            class="h-4 w-4 shrink-0 {{ $seo['sitemapReady'] ? 'text-success-600' : 'text-danger-600' }}"
                        />
                        <span>Sitemap جاهز</span>
                    </li>
                    <li class="flex items-center gap-x-1.5">
                        <x-filament::icon
                            :icon="$seo['robotsReady'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'"
                            class="h-4 w-4 shrink-0 {{ $seo['robotsReady'] ? 'text-success-600' : 'text-danger-600' }}"
                        />
                        <span>Robots جاهز</span>
                    </li>
                    <li class="flex items-center gap-x-1.5 text-gray-500 dark:text-gray-400">
                        <x-filament::icon icon="heroicon-o-information-circle" class="h-4 w-4 shrink-0" />
                        <span>الفهرسة: {{ $seo['indexingEnabled'] ? 'مفعّلة' : 'متوقفة' }}</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">التواصل</h3>
                <ul class="space-y-1.5 text-sm">
                    <li class="flex items-center gap-x-1.5">
                        <x-filament::icon
                            :icon="$contact['notificationsEnabled'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'"
                            class="h-4 w-4 shrink-0 {{ $contact['notificationsEnabled'] ? 'text-success-600' : 'text-warning-600' }}"
                        />
                        <span>الإشعارات مفعلة</span>
                    </li>
                    <li class="flex items-center gap-x-1.5">
                        <x-filament::icon
                            :icon="$contact['protectionEnabled'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'"
                            class="h-4 w-4 shrink-0 {{ $contact['protectionEnabled'] ? 'text-success-600' : 'text-danger-600' }}"
                        />
                        <span>الحماية مفعلة</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">الأمان</h3>
                <ul class="space-y-1.5 text-sm">
                    <li class="flex items-center gap-x-1.5">
                        <x-filament::icon
                            :icon="$security['rolesSeeded'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'"
                            class="h-4 w-4 shrink-0 {{ $security['rolesSeeded'] ? 'text-success-600' : 'text-danger-600' }}"
                        />
                        <span>Roles &amp; Permissions</span>
                    </li>
                    <li class="flex items-center gap-x-1.5">
                        <x-filament::icon
                            :icon="$security['hasSuperAdmin'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'"
                            class="h-4 w-4 shrink-0 {{ $security['hasSuperAdmin'] ? 'text-success-600' : 'text-danger-600' }}"
                        />
                        <span>Super Admin</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">البيئة</h3>
                <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">APP_ENV</span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $environment['appEnv'] }}</code>
                    </li>
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">APP_DEBUG</span>
                        <code @class([
                            'rounded px-1.5 py-0.5 text-xs',
                            'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300' => $environment['appDebug'],
                            'bg-gray-100 dark:bg-gray-800' => ! $environment['appDebug'],
                        ])>{{ $environment['appDebug'] ? 'true' : 'false' }}</code>
                    </li>
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">QUEUE_CONNECTION</span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $environment['queueConnection'] }}</code>
                    </li>
                    <li class="flex items-center justify-between gap-x-2">
                        <span class="text-gray-500 dark:text-gray-400">MAIL_MAILER</span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $environment['mailMailer'] }}</code>
                    </li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
