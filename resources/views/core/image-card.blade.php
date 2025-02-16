@props(['src', 'title'])
<div class="group w-fit">
    <div class="relative bg-base-200">
        @if($src)
            <img class="h-72 w-48" loading="lazy" src="{{ $src }}" alt="wow no images" onerror="this.alt = 'Error While Loading'" />
        @else
        <div class="h-72 w-48 flex items-center justify-center">
            <i class="fa-solid fa-plus"></i>
        </div>
        @endif
        <div class="group-hover:block group-focus:block text-white absolute w-full bg-opacity-70 p-2 bg-black bottom-0">
            {{ $title }}
        </div>
    </div>
</div>
