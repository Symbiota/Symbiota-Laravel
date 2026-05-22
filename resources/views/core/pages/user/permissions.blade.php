@props(['users' => []])
<x-margin-layout>
    <div class="flex items-center gap-2">
        <x-page-title class="flex-grow"> {{ __('profile_usermanagement.USER_MNGMT') }} </x-page-title>

        <x-button> {{ __('profile_usermanagement.CREATE_NEW_USER') }} </x-button>
    </div>

    <x-input id="searchterm" :label="__('profile_usermanagement.LAST_OR_LOGIN')" />
    <x-button> {{ __('profile_usermanagement.SEARCH_BOX') }} </x-button>

    @if(!empty($users))
        {{--
        @foreach ($users as $user)
        <x-link :href="url('user/' .  $user->uid . '/permissions')"></x-link>
        @endforeach
        --}}
        <div class="flex flex-col">
            @foreach($users as $uid => $label)
                <x-link :href="url('user/' .  $uid . '/permissions')"> {{ $label }} </x-link>
            @endforeach
        </div>
    @else
        <div>TODO EMPTY CASE</div>
    @endif
</x-margin-layout>
