<!-- /resources/views/components/header.blade.php -->
<header class="bg-center bg-cover h-28" style="background-image: url(/images/banner.jpg)">
    <div class="flex bg-primary-darker bg-opacity-80 w-full h-full py-4">
        <div class="flex pl-12">
            <div class="w-[7.5rem] h-fit">
                <a href="https://symbiota.org">
                    <x-brand/>
                </a>
            </div>
            <div class="ml-8 flex justify-center flex-col text-white">
                <h1 class="text-2xl">Symbiota Brand New Portal</h1>
                <h2 class="text-lg">Redesigned by the Symbiota Support Hub</h2>
            </div>
        </div>

        <nav class="flex grow justify-end space-x-3 mr-4">
            @if (false)
            <span style="">
                {!! __("header.welcome") !!}
                {{ $USER_DISPLAY_NAME }}!
            </span>
            <x-button class="text-base" variant="accent">
                <a href="/{{ config('portal.name') }}/profile/viewprofile.php">My Profile</a>
            </x-button>
            <x-button class="text-base" variant="accent">
                <a href="/{{ config('portal.name') }}/profile/index.php?submit=logout">
                    {!! __("header.sign_out") !!}
                </a>
            </x-button>
            @else
            <x-button class="text-base" variant="accent">
                <a href="#">
                    {!! __("header.contact_us") !!}
                </a>
            </x-button>
            <x-button class="text-base" variant="accent">
                <a href="/login">
                    {!! __("header.sign_in") !!}
                </a>
            </x-button>
            @endif
        </nav>
    </div>
</header>
