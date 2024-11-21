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
</x-layout>
