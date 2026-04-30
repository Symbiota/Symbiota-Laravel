@props(['legend' => ''])
<fieldset class="flex flex-col gap-2 rounded border border-gray-300 p-4">
    @if($legend)
        <legend class="text-xl">{{ $legend }}</legend>
    @endif
    {{ $slot }}
</fieldset>
