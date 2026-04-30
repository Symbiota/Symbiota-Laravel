@props(['buttonVariant' => 'accent'])
<!-- /resources/views/components/header.blade.php -->
<header
    class="text-banner-overlay-content h-28 bg-cover bg-position-[var(--symb-banner-position)]"
    style="background-image: var(--symb-banner-url)"
>
    <div {{ $attributes->twMerge('bg-banner-overlay flex w-full h-full py-4') }}>
        <div class="flex pl-12">
            @if(config('portal.show_brand'))
                <div class="h-fit w-[7.5rem]">
                    <a href="https://symbiota.org">
                        <x-brand />
                    </a>
                </div>
            @endif
            <div class="ml-8 flex flex-col justify-center font-bold text-shadow-lg">
                <h1 class="text-4xl">{{ config('portal.header_title') }}</h1>
                <h2 class="text-base">{{ config('portal.header_sub_title') }}</h2>
            </div>
        </div>

        <nav class="mr-4 flex grow items-center justify-end space-x-3">
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
