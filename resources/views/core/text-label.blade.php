@props(['label'])
@if($slot->isNotEmpty())
    <div><span class="font-bold">{{ $label }}: </span>{{ $slot }}</div>
@endif
