<!-- /resources/views/components/header.blade.php -->
<header class="bg-center bg-cover h-28" style="background-image: url(/images/banner.jpg)">
    <div class="flex bg-primary bg-opacity-75 w-full h-full py-4">
        <div class="flex pl-12">
            <div class="w-[7.5rem] h-fit">
                <a href="https://symbiota.org">
                    <img src="/icons/brand.svg" alt="Symbiota logo" width="100%">
                </a>
            </div>
            <div class="ml-8 flex justify-center flex-col text-white">
                <h1 class="text-2xl">Symbiota Brand New Portal</h1>
                <h2 class="text-lg">Redesigned by the Symbiota Support Hub</h2>
            </div>
        </div>

		<nav class="flex grow justify-end space-x-1 mr-4">
                @if (false)
                    <span style="">
                        {!! __("header.welcome") !!}
                        {{ $USER_DISPLAY_NAME }}!
                    </span>
					<span class="button button-tertiary">
						<a href="/{{ config('portal.name') }}/profile/viewprofile.php">My Profile</a>
					</span>
					<span class="button button-secondary">
						<a href="/{{ config('portal.name') }}/profile/index.php?submit=logout">
                            {!! __("header.sign_out") !!}
                        </a>
					</span>
                @else
                    <x-button>
						<a href="#">
                            {!! __("header.contact_us") !!}
						</a>
                    </x-button>
                    <x-button>
                        <a href="/{{ config('portal.name') }}/profile/index.php">
                            {!! __("header.sign_in") !!}
                        </a>
                    </x-button>
                @endif
		</nav>
    </div>
</header>
