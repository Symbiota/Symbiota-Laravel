@props(['permissions', 'errors', 'info' => []])
@php
use App\Models\UserRole;
@endphp
<form
    class="flex flex-col gap-4"
    method="POST"
    hx-put="{{ route('user.permissions.update', [ 'uid' => request('uid') ]) }}"
    hx-swap="outerHTML"
>
    @method('PUT')
    @csrf
    <div class="flex flex-col gap-1">
        <div class="text-xl font-bold">{{ __('profile_usermanagement.ASSIGN_NEW') }}</div>
        @foreach([
        UserRole::SUPER_ADMIN => __('profile_usermanagement.SUPERADMIN'),
        UserRole::TAXONOMY => __('profile_usermanagement.TAX_EDITOR'),
        UserRole::TAXON_PROFILE => __('profile_tpeditor.TAX_PROF_EDITOR'),
        UserRole::GLOSSARY_EDITOR => __('profile_usermanagement.GLOSSARY_EDITOR'),
        UserRole::KEY_ADMIN => __('profile_usermanagement.ID_KEY_ADMIN'),
        UserRole::KEY_EDITOR => __('profile_usermanagement.ID_KEY_EDITOR'),
        UserRole::CL_CREATE => __('profile_usermanagement.CL_CREATE'),
    ] as $permission => $label)
            <x-checkbox
                :label="$label"
                name="{{ $permission }}"
                :value="$permission"
                :checked="array_key_exists($permission, $permissions)"
            />
        @endforeach
    </div>

    <div class="flex flex-col gap-1">
        <div class="text-xl font-bold">{{ __('profile_usermanagement.OCCURRENCE_PROTECT') }}</div>
        @foreach([
        UserRole::RARE_SPP_ADMIN => __('profile_usermanagement.RARE_SP_ADMIN_2'),
        UserRole::RARE_SPP_READER_ALL => __('profile_usermanagement.CAN_READ'),
        ] as $permission => $label)
            <x-checkbox
                :label="$label"
                name="{{ $permission }}"
                :value="$permission"
                :checked="array_key_exists($permission, $permissions)"
            />
        @endforeach
    </div>

    <x-alerts class="bg-info text-info-content" :messages="$info" />
    <x-errors :errors="$errors" />

    <x-button>{{ __('profile_usermanagement.ADD_PERMISSION') }}</x-button>
</form>
