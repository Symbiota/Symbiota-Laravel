@php
global $LANG;
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('prohibit');
$referer = request()->header('referer');
$hasLogin = auth()->check();
@endphp

{{-- TODO (Logan) translation for text before pr merge (waiting on translation changes) --}}
<x-margin-layout class="justify-center items-center" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="flex flex-col gap-4 p-4 border border-base-300 rounded-md">
        <div>
            <h1 class="text-4xl font-bold">Resource Access Denied</h1>
            <hr>
        </div>
        @if(!$hasLogin)
        <p>You must be logged in to access this page</p>
        @else
        <p>{{ $LANG['NO_PERMISSION']}}</p>
        @endif

        <div class="flex items-center gap-2 flex-wrap">
            @if(!$hasLogin)
            <x-button class="flex-grow justify-center" :href="url('/login')">Login</x-button>
            @else
            <x-button class="flex-grow justify-center" :href="url('/')">Return to home</x-button>
            @endif

            @if($referer && $referer !== url()->current())
            <x-button class="flex-grow justify-center"  :href="$referer">Go back</x-button>
            @endif
        </div>
    </div>
</x-margin-layout>
