@props(['image', 'href' => null])
<div class="relative bg-base-200 group w-fit">
    @if($href)
        <x-link :href="$href">
            <img class="h-72 w-48 object-cover" loading="lazy" src="{{$image->thumbnailUrl ?? $image->url}}" />
        </x-link>
    @else
        <img class="h-72 w-48 object-cover" loading="lazy" src="{{$image->thumbnailUrl ?? $image->url}}" />
    @endif
    <div
        class="group-hover:block group-focus:block text-white w-full p-2 bg-neutral bottom-0">
        {{ $slot }}
    </div>
</div>
