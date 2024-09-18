<!-- /resources/views/components/header.blade.php -->
<header class="bg-center bg-cover h-28" style="background-image: url('{{url('/images/banner.jpg')}}')">
    <div class="flex bg-primary-darker bg-opacity-80 w-full h-full py-4">
        <div class="flex pl-12">
            <div class="w-[7.5rem] h-fit">
                <a href="https://symbiota.org">
                    <x-brand/>
                </a>
            </div>
            <div class="ml-8 flex justify-center flex-col text-white">
                <h1 class="text-2xl font-bold">Symbiota Brand New Portal</h1>
                <h2 class="text-[1.05rem] font-bold">Redesigned by the Symbiota Support Hub</h2>
            </div>
        </div>

        <nav class="flex grow items-center justify-end space-x-3 mr-4">
            @if (Auth::check())
            <span class="text-primary-content text-base">
                {!! __("header.welcome") !!}
                {{ Auth::user()->name }}!
            </span>
            <x-button class="text-base" variant="accent">
                <x-nav-link href="{{config('portal.name') . '/profile/viewprofile.php'}}">
                    My Profile
                </x-nav-link>
            </x-button>
            <x-button class="text-base" variant="accent">
                <x-nav-link hx-get="{{url('/logout')}}" hx-trigger="click" hx-boost="true" hx-target="body">
                    {!! __("header.sign_out") !!}
                </x-nav-link>
            </x-button>
            @else
            <x-button class="text-base" variant="accent">
                <x-nav-link href="#">
                    {!! __("header.contact_us") !!}
                </x-nav-link>
            </x-button>
            <x-button class="text-base" variant="accent">
                <x-nav-link hx-get="{{url('/login')}}" hx-trigger="click" hx-boost="true" hx-target="body" >
                    {!! __("header.sign_in") !!}
                </x-nav-link>
            </x-button>
            @endif
        </nav>
    </div>
</header>
