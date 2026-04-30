@props(['title'])
<div class="group h-max">
    <x-navbar.item class="h-max"> {{ $title }} </x-navbar.item>
    <div class="top absolute hidden w-max bg-gray-50 text-black group-hover:block">{{ $slot }}</div>
</div>
