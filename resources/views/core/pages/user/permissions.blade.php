@props(['users' => []])
<x-margin-layout>
    <div class="flex items-center gap-2">
        <x-page-title class="flex-grow"> {{ __('profile_usermanagement.USER_MNGMT') }}</x-page-title>
        <x-button> {{ __('profile_usermanagement.CREATE_NEW_USER') }} </x-button>
    </div>

    <form method="GET" hx-get="{{ route('user.management') }}" hx-target="#user-list">
        <x-input id="searchterm" :label="__('profile_usermanagement.LAST_OR_LOGIN')" />
        <x-button class="mt-2"> {{ __('profile_usermanagement.SEARCH_BOX') }} </x-button>
    </form>

    <div id="user-list">
        @fragment('user-list')
            @if(!empty($users))
                <div class="flex flex-col">
                    @foreach($users as $uid => $label)
                        <x-link :href="url('user/' .  $uid . '/permissions')"> {{ $label }} </x-link>
                    @endforeach
                </div>
            @endif
        @endfragment
    </div>
</x-margin-layout>
