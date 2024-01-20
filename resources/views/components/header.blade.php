@php
include_once(base_path('public/Portal') . '/config/symbini.php');
@endphp

<!-- /resources/views/components/header.blade.php -->
<div class="header-wrapper">
	<header>
		<div class="top-wrapper">
			<nav class="top-login">
                @if ($USER_DISPLAY_NAME)
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
					<span>
						<a href="#">
                            {!! __("header.contact_us") !!}
						</a>
					</span>
                    <span class="button button-secondary">
                        <a href="/{{ config('portal.name') }}/profile/index.php">
                            {!! __("header.sign_in") !!}
                        </a>
                    </span>
                @endif
			</nav>
			<div class="top-brand">
				<a href="https://symbiota.org">
					<img src="/{{ config('portal.name') }}/images/layout/logo_symbiota.png" alt="Symbiota logo" width="100%">
				</a>
				<div class="brand-name">
					<h1>Symbiota Brand New Portal</h1>
					<h2>Redesigned by the Symbiota Support Hub</h2>
				</div>
			</div>
        </div>
        {{ $slot }}
	</header>
</div>
