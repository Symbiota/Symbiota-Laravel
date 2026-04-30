@props(['image', 'href' => null])
<div class="bg-base-200 group relative w-fit">
    @if($href)
        <x-link :href="$href">
            <img class="h-72 w-48 object-cover" loading="lazy" src="{{ $image->thumbnailUrl ?? $image->url }}" />
        </x-link>
    @else
        <img class="h-72 w-48 object-cover" loading="lazy" src="{{ $image->thumbnailUrl ?? $image->url }}" />
    @endif
    <div class="bg-neutral bottom-0 w-full p-2 text-white group-hover:block group-focus:block">{{ $slot }}</div>
</div>
