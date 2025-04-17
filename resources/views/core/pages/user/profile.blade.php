@props(['user_tokens' => [], 'user' => request()->user()])
@php
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

$collections = App\Models\Collection::query()
->join('userroles', 'tablePK', 'collid')
->whereIn('role', [UserRole::COLL_ADMIN, UserRole::COLL_EDITOR])
->selectRaw('omcollections.*, GROUP_CONCAT(DISTINCT role) as roles')
->groupBy('collid')
->where('uid', $user->uid)
->get();

$checklists = DB::table('fmchecklists')
->leftJoin('userroles as ur', 'tablePK', 'clid')
->where(function ($query) use($user) {
$query
->whereIn('role', [UserRole::CL_ADMIN])
->where('ur.uid', $user->uid);
})
->orWhere('fmchecklists.uid', $user->uid)
->get();

$datasets = DB::table('omoccurdatasets')
->where('uid', $user->uid)
->get();

@endphp
<x-layout class="sm:w-[95%] lg:w-[75%] m-auto flex flex-col gap-4 p-0">
    <h1 class="text-4xl font-bold sr-only">User Settings</h1>

    <div class="mt-4 flex gap-2 items-center">
        <div
            class="font-bold border border-base-300 rounded-full w-10 h-10 bg-base-300 flex items-center justify-center">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div class="text-2xl font-bold">{{$user->name}}</div>
    </div>

    <x-horizontal-nav.container default_active_tab="Profile" :items="[
            ['label' => 'Profile', 'icon' => 'fa-solid fa-user'],
            ['label' => 'Projects and checklists', 'icon' => 'fa-solid fa-list'],
            ['label' => 'Collections', 'icon' => 'fa-solid fa-jar'],
            ['label' => 'Datasets', 'icon' => 'fa-solid fa-database'],
            ['label' => 'Passwords and authentication', 'icon' => 'fa-solid fa-lock'],
            ['label' => 'Developer', 'icon' => 'fa-solid fa-code'],
            ]">

        <x-horizontal-nav.tab name="Profile">
            <div class="text-2xl font-bold">Profile</div>
            <hr class="mb-4" />
            @fragment('profile')
            <form hx-put="{{ url('user/profile/metadata') }}" class="flex flex-col gap-4" hx-swap="outerHTML">
                @csrf
                <x-input label="Name" id="name" value="{{ $user->name }}" />
                <x-input label="Email" id="email" value="{{ $user->email }}" />
                <x-checkbox label="Accessibility Preference" id="accessibilityPref"
                    :checked="$user->dynamicProperties['accessibilityPref'] ?? false" />
                <x-input label="ORCID or other GUID" value="{{ $user->guid }}" />
                <x-input label="Title" id="title" value="{{ $user->title }}" />
                <x-input label="Institution" id="institution" value="{{ $user->institution }}" />
                <x-input label="City" id="city" value="{{ $user->city }}" />
                <x-input label="State" id="state" value="{{ $user->state }}" />
                <x-input label="Zip Code" id="zip" value="{{ $user->zip }}" />
                <x-input label="Country" id="country" value="{{ $user->country}}" />

                <x-button type="submit">Update Profile</x-button>
                <x-button hx-delete="{{ url('user/profile') }}" variant="error"
                    hx-confirm="Are you sure you wish to delete your account?">Delete Profile</x-button>
                {{-- TODO (Logan) taxonomic relationships. Not sure how this is tied to user profiles --}}
            </form>
            @endfragment
        </x-horizontal-nav.tab>

        {{-- CHECKLIST START --}}
        <x-horizontal-nav.tab name="Projects and checklists" class="flex flex-col gap-4"
            x-data="{ show_create_form: false }">
            <div class="flex items-center">
                <div class="text-2xl font-bold">Checklists</div>
                <div class="flex flex-grow justify-end">
                    @can('CL_CREATE')
                    <x-button @click="show_create_form = true">
                        Create checklist
                    </x-button>
                    @endcan
                </div>
            </div>
            <hr class="mb-4" />

            <form hx-post="{{ url('checklists/create') }}" hx-target="#user_checklists" hx-swap="outerHTML"
                x-show="show_create_form">
                @csrf
                <fieldset class="flex flex-col gap-4">
                    <legend class="font-bold text-lg">Create New Checklist</legend>
                    <hr />
                    <x-input label="Checklist Name" id="checklist_name" />

                    <div class="flex gap-2">
                        <x-button type="submit">Create</x-button>
                        <x-button type="button" variant="error" @click="show_create_form = false">Cancel</x-button>
                    </div>
                </fieldset>
            </form>
            @fragment('checklists')
            <div id="user_checklists">
                @if(count($checklists) <= 0) <div>
                    you have no permissions for any checklists.
            </div>
            @endif
            @foreach ($checklists as $checklist)
            <div class="p-2 border border-base-300 rounded-md">
                <div class="flex items-center">
                    <span class="font-bold text-xl">
                        {{ $checklist->name }}
                    </span>

                    <span class="flex flex-grow justify-end items-center gap-4">
                        <x-nav-link hx-boost="true" href="{{ url('checklists/' . $checklist->clid)}}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            Public View
                        </x-nav-link>

                        <x-nav-link
                            href="{{ url(config('portal.name') . '/checklists/checklistadmin.php') }}?clid={{ $checklist->clid }}&pid={{$checklist->pid ?? ''}}">
                            <x-icons.edit x-on:click="console.log('click')" />
                            Admin
                        </x-nav-link>

                        <x-nav-link
                            href="{{ url(config('portal.name') . '/checklists/voucheradmin.php') }}?clid={{ $checklist->clid }}&pid={{$checklist->pid ?? ''}}"
                            {{-- TODO Logan for checklist admin pr
                            href="{{ url('checklists')}}/{{$checklist->clid}}/admin" --}}>
                            <x-icons.edit />
                            Voucher Admin
                        </x-nav-link>
                    </span>
                </div>
                @if(!empty($checklist->locality))
                <div>
                    {{ $checklist->locality }}
                </div>
                @endif

                @if(!empty($checklist->abstract))
                <div>
                    {!! Purify::clean($checklist->abstract) !!}
                </div>
                @endif
            </div>
            @endforeach
            </div>
            @endfragment
        </x-horizontal-nav.tab>
        {{-- CHECKLIST END --}}

        {{-- COLLECTIONS START --}}
        <x-horizontal-nav.tab name="Collections" class="flex flex-col gap-4">
            <div class="flex items-center">
                <div class="text-2xl font-bold">Collections</div>
                <div class="flex flex-grow justify-end">
                    @can('SUPER_ADMIN')
                    <x-button href="{{ url(config('portal.name') . '/collections/misc/collmetadata.php') }}">
                        Create Collection
                    </x-button>
                    @endcan
                </div>
            </div>
            <hr class="mb-4" />

            @if(count($collections) <= 0) <div>
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
        </x-horizontal-nav.tab>
        {{-- COLLECTIONS END --}}

        {{-- DATASETS START --}}
        <x-horizontal-nav.tab name="Datasets" class="flex flex-col gap-4" x-data="{ show_create_form: false }">
            <div class="flex items-center">
                <div class="text-2xl font-bold">Datasets</div>
                <div class="flex flex-grow justify-end">
                    <x-button {{-- href="{{url(config('portal.name'))}}/collections/datasets/index.php" --}}
                        @click="show_create_form = true">
                        Create Dataset
                    </x-button>
                </div>
            </div>
            <hr class="mb-4" />

            <form x-show="show_create_form" hx-post="{{ url('user/profile/dataset') }}">
                @csrf
                <fieldset class="flex flex-col gap-4">
                    <legend class="font-bold text-lg">Create New Dataset</legend>
                    <hr />
                    <x-input label="Name" name="name" />
                    <x-checkbox label="Publicly Visible" name="isPublic" />
                    <x-input label="Notes (Not Displayed Publicly)" name="notes" />
                    <x-rich-editor label="Description (Displayed Publicly)" name="description"></x-rich-editor>

                    <div class="flex gap-2">
                        <x-button type="submit">Create</x-button>
                        <x-button type="button" variant="error" @click="show_create_form = false">Cancel</x-button>
                    </div>
                </fieldset>

                <div class="flex gap-2">
                </div>
            </form>

            @fragment('datasets')
            @if(count($datasets) <= 0) <div>
                You have no permissions for any datasets.
                </div>
                @endif

                @foreach ($datasets as $dataset)
                <div>
                    <div class="border border-base-300 rounded-md p-2">
                        <div class="flex">
                            <span class="text-xl font-bold">
                            {{ empty($dataset->name) ?'[ Empty Name ]' : $dataset->name }}
                            </span>

                            <span class="flex flex-grow justify-end items-center gap-4">
                                <x-nav-link
                                    href="{{ url(config('portal.name'))}}/collections/datasets/public.php?datasetid={{$dataset->datasetID}}">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    Dataset Managment
                                </x-nav-link>
                            </span>
                        </div>
                        @if(!empty($dataset->notes))
                        <div>
                            <span class="font-bold">Notes:</span>
                            {{ $dataset->notes }}
                        </div>
                        @endif
                        <div>
                            <span class="font-bold">Created:</span>
                            {{ $dataset->initialTimestamp }}
                        </div>
                    </div>
                </div>
                @endforeach
                @endfragment
        </x-horizontal-nav.tab>
        {{-- DATASETS END --}}

        {{-- PASSORDS START --}}
        <x-horizontal-nav.tab name="Passwords and authentication">
            <div class="text-2xl font-bold">Password</div>
            <hr class="mb-4" />
            @fragment('password')
            <form hx-post="{{ url('user/profile/password') }}" class="flex flex-col gap-4" hx-swap="outerHTML">
                @csrf
                <input type="hidden" value="{{ $user->email }}" name="email" />
                <x-input type="password" label="Password" id="current_password" value="{{ old('new_password') }}" />
                <x-input type="password" label="New password" id="password" value="{{ old('new_password') }}" />
                <x-input type="password" label="Confirm password" id="password_confirmation"
                    value="{{ old('password_confirmation') }}" />
                <x-button type="submit">Update Password</x-button>
                {{-- TODO (Logan) password resets <x-link hx-boost="true" href="{{ url('forgot-password') }}">I forgot my password</x-link> --}}
                <x-errors :errors="$errors" />
            </form>
            @endfragment

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

            <x-errors :errors="$errors" />
        </x-horizontal-nav.tab>
        {{-- PASSWORDS END --}}

        {{-- DEVELOPER START --}}
        <x-horizontal-nav.tab name="Developer">
            @fragment('tokens')
            <div id="tokens-container" class="flex flex-col gap-4" x-data="{ show_token_form: false}">
                <div>
                    <div class="py-2 flex items-center gap-2">
                        <div class="text-2xl font-bold flex-grow">Personal access tokens </div>
                        <x-button @click="show_token_form = true" type="button">Generate new token</x-button>
                    </div>
                    <hr>
                </div>

                <span>
                    Tokens you have generate that can be used to access the <x-link target="_blank"
                        href="{{ url('api/documentation') }}">Symbiota API</x-link>
                </span>

                <form x-show="show_token_form" class="m-0" hx-post="{{ url('token/create') }}" hx-swap="outerHTML"
                    hx-target="#tokens-container">
                    @csrf
                    <fieldset class="flex flex-col gap-4">
                        <legend class="font-bold text-lg">Create New Access Token</legend>
                        <hr />
                        <x-input required label="Token Name" id="token_name" />
                        <x-input type="date" label="Expiration Date" id="expiration_date" />
                        {{-- <x-checkbox id="" />
                        <x-checkbox />--}}
                        <div class="flex gap-4">
                            <x-button>Create</x-button>
                            <x-button type="button" variant="error" @click="show_token_form = false">Cancel</x-button>
                        </div>
                    </fieldset>
                </form>

                @isset($created_token)
                <div class="mt-4 p-4 border-t border-base-300">
                    Generated api key:
                    <span class="bg-base-300 py-1 px-2 rounded-md">{{ $created_token }}</span>
                    <div class="mt-1 text-warning font-bold">
                        This key cannot be viewed again make sure to keep it somewhere safe
                    </div>
                </div>
                @endisset
                @if(count($user_tokens) > 0)
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
                @endif
            </div>
            @endfragment
        </x-horizontal-nav.tab>
        {{-- DEVELOPER END --}}

    </x-horizontal-nav.container>
</x-layout>
