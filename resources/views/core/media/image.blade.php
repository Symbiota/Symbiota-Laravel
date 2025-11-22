@props(['image'])
<div class="relative bg-base-200 group w-fit">
    <img class="h-72 w-48 object-cover" loading="lazy" src="{{$image->thumbnailUrl ?? $image->url}}" />
    <div
        class="group-hover:block group-focus:block hidden text-white absolute w-full p-2 bg-black/70 bottom-0">
        {{ $slot }}
    </div>
</div>
