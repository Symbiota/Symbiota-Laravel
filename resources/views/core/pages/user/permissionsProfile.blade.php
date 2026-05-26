@props([
    'user',
    'permissions',
    'specimen_collections',
    'observation_collections',
    'personal_observation_collections',
    'projects',
])

@php use App\Models\UserRole;
function collectionLabel($v) {
    return $v['collectionname'] . ' (' . $v['institutioncode'].')';
}
@endphp

<x-margin-layout>
    <x-page-title> {{ __('profile_usermanagement.USER_MNGMT') }} </x-page-title>

    <div class="flex items-center gap-2">
        <div class="text-2xl font-bold">
            {{ $user['firstname'] . ' ' . $user['lastname'] . ' (#' . $user['uid'] . ')' }}
        </div>
        {{--
        <x-link class="text-2xl" href="#">
            <x-icons.edit />
        </x-link>
        --}}
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

    <x-user.GeneralPermissionsForm :permissions="$permissions" :info="$info ?? []" />

    <x-user.KeyedPermissions :permissions="$permissions" />

    <form method="POST">
        @csrf
        <div class="text-xl font-bold">{{ __('profile_usermanagement.SPEC_COLS') }}</div>
        <x-select
            id="obs_projects"
            name="tablePk"
            label="Collection"
            :items="itemize_assoc($specimen_collections, 'collectionLabel')"
            class="w-full"
        />
        <x-radio
            label="Permission"
            name="role"
            :options="[
            [ 'value' => UserRole::COLL_ADMIN, 'label' => __('profile_usermanagement.ADMIN') ],
            [ 'value' => UserRole::COLL_EDITOR, 'label' => __('profile_usermanagement.EDITOR') ],
            [ 'value' => UserRole::RARE_SPP_READER, 'label' => __('profile_usermanagement.RARE') ],
        ]"
        />
        <x-button>{{ __('profile_usermanagement.ADD_PERMISSION') }}</x-button>
    </form>

    <form method="POST">
        @csrf
        <div class="text-xl font-bold">{{ __('profile_usermanagement.OBS_PROJECTS') }}</div>
        <x-select
            id="obs_projects"
            name="tablePk"
            label="Collection"
            :items="itemize_assoc($observation_collections, 'collectionLabel')"
            class="w-full"
        />
        <x-radio
            label="Permission"
            name="role"
            :options="[
            [ 'value' => UserRole::COLL_ADMIN, 'label' => __('profile_usermanagement.ADMIN') ],
            [ 'value' => UserRole::COLL_EDITOR, 'label' => __('profile_usermanagement.EDITOR') ],
            [ 'value' => UserRole::RARE_SPP_READER, 'label' => __('profile_usermanagement.RARE') ],
        ]"
        />

        <x-button>{{ __('profile_usermanagement.ADD_PERMISSION') }}</x-button>
    </form>

    <form method="POST">
        @csrf
        <div class="text-xl font-bold">{{ __('profile_usermanagement.PERS_SP_MGMNT') }}</div>
        <x-select
            id="spec"
            name="tablePk"
            label="Collection"
            :items="itemize_assoc($personal_observation_collections, 'collectionLabel')"
            class="w-full"
        />
        <x-radio
            label="Permission"
            name="role"
            :options="[
            [ 'value' => UserRole::COLL_ADMIN, 'label' => __('profile_usermanagement.ADMIN') ],
            [ 'value' => UserRole::COLL_EDITOR, 'label' => __('profile_usermanagement.EDITOR') ],
        ]"
        />
        <x-button>{{ __('profile_usermanagement.ADD_PERMISSION') }}</x-button>
    </form>

    <form method="POST">
        @csrf
        <div class="text-xl font-bold">{{ __('profile_usermanagement.INV_MGMNT') }}</div>
        <x-select id="spec" name="tablePk" label="Project" :items="itemize($projects)" class="w-full" />
        <input type="hidden" name="role" value="{{ UserRole::PROJ_ADMIN }}" />
        <x-button>{{ __('profile_usermanagement.ADD_PERMISSION') }}</x-button>
    </form>

    <form method="POST">
        @csrf
        <div class="text-xl font-bold">{{ __('profile_usermanagement.CHECKLIST_MGMNT') }}</div>
        <x-select id="spec" name="tablePk" label="Checklist" :items="itemize($checklists)" class="w-full" />
        <input type="hidden" name="role" value="{{ UserRole::CL_ADMIN }}" />
        <x-button>{{ __('profile_usermanagement.ADD_PERMISSION') }}</x-button>
    </form>
</x-margin-layout>
