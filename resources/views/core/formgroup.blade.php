@props(['label'])
<fieldset class="flex flex-col gap-2">
    <legend class="text-primary text-xl font-bold">{{ $label }}</legend>
    {{ $slot }}
</fieldset>
