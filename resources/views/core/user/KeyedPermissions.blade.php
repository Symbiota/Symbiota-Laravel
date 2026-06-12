@props(['permissions'])
@php
    use App\Models\UserRole;
    $hasKeyed = false;
@endphp
<div id="key-permissons" class="flex flex-col gap-2">
    <div class="text-xl font-bold">{{ __('profile_usermanagement.PERMISSIONS') }}</div>

    @foreach([
    UserRole::COLL_ADMIN => __('profile_usermanagement.ADMIN_FOR'),
    UserRole::COLL_EDITOR => __('profile_usermanagement.COL_EDITOR_FOR'),
    UserRole::RARE_SPP_READER => __('profile_usermanagement.RARE_SP_FOR'),
    UserRole::PROJ_ADMIN => __('profile_usermanagement.INVENTORY_ADMIN'),
    UserRole::CL_ADMIN => __('profile_usermanagement.CHECKLIST_ADMIN_FOR'),
] as $permission => $label)
        @if(array_key_exists($permission, $permissions))
            @php $hasKeyed = true; @endphp
            <div class="text-lg font-bold">{{ $label }}</div>
            @foreach($permissions[$permission] as $key => $sub_permission)
                <div class="bg-base-200 border-base-300 flex items-center rounded-md border p-1 px-2">
                    <span class="grow">{{ $sub_permission['name'] ?? 'unknown' }} {{ $key }}</span>
                    <form
                        method="POST"
                        hx-delete="{{ route('user.permissions.delete', ['uid' => request('uid'), 'role' => $permission]) }}"
                        hx-target="#key-permissons"
                    >
                        @csrf
                        <input type="hidden" name="tablePk" value="{{ $key }}" />
                        <button>
                            <x-icons.delete />
                        </button>
                    </form>
                </div>
            @endforeach
        @endif
    @endforeach

    @if(!$hasKeyed)
        <div>{{ __('profile_usermanagement.NO_PERMISSIONS') }}</div>
    @endif
</div>
