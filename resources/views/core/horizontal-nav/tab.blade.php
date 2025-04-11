@props(['name'])
<div x-show="active_tab === '{{ $name }}'" x-cloak {{ $attributes }}>
    {{ $slot }}
</div>
