@props(['label', 'allow_empty' => false])
@if($slot->isNotEmpty() || $allow_empty)
    <div><span class="font-bold">{{ $label }}: </span>{{ $slot }}</div>
@endif
