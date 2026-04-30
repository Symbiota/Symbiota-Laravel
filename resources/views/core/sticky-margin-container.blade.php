{{-- Note the intended use is only to be sticky when in full screen as there is a margin available to put content --}}
<div
    {{ $attributes->twMerge("lg:w-[100vw] lg:ml-[calc(50%-50vw)] lg:flex lg:justify-end lg:h-0 lg:sticky lg:top-4 lg:flex-none") }}
>
    {{ $slot }}
</div>
