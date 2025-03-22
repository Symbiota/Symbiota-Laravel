@php
use App\Models\UserRole;
$user = request()->user();
$collections = App\Models\Collection::query()
->join('userroles', 'tablePK', 'collid')
->whereIn('role', [UserRole::COLL_ADMIN, UserRole::COLL_EDITOR])
->selectRaw('omcollections.*, GROUP_CONCAT(DISTINCT role) as roles')
->groupBy('collid')
->where('uid', $user->uid)
->get();

@endphp
<x-layout class="sm:w-[95%] lg:w-[75%] m-auto flex flex-col gap-4 p-0">
    {{--<div class="mt-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('') ],
            ['title' => 'Previous', 'href' => url('') ],
            'User Settings'
        ]" />
    </div>
    --}}

    <h1 class="text-4xl font-bold sr-only">User Settings</h1>

    <div class="mt-4 flex gap-2 items-center">
        <div
            class="font-bold border border-base-300 rounded-full w-10 h-10 bg-base-300 flex items-center justify-center">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div class="text-2xl font-bold">{{$user->name}}</div>
    </div>

    <div class="flex flex-cols-2 mb-4" x-data="{ active_tab: 'Collections' }">
        {{-- Navigation Menu --}}
        <div class="flex-shrink">
            @foreach ([ 'Profile', 'Projects and checklists', 'Collections', 'Datasets', 'Passwords and authentication',
            'Developer' ] as $item)
            <button :class="active_tab === '{{ $item }}'? 'bg-base-200': 'bg-base-100' "
                @click="active_tab = '{{ $item }}'"
                class="flex items-center gap-4 hover:bg-base-300 px-2 p-1 rounded-md relative cursor-pointer w-full">
                <div x-show="active_tab === '{{ $item }}'" x-cloak
                    class="bg-accent w-1 h-5 absolute -left-2 rounded-md"></div>
                <x-icons.edit />
                {{$item}}
            </button>
            @endforeach
        </div>

        {{-- Navigation Content --}}
        <div class="pl-10 flex-grow">

            {{-- User Profile --}}
            <div x-show="active_tab === 'Profile'" x-cloak>
                <div class="text-2xl font-bold">Profile</div>
                <hr class="mb-4" />
                <form class="flex flex-col gap-4">
                    <x-input label="Name" id="name" value="{{ $user->name }}" />
                    <x-input label="Email" id="email" value="{{ $user->email }}" />
                </form>
            </div>
            {{-- Projects and checklists --}}
            <div x-show="active_tab === 'Projects and checklists'" x-cloak>
                Todo projects and checklist
            </div>

            {{-- Collections --}}
            <div x-show="active_tab === 'Collections'" x-cloak class="flex flex-col gap-4">
                <div class="flex items-center">
                    <div class="text-2xl font-bold">Collections</div>
                    <div class="flex flex-grow justify-end">
                    @can('SUPER_ADMIN')
                        {{-- TODO (Logan) create collections --}}
                        <x-button href="{{ url(config('portal.name') . '/collections/misc/collmetadata.php') }}">
                            Create Collection
                        </x-button>
                    @endcan
                    </div>
                </div>
                <hr class="mb-4" />

                @if(count($collections) <= 0)
                    <div class="w-full h-full">
                        You have no permissions for any collections.
                    </div>
                @endif

                @foreach ($collections as $collection)
                <div class="flex items-center gap-4 p-4 rounded-md border border-base-300 relative">
                    <img class="w-16 mx-auto flex-shrink" src="{{ $collection->icon }}">
                    <div class="flex-grow">
                        <div class="text-xl font-bold">
                            {{ $collection->collectionName }}
                            <x-link hx-boost="true" href="{{ url('collections/' . $collection->collID)}}">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </x-link>
                        </div>
                        @php
                        $roles = explode(',', $collection->roles);
                        @endphp
                        <div class="flex gap-2">
                            @foreach ($roles as $role)
                            <div class="bg-base-300 w-fit px-2 rounded-full">
                                @if($role === UserRole::COLL_ADMIN)
                                Admin
                                @elseif($role === UserRole::COLL_EDITOR)
                                Editor
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Datasets --}}
            <div x-show="active_tab === 'Datasets'" x-cloak>
                Todo datasets
            </div>

            {{-- Passwords and authentication --}}
            <div x-show="active_tab === 'Passwords and authentication'" x-cloak>
                <div class="text-2xl font-bold">Password</div>
                <hr class="mb-4" />
                <form class="flex flex-col gap-4">
                    <x-input type="password" label="Old password" id="old_password" />
                    <x-input type="password" label="New password" id="new_password" />
                    <x-input type="password" label="Confirm password" id="confirm_password" />
                    <x-button type="submit">Update Password</x-button>
                    <x-link href="#todo">I forgot my password</x-link>
                </form>

                <div class="text-2xl font-bold">Two-factor authentication</div>
                <hr class="mb-4" />
                @if(session('status') == 'two-factor-authentication-confirmed')
                <div class="flex flex-col gap-4">
                    <p class="font-medium">
                        Two factor authentication confirmed and enabled successfully.
                    </p>
                    <p>
                        These are the recovery codes needed to get back into the account. Keep these in a safe place
                        losing them may
                        lead to losing access to your account.
                    </p>
                    <div>
                        @foreach (request()->user()->recoveryCodes() as $code)
                        <div>
                            {{ $code }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @elseif(auth()->user()->two_factor_confirmed_at)
                <form hx-delete="{{ url('/user/two-factor-authentication') }}">
                    @csrf
                    <x-button type="submit">Disable 2FA</x-button>
                </form>
                @elseif (session('status') == 'two-factor-authentication-enabled')
                <div class="flex flex-col gap-4 justify-center w-80">
                    <div class="font-medium">
                        Please finish configuring two factor authentication below.
                    </div>

                    <div>
                        {!! request()->user()->twoFactorQrCodeSvg(); !!}
                    </div>

                    <form hx-post="{{url('/user/confirmed-two-factor-authentication')}}" hx-swap="outerHTML"
                        hx-target="body" class="flex flex-col gap-4">
                        @csrf
                        <x-input label="Enter your verification code" id="code" />
                        <x-button class="w-fit" type="submit">Confirm 2FA</x-button>
                    </form>
                </div>
                @else
                <form hx-post="{{url('/user/two-factor-authentication')}}" hx-swap="outerHTML" hx-target="body">
                    @csrf
                    <x-button class="w-fit" type="submit">Enable Two Factor Auth</x-button>
                </form>
                @endif

                @if(count($errors) > 0)
                <div class="mb-4">
                    @foreach ($errors->all() as $error)
                    <div class="bg-error text-error-content rounded-md p-4">
                        {{ $error }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Developer --}}
            <div x-show="active_tab === 'Developer'">
                @fragment('tokens')
                <div id="tokens-container" class="flex flex-col gap-4">
                    <div>
                        <div class="p-2 flex items-center gap-2">
                            <div class="text-xl font-bold flex-grow">Personal access tokens </div>
                            <form class="m-0" hx-swap="outerHTML" hx-target="#tokens-container"
                                hx-post="{{ url('token/create') }}">
                                <input type="hidden" name="token_name" value="new_token">
                                @csrf
                                <x-button>Generate new token</x-button>
                            </form>
                        </div>
                        <hr>
                    </div>

                    <span>
                        Tokens you have generate that can be used to access the <x-link target="_blank"
                            href="{{ url('api/documentation') }}">Symbiota API</x-link>
                    </span>

                    @isset($created_token)
                    <div class="mt-4 p-4 border-t border-base-300">
                        Generated api key:
                        <span class="bg-base-300 py-1 px-2 rounded-md">{{ $created_token }}</span>
                        <div class="mt-1 text-warning font-bold">
                            This key cannot be viewed again make sure to keep it somewhere safe
                        </div>
                    </div>
                    @endisset
                    <div class="border border-base-300">
                        @foreach ($user_tokens as $token)
                        <div class="p-4">
                            <div class="flex items-center gap-4">
                                <div class="font-bold flex-grow">
                                    <span>{{ $token->name }}</span>
                                    @if($token->abilities)
                                    <i class="text-base opacity-50">- {{ implode(',', $token->abilities) }}</i>
                                    @endif
                                </div>
                                @if($token->last_used_at)
                                <div>Last used {{ $token->last_used_at }}</div>
                                @endif
                                <x-button variant="error" hx-swap="outerHTML" hx-include="input[name='_token']"
                                    hx-target="#tokens-container"
                                    hx-delete="{{url('token/delete/' . $token->id)}}">Delete</x-button>
                            </div>
                            @if($token->expires_at)
                            <div>Expires {{ $token->expires_at }}</div>
                            @else
                            <div class="text-warning font-bold underline">This token has no expiration date.</div>
                            @endif
                        </div>
                        @if(!$loop->last && count($user_tokens) > 1)
                        <hr />
                        @endif
                        @endforeach
                    </div>
                </div>
                @endfragment
            </div>
            {{-- Navigation Content End --}}
        </div>
    </div>
</x-layout>
