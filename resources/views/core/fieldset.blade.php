@props(['legend' => ''])
<fieldset class="flex flex-col gap-2 border p-4 rounded border-gray-300">
    @if($legend)
        <legend class="text-xl">{{ $legend }}</legend>
    @endif
    {{ $slot }}
</fieldset>