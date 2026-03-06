@props(['buttonVariant' => 'accent'])
<!-- /resources/views/components/header.blade.php -->
<header class="bg-position-[var(--symb-banner-position)] bg-cover h-28 text-banner-overlay-content" style="background-image: var(--symb-banner-url)">
    <div {{ $attributes->twMerge('bg-banner-overlay flex w-full h-full py-4') }}>
        <div class="flex pl-12">
            @if(config('portal.show_brand'))
            <div class="w-[7.5rem] h-fit">
                <a href="https://symbiota.org">
                    <x-brand/>
                </a>
            </div>
            @endif
            <div class="ml-8 flex justify-center flex-col text-shadow-lg font-bold">
                <h1 class="text-4xl">{{ config('portal.header_title') }}</h1>
                <h2 class="text-base">{{ config('portal.header_sub_title') }}</h2>
            </div>
        </div>

        <nav class="flex grow items-center justify-end space-x-3 mr-4">
            @if (Auth::check())
            <span class="text-base text-shadow-lg">
                {!! __("header.H_WELCOME") !!}
                {{ Auth::user()->firstName}}!
            </span>
            <x-button class="text-base" href="{{ url('/user/profile') }}" hx-boost="true" hx-push-url="true" variant="{{ $buttonVariant }}">
                {!! __("header.H_MY_PROFILE") !!}
            </x-button>
            <x-button class="text-base cursor-pointer" href="{{ url('/logout') }}" hx-boost="true" hx-boost="true" variant="{{ $buttonVariant }}">
                {!! __("header.H_LOGOUT") !!}
            </x-button>
            @else
            <x-button class="text-base" href="#" variant="{{ $buttonVariant }}">
                {!! __("header.H_CONTACTS") !!}
            </x-button>
            <x-button class="text-base" href="{{ url('/login') }}" hx-boost="true" hx-push-url="true" variant="{{ $buttonVariant }}">
                {!! __("header.H_LOGIN") !!}
            </x-button>
            @endif
        </nav>
    </div>
</header>
