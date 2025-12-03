@props(['navigations'])
<!-- resources/views/core/navbar.blade.php -->
<nav {{$attributes->twMerge('h-14 bg-navbar text-navbar-content')}}>
    <div class="flex flex-wrap gap-2 justify-center h-full font-bold">
        @foreach ($navigations as $nav)
        <x-navbar.item>
            <x-nav-link :href="$nav['link']" hx-push-url="true" hx-boost="{{ ($nav['htmx'] ?? false)? 'true': 'false'}}">
                {{ $nav['title'] }}
            </x-nav-link>
        </x-navbar.item>
        @endforeach
        <x-navbar.item>
            <x-language-selector />
        </x-navbar.item>
    </div>
</nav>
