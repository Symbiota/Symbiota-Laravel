@props([
    'user',
    'permissions',
    'specimen_collections',
    'observation_collections',
    'personal_observation_collections',
    'projects',
])

@php use App\Models\UserRole; @endphp

<x-margin-layout>
    <x-page-title> {{ __('profile_usermanagement.USER_MNGMT') }} </x-page-title>

    <div class="flex items-center gap-2">
        <div class="text-2xl font-bold">
            {{ $user['firstname'] . ' ' . $user['lastname'] . ' (#' . $user['uid'] . ')' }}
        </div>
        <x-link href="#">TODO EDIT</x-link>
    </div>

    <div>
        @foreach([
            'title' => __('exsiccati.TITLE') ,
            'institution' => __('profile_newprofile.INSTITUTION'),
            'city' => __('profile_newprofile.CITY'),
            'zip' => __('profile_newprofile.ZIP'),
            'state' => __('profile_newprofile.STATE'),
            'country' => __('profile_newprofile.COUNTRY'),
            'email' => __('profile_newprofile.EMAIL'),
            'url' => __('profile_newprofile.URL'),
            'username' => __('profile_newprofile.USERNAME')
        ] as $field => $label)
            <x-text-label :label="$label">{{ $user[$field] }}</x-text-label>
        @endforeach

        <x-text-label :label="__('profile_usermanagement.LAST_LOGIN_DATE')">
            {{ $user['lastlogindate'] ?? __('profile_usermanagement.NOT_REGISTERED') }}
        </x-text-label>
    </div>

    <div>
        <x-link> {{ __('header.H_LOGIN') }} </x-link>
        {{ __('profile_usermanagement.AS_USER') }}
    </div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.PERMISSIONS') }}</div>

    @if(!empty($permissions))
        @foreach([
        UserRole::SUPER_ADMIN => __('profile_usermanagement.SUPERADMIN'),
        UserRole::TAXONOMY => __('profile_usermanagement.TAX_EDITOR'),
        UserRole::TAXON_PROFILE => __('profile_tpeditor.TAX_PROF_EDITOR'),
        UserRole::GLOSSARY_EDITOR => __('profile_usermanagement.GLOSSARY_EDITOR'),
        UserRole::KEY_ADMIN => __('profile_usermanagement.ID_KEY_ADMIN'),
        UserRole::KEY_EDITOR => __('profile_usermanagement.ID_KEY_EDITOR'),
        UserRole::CL_CREATE => __('profile_usermanagement.CL_CREATE'),
        UserRole::RARE_SPP_ADMIN => __('profile_usermanagement.RARE_SP_ADMIN'),
        UserRole::RARE_SPP_READER_ALL => __('profile_usermanagement.RARE_SP_VIEWER'),
    ] as $permission => $label)
            @if(array_key_exists($permission, $permissions))
                <div class="bg-base-200 border-base-300 flex items-center rounded-md border p-1 px-2">
                    <span class="grow" title="{{ $permissions[$permission]["aby"] ?? "" }}">{{ $label }}</span>
                    <button hx-delete="{{ url('user/' . $user['uid'] . '/permissions/' . $permission) }}">
                        <x-icons.delete />
                    </button>
                </div>
            @endif
        @endforeach
        {{-- With sub permissions --}}
        @foreach([
        UserRole::COLL_ADMIN => __('profile_usermanagement.ADMIN_FOR'),
        UserRole::COLL_EDITOR => __('profile_usermanagement.CL_CREATE'),
        UserRole::RARE_SPP_READER => __('profile_usermanagement.CL_CREATE'),
        UserRole::PERSONAL_OBS_ADMIN => __('profile_usermanagement.CL_CREATE'),
        UserRole::PERSONAL_OBS_EDITOR => __('profile_usermanagement.CL_CREATE'),
        UserRole::PERSONAL_OBS_READER => __('profile_usermanagement.CL_CREATE'),
        UserRole::PROJ_ADMIN => __('profile_usermanagement.CL_CREATE'),
        UserRole::CL_ADMIN => __('profile_usermanagement.CL_CREATE'),
    ] as $permission => $label)
            @if(array_key_exists($permission, $permissions))
                <div class="text-lg font-bold">{{ $label }}</div>
                @foreach($permissions[$permission] as $key => $sub_permission)
                    <div class="bg-base-200 border-base-300 flex items-center rounded-md border p-1 px-2">
                        <span class="flex-grow">{{ $sub_permission['name'] ?? 'no name' }} {{ $key }}</span>
                        <button hx-delete="{{ url('user/' . $user['uid'] . '/permissions/' . $permission) }}">
                            <x-icons.delete />
                        </button>
                    </div>
                @endforeach
            @endif
        @endforeach

    @else
        <div>{{ __('profile_usermanagement.NO_PERMISSIONS') }}</div>
    @endif

    <div class="text-xl font-bold">{{ __('profile_usermanagement.ASSIGN_NEW') }}</div>

    <div>
        @foreach([
        UserRole::SUPER_ADMIN => __('profile_usermanagement.SUPERADMIN'),
        UserRole::TAXONOMY => __('profile_usermanagement.TAX_EDITOR'),
        UserRole::TAXON_PROFILE => __('profile_tpeditor.TAX_PROF_EDITOR'),
        UserRole::GLOSSARY_EDITOR => __('profile_usermanagement.GLOSSARY_EDITOR'),
        UserRole::KEY_ADMIN => __('profile_usermanagement.ID_KEY_ADMIN'),
        UserRole::KEY_EDITOR => __('profile_usermanagement.ID_KEY_EDITOR'),
        UserRole::CL_CREATE => __('profile_usermanagement.CL_CREATE'),
        UserRole::RARE_SPP_ADMIN => __('profile_usermanagement.RARE_SP_ADMIN'),
        UserRole::RARE_SPP_READER_ALL => __('profile_usermanagement.RARE_SP_VIEWER'),
    ] as $permission => $label)
            @if(!array_key_exists($permission, $permissions))
                <x-checkbox :label="$label" name="name" />
            @endif
        @endforeach
    </div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.OCCURRENCE_PROTECT') }}</div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.SPEC_COLS') }}</div>

    <div>
        @foreach($specimen_collections as $collId => $collection)
            <div>
                <x-checkbox
                    :label="$collection['collectionname'] . ' (' . $collection['institutioncode'].')'"
                    :id="$collId"
                />
            </div>
        @endforeach
    </div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.OBS_PROJECTS') }}</div>

    <div>
        @foreach($observation_collections as $collId => $collection)
            <div>
                <x-checkbox
                    :label="$collection['collectionname'] . ' (' . $collection['institutioncode'].')'"
                    :id="$collId"
                />
            </div>
        @endforeach
    </div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.PERS_SP_MGMNT') }}</div>

    <x-autocomplete-input search="{{ url('api/collections/search') }}">
        <x-slot name="input" />
    </x-autocomplete-input>

    <div>
        @foreach($personal_observation_collections as $collId => $collection)
            <div class="flex items-center gap-2">
                <x-checkbox label="" :id="$collId" />
                <x-checkbox
                    :label="$collection['collectionname'] . ' (' . $collection['institutioncode'].')'"
                    :id="$collId"
                />
            </div>
        @endforeach
    </div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.INV_MGMNT') }}</div>

    <div>
        @foreach($projects as $pid => $project)
            <div>
                <x-checkbox :label="$project" :id="$pid" />
            </div>
        @endforeach
    </div>

    <div class="text-xl font-bold">{{ __('profile_usermanagement.CHECKLIST_MGMNT') }}</div>

    <div>
        @foreach($checklists as $clid => $checklist)
            <div>
                <x-checkbox :label="$checklist" :id="$clid" />
            </div>
        @endforeach
    </div>
</x-margin-layout>
