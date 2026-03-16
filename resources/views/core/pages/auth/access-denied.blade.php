@php
global $LANG;
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('prohibit');
$referer = request()->header('referer');
@endphp

<x-margin-layout class="justify-center items-center" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div>
    <div class="flex flex-col gap-4 p-4 border border-base-300 rounded-md">
        <div>
            <h1 class="text-4xl font-bold">Resource Access Denied</h1>
            <hr>
        </div>
        <p>{{ $LANG['NO_PERMISSION']}}</p>

        <div class="flex items-center gap-2">
            <x-button class="w-1/2 justify-center" :href="url('/')">Return to home</x-button>

            @if($referer !== url()->current())
            <x-button class="w-1/2 justify-center"  :href="$referer">Go back</x-button>
            @endif
        </div>
    </div>
</x-margin-layout>
