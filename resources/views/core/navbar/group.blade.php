@props(['title'])
<div class="group h-max">
    <x-navbar.item class="h-max">
        {{$title}}
    </x-navbar.item>
    <div class="bg-gray-50 w-max text-black absolute top hidden group-hover:block">
        {{$slot}}
    </div>
</div>
