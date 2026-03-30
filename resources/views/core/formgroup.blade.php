@props(['label'])
<fieldset class="flex flex-col gap-2">
<legend class="font-bold text-xl text-primary">{{ $label }}</legend>
    {{ $slot }}
</fieldset>
