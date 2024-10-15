@props(['items' => []])
<nav class="flex justify-between px-3.5 py-1 border border-neutral-200/60 rounded-md">
    <ol class="inline-flex items-center mb-3 space-x-1 text-xs text-base-content/50 [&_.active-breadcrumb]:text-neutral-600 [&_.active-breadcrumb]:font-medium sm:mb-0">
        @empty($slot)
            {{ $slot }}
        @endempty
        @foreach ($items as $item)
            @if(is_array($item) && isset($item['icon']))
               @include('icons.' . $item['icon'])
            @elseif(is_array($item) && isset($item['fa_icon']))
               <i class="{{ twMerge(($loop->last? 'text-base-content ': '') . $item['fa_icon']) }}"></i>
            @endif

            @if($loop->last)
                <li class="text-base font-bold text-base-content">
                    {{ is_array($item) ? $item['title']: $item }}
                </li>
            @else
                @if(is_array($item) && isset($item['href']))
                    <x-link class="hover:text-base-content text-base-content/50 no-underline text-base" href="{{ $item['href'] }}">
                        {{ $item['title'] }}
                    </x-link>
                @else
                    <li class="text-base text-base-content/50 font-bold">
                        {{ $item }}
                    </li>
                @endif
                <x-icons.breadcrumb-seperator />
            @endif
        @endforeach
    </ol>
</nav>
