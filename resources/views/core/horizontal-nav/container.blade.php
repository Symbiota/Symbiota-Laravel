@props(['default_active_tab', 'items' => []])
<div x-data="{ active_tab: '{{ $default_active_tab }}' }" {{$attributes->twMerge('flex flex-cols-2 mb-4')}}>
    <div class="flex-shrink">
        @foreach ($items as $item)
        <button :class="active_tab === '{{ $item['label'] }}'? 'bg-base-200': 'bg-base-100' "
            @click="active_tab = '{{ $item['label'] }}'"
            class="flex items-center text-nowrap gap-4 hover:bg-base-300 px-3 p-1 rounded-md relative cursor-pointer w-full">
            <div x-show="active_tab === '{{ $item['label'] }}'" x-cloak
                class="bg-accent w-1 h-5 absolute -left-2 rounded-md"></div>
            <span class="w-3 flex items-center justify-center">
                <i class="{{ $item['icon'] }}"></i>
            </span>
            {{ $item['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Navigation Content --}}
    <div class="pl-10 flex-grow">
        {{-- Should be paired with x-horizontal-nav.tab --}}
        {{ $slot }}
    </div>
</div>
