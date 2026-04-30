@php
$referer = request()->header('referer');
$hasLogin = auth()->check();
@endphp

<x-margin-layout class="items-center justify-center" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="border-base-300 flex flex-col gap-4 rounded-md border p-4">
        <div>
            <h1 class="text-4xl font-bold">{{ __('Resource Access Denied') }}</h1>
            <hr />
        </div>
        @if(!$hasLogin)
            <p>{{ __("You must be logged in to access this page") }}</p>
        @else
            <p>{{ __("You don't have permission to access this page") }}</p>
        @endif

        <div class="flex flex-wrap items-center gap-2">
            @if(!$hasLogin)
                <x-button class="flex-grow justify-center" :href="url('/login')"> {{ __('header.H_LOGIN') }} </x-button>
            @else
                <x-button class="flex-grow justify-center" :href="url('/')"> {{ __('Return to homepage') }} </x-button>
            @endif

            @if($referer && $referer !== url()->current())
                <x-button class="flex-grow justify-center" :href="$referer"> {{ __('Go back') }} </x-button>
            @endif
        </div>
    </div>
</x-margin-layout>
