@props(['navigations'])
<!-- resources/views/core/navbar.blade.php -->
<nav x-data="{ open: false }" {{ $attributes->twMerge('bg-navbar text-navbar-content') }}>
    <div class="flex min-h-14 items-center justify-center px-4 md:hidden">
        <x-button
            type="button"
            class="text-navbar-content bg-transparent px-3 py-2 shadow-none active:bg-transparent"
            aria-controls="expand-navigation"
            x-bind:aria-expanded="open.toString()"
            aria-label="{{ __('header.H_MENU') }}"
            x-on:click="open = !open"
        >
            <i class="fa-solid fa-bars text-4xl" aria-hidden="true" x-show="!open"></i>
            <i class="fa-solid fa-xmark text-4xl" aria-hidden="true" x-show="open" x-cloak></i>
            <span class="sr-only">{{ __('header.H_MENU') }}</span>
        </x-button>
    </div>

    <div
        id="expand-navigation"
        class="hidden flex-col gap-2 font-bold md:flex md:min-h-14 md:flex-row md:flex-wrap md:justify-center md:gap-2"
        :class="{ '!flex': open }"
    >
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
