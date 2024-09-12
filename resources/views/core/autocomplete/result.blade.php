@props(['data' => [], 'value' => 'value', 'label' => 'label'])
@foreach ($data as $item)
    @if(is_object($item))
    <div class="p-1" id="{{ $item->$value }}" >{{ $item->$label }}</div>
    @else
    <div class="p-1" id="{{ $item[$value] }}" >{{ $item[$label] }}</div>
    @endif
@endforeach
