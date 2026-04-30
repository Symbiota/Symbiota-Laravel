@props(['src', 'title'])
<div class="group w-fit">
    <div class="bg-base-200 relative">
        @if($src)
            <img
                class="h-72 w-48"
                loading="lazy"
                src="{{ $src }}"
                alt="wow no images"
                onerror="this.alt = 'Error While Loading'"
            />
        @else
            <div class="flex h-72 w-48 items-center justify-center">
                <i class="fa-solid fa-plus"></i>
            </div>
        @endif
        <div class="absolute bottom-0 w-full bg-black/70 p-2 text-white group-hover:block group-focus:block">
            {{ $title }}
        </div>
    </div>
</div>
