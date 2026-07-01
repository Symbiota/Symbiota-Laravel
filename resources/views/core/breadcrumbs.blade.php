@props(['items' => []])
<nav class="border-base-300 flex w-fit justify-between rounded-md border px-3.5 py-1">
    <ol class="text-base-content/50 mb-3 inline-flex items-center space-x-1 text-xs sm:mb-0">
        @empty($slot) {{ $slot }} @endempty
        @foreach($items as $item)
            @if(is_array($item) && isset($item['icon']))
                @include('icons.' . $item['icon'])
            @elseif(is_array($item) && isset($item['fa_icon']))
                <i class="{{ twMerge(($loop->last? 'text-base-content ': '') . $item['fa_icon']) }}"></i>
            @endif
            @if($loop->last)
                <li class="text-base-content text-base font-bold">
                    {{ \Illuminate\Support\Str::limit(is_array($item) ? $item['title'] : $item, 50, '...') }}
                </li>
            @else
                @if(is_array($item) && isset($item['href']))
                    <x-link
                        class="focus:ring-accent hover:text-base-content text-base-content/50 rounded-md text-base no-underline outline-none focus:ring"
                        href="{{ $item['href'] }}"
                    >
                        {{ \Illuminate\Support\Str::limit($item['title'], 50, '...') }}
                    </x-link>
                @else
                    <li class="text-base-content/50 text-base font-bold">
                        {{ \Illuminate\Support\Str::limit($item['title'] ?? $item, 50, '...') }}
                    </li>
                @endif
                <x-icons.breadcrumb-seperator />
            @endif
        @endforeach
    </ol>
</nav>
