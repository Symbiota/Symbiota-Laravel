@props(['legend' => ''])
<fieldset {{ $attributes->twMerge('flex flex-col gap-2 rounded border border-gray-300 p-4') }}>
    @if($legend)
        <legend class="text-xl font-bold">{{ $legend }}</legend>
    @endif
    {{ $slot }}
</fieldset>
