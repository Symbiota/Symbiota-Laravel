@props(['navigations'])
<!-- resources/views/core/navbar.blade.php -->
<nav {{ $attributes->twMerge('h-14 bg-navbar text-navbar-content') }}>
    <div class="flex h-full flex-wrap justify-center gap-2 font-bold">
        @foreach($navigations as $nav)
            <x-navbar.item>
                <x-nav-link
                    :href="$nav['link']"
                    hx-push-url="true"
                    hx-boost="{{ ($nav['htmx'] ?? false)? 'true': 'false' }}"
                >
                    {{ $nav['title'] }}
                </x-nav-link>
            </x-navbar.item>
        @endforeach
        <x-navbar.item>
            <x-language-selector />
        </x-navbar.item>
    </div>
</nav>
