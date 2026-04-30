@props(['labels'])
@foreach($labels as $label => $value)
    <x-text-label :label="$label" {{ $attributes }}>{{ $value }}</x-text-label>
@endforeach
