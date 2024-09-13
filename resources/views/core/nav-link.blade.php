@props(['href' => '#'])
<a href={{url($href)}} {{$attributes->twMerge('')}}>
    {{ $slot }}
</a>
