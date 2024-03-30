@props(['navigations'])
<!-- resources/views/tasks.blade.php -->
<nav {{$attributes}} class="bg-primary text-white h-14">
    <ul class="flex flex-wrap gap-2 justify-center h-full">
        @foreach ($navigations as $nav)
            <x-navbar.item>
                <a href="{{ $nav['link']}}">
                    {{$nav['title']}}
                </a>
            </x-navbar.item>
        @endforeach
        <x-navbar.item>
            <x-language-selector/>
        </x-navbar.item>
    </ul>
</nav>
