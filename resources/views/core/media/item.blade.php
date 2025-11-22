@props(['media' => [], 'allow_empty_trigger' => false, 'fixed_start' => false, 'params' => []])
@php
$query_params = request()->all();
if(is_numeric($fixed_start) && $fixed_start >= 0) {
    $query_params['start'] = $fixed_start;
} else if(isset($query_params['start']) && $query_params['start'] >= 0) {
    $query_params['start'] += 30;
} else {
    $query_params['start'] = 0;
}
$query_params['partial'] = true;

foreach($params as $key => $value) {
    $query_params[$key] = $value;
}
@endphp

{{-- This is Need so infinte requests don't spawn --}}
{{-- @if(count($media) > 0) -- }}

{{-- Render Media Items --}}
@foreach ($media as $item)
<a class="group" target="_blank"
    href="{{ legacy_url('/collections/individual/index.php') . '?occid=' . $item->occid }}">
    <div class="relative bg-base-200">
        <img class="h-72 w-48 object-cover" loading="lazy" src="{{$item->thumbnailUrl ?? $item->url}}" />
        <div
            class="group-hover:block group-focus:block hidden text-white absolute w-full p-2 bg-black/70 bottom-0">
            {{$item->sciName}}
        </div>
    </div>
</a>
@endforeach

{{-- Avoids call if there isn't anymore items --}}
@if(count($media) >= 30 || $allow_empty_trigger)
{{-- When the bottom is revealed then fetch more data --}}
<div class="m-[-0.3rem]" hx-get="{{ url('/media/search') . '?' . http_build_query($query_params) }}" hx-swap="{{$allow_empty_trigger ? 'outerHTML':'afterend'}}"
    hx-indicator="#scroll-loader" hx-trigger="revealed">
</div>
@endif

{{--@endif--}}
