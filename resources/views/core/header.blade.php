@props(['buttonVariant' => 'accent'])
<!-- /resources/views/components/header.blade.php -->
<header
    class="text-banner-overlay-content min-h-28 bg-cover bg-position-[var(--symb-banner-position)]"
    style="background-image: var(--symb-banner-url)"
>
    <div
        {{ $attributes->twMerge('bg-banner-overlay flex items-center gap-4 min-h-28 w-full flex-wrap py-4 px-4 md:px-8') }}
    >
        <div class="flex flex-1 items-center gap-4">
            @if(config('portal.show_brand'))
                <div class="h-fit w-[4.5rem] lg:w-[7.5rem]">
                    <a href="https://symbiota.org">
                        <x-brand />
                    </a>
                </div>
            @endif
            <div class="flex flex-col justify-center font-bold text-shadow-lg">
                <h1 class="text-xl text-nowrap md:text-2xl lg:text-4xl">{{ config('portal.header_title') }}</h1>
                <h2 class="text-xs text-nowrap md:text-sm lg:text-base">{{ config('portal.header_sub_title') }}</h2>
            </div>
        </div>

        <nav class="flex flex-wrap items-center space-x-3 lg:justify-end">
            @if(Auth::check())
                <span class="text-base text-shadow-lg">
                    {!! __("header.H_WELCOME") !!} {{ Auth::user()->firstName }}!
                </span>
                <x-button
                    class="text-base"
                    href="{{ url('/user/profile') }}"
                    hx-boost="true"
                    hx-push-url="true"
                    variant="{{ $buttonVariant }}"
                >
                    {!! __("header.H_MY_PROFILE") !!}
                </x-button>
                <x-button
                    class="cursor-pointer text-base"
                    href="{{ url('/logout') }}"
                    hx-boost="true"
                    hx-boost="true"
                    variant="{{ $buttonVariant }}"
                >
                    {!! __("header.H_LOGOUT") !!}
                </x-button>
            @else
                <x-button class="text-base" href="#" variant="{{ $buttonVariant }}">
                    {!! __("header.H_CONTACT_US") !!}
                </x-button>
                <x-button
                    class="text-base"
                    href="{{ url('/login') }}"
                    hx-boost="true"
                    hx-push-url="true"
                    variant="{{ $buttonVariant }}"
                >
                    {!! __("header.H_LOGIN") !!}
                </x-button>
            @endif
        </nav>
    </div>
</header>
