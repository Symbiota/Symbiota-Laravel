<x-layout class="sm:w-[90%] lg:w-[70%] m-auto">
    <h1 class="text-4xl font-bold">User Settings</h1>
    @if(session('status') == 'two-factor-authentication-confirmed')
    <div class="flex flex-col gap-4">
        <p class="font-medium">
            Two factor authentication confirmed and enabled successfully.
        </p>
        <p>
            These are the recovery codes needed to get back into the account. Keep these in a safe place losing them may
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

        <form hx-post="{{url('/user/confirmed-two-factor-authentication')}}" hx-swap="outerHTML" hx-target="body"
            class="flex flex-col gap-4">
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
    <div>
        @fragment('tokens')
        <div id="tokens-container" class="border border-base-300">
            <div class="p-2 flex items-center gap-2">
                <div class="text-xl font-bold flex-grow">Personal access tokens </div>
                <form class="m-0" hx-swap="outerHTML" hx-target="#tokens-container" hx-post="{{ url('token/create') }}">
                    <input type="hidden" name="token_name" value="new_token">
                    @csrf
                    <x-button>Generate new token</x-button>
                </form>
            </div>
            @foreach ($user_tokens as $token)
            <div class="p-4 border-t border-base-300">
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
                    <x-button variant="error" hx-swap="outerHTML" hx-include="input[name='_token']" hx-target="#tokens-container" hx-delete="{{url('token/delete/' . $token->id)}}">Delete</x-button>
                </div>
                @if($token->expires_at)
                <div>Expires {{ $token->expires_at }}</div>
                @else
                <div class="text-warning font-bold underline">This token has no expiration date.</div>
                @endif
            </div>
            @endforeach
        </div>
        @endfragment
    </div>
</x-layout>
