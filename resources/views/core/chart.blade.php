@props([
    'id' => uniqid(),
    'values' => [],
    'name',
    'type',
    'width' => '',
    'height' => '',
])
{{-- Any chart js can be passed for $type --}}
{{-- Note js for chart setup is stored in resources/js/components/chart.js --}}
@if(!empty($values))
<div x-init="symbChartSetup('{{ $id }}', '{{ $name }}', '{{ $type }}', {{ $height || $width? 'false': 'true'}})" {{ $attributes->twMerge('relative overflow-auto') }} >
    <canvas width="{{ $width }}" height="{{ $height }}" data-chart="{{ json_encode($values) }}" id="{{ $id }}"></canvas>
</div>
@endif
