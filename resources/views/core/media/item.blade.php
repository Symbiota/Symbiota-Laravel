@props(['media' => []])
@php
$query_params = request()->all();
$query_params['start'] = isset($query_params['start']) && $query_params['start'] >= 0 ?
    $query_params['start'] + 30:
    0;
$query_params['partial'] = true;
@endphp

{{-- This is Need so infinte requests don't spawn --}}
@if(count($media) > 0)

{{-- Render Media Items --}}
@foreach ($media as $item)
<a class="group" target="_blank"
    href="{{ url(config('portal.name')) . '/collections/individual/index.php?occid=' . $item->occid }}">
    <div class="relative bg-base-200">
        <img class="h-72 w-48 object-cover" loading="lazy" src="{{$item->thumbnailUrl}}" />
        <div
            class="group-hover:block group-focus:block hidden text-white absolute w-full bg-opacity-70 p-2 bg-black bottom-0">
            {{$item->sciName}}
        </div>
    </div>
</a>
@endforeach

{{-- When the bottom is revealed then fetch more data --}}
<div hx-get="{{ url()->current() . '?' . http_build_query($query_params) }}" hx-swap="afterend"
    hx-indicator="#scroll-loader" hx-trigger="revealed">
</div>

@endif
