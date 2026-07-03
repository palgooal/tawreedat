<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            إجراءات سريعة
        </x-slot>

        <div class="flex flex-wrap gap-2">
            @foreach ($actions as $action)
                <x-filament::button
                    tag="a"
                    :href="$action['url']"
                    :icon="$action['icon']"
                    color="gray"
                    outlined
                    size="sm"
                >
                    {{ $action['label'] }}
                </x-filament::button>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
