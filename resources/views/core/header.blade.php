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
                <h2 class="text-[1.05rem]">{{ config('portal.header_sub_title') }}</h2>
            </div>
        </div>

        <nav class="flex grow items-center justify-end space-x-3 mr-4">
            @if (Auth::check())
            <span class="text-base text-shadow-lg">
                {!! __("header.welcome") !!}
                {{ Auth::user()->name }}!
            </span>
            <x-button class="text-base" variant="{{ $buttonVariant }}">
                <x-nav-link href="{{ url('/user/profile') }}" hx-boost="true" hx-push-url="true">
                    My Profile
                </x-nav-link>
            </x-button>
            <x-button class="text-base" variant="{{ $buttonVariant }}">
                <x-nav-link hx-get="{{url('/logout')}}" hx-trigger="click" hx-boost="true" hx-target="body">
                    {!! __("header.sign_out") !!}
                </x-nav-link>
            </x-button>
            @else
            <x-button class="text-base" variant="{{ $buttonVariant }}">
                <x-nav-link href="#">
                    {!! __("header.contact_us") !!}
                </x-nav-link>
            </x-button>
            <x-button class="text-base" variant="{{ $buttonVariant }}">
                <x-nav-link href="{{url('/login')}}" hx-boost="true" hx-push-url="true">
                    {!! __("header.sign_in") !!}
                </x-nav-link>
            </x-button>
            @endif
        </nav>
    </div>
</header>
