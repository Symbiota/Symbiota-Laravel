@props(['default_active_tab', 'items' => []])
<div x-data="{ active_tab: '{{ $default_active_tab }}' }" {{ $attributes->twMerge('flex flex-cols-2 mb-4 py-4') }}>
    <div class="flex-shrink lg:w-[448px]">
        <div class="ml-auto w-fit pl-10">
            @foreach($items as $item)
                <button
                    :class="active_tab === '{{ $item['id'] ?? $item['label'] }}'? 'bg-base-200': 'bg-base-100' "
                    @click="active_tab = '{{ $item['id'] ?? $item['label'] }}'"
                    class="hover:bg-base-300 relative flex w-full cursor-pointer items-center gap-4 rounded-md p-1 px-3 text-nowrap"
                >
                    <div
                        x-show="active_tab === '{{ $item['id'] ?? $item['label'] }}'"
                        x-cloak
                        class="bg-accent absolute -left-2 h-5 w-1 rounded-md"
                    ></div>
                    <span class="flex w-3 items-center justify-center">
                        <i class="{{ $item['icon'] }}"></i>
                    </span>
                    {{ $item['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Navigation Content --}}
    <div class="mr-auto w-[90%] max-w-screen-lg flex-grow px-10 md:w-full">
        {{-- Should be paired with x-horizontal-nav.tab --}}
        {{ $slot }}
    </div>
</div>
