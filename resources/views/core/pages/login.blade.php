<x-layout>
    @fragment('form')
        <form
            method="post"
            action="{{ url('/login') }}"
            hx-post="{{ url('/login') }}"
            hx-swap="outerHTML"
            class="m-auto mt-5 flex max-w-screen-sm justify-center"
        >
            @csrf
            <fieldset class="grid w-full grid-cols-1 gap-4 p-4">
                <legend class="text-primary text-2xl font-bold">Portal Login</legend>
                <x-input required :id="'email'" value="{{ old('email') }}" :label="'Email'" />
                <x-input required type="password" :id="'password'" value="{{ old('password') }}" :label="'Password'" />
                <x-checkbox :id="'remember-me'" :label="'Remember me on this computer'" />
                <x-button class="w-fit" type="submit">Sign In</x-button>
                @if(count($errors) > 0)
                    <div class="mb-4">
                        @foreach($errors->all() as $error)
                            <div class="bg-error text-error-content rounded-md p-4">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <div class="no-wrap flex items-center">
                    <hr class="border-base-300 flex-grow border-2" />
                    <div class="text-base-content/50 mx-4">OR</div>
                    <hr class="border-base-300 flex-grow border-2" />
                </div>
                <a href="{{ url('/auth/redirect') }}">
                    <button
                        type="button"
                        class="border-base-300 hover:bg-base-200 flex w-full items-center gap-4 rounded-md border p-4"
                    >
                        <img
                            class="h-10 w-10"
                            src="https://orcid.filecamp.com/static/thumbs/42DBWh3MuwolJCUX-small.png"
                        />
                        <span> SIGN IN WITH ORCID </span>
                    </button>
                </a>

                <div>
                    <p>Don't have an account?</p>
                    <x-link href="{{ url('/register') }}">Create an Account</x-link>
                    <p>Can't remember your password?</p>
                    <x-link hx-boost="true" href="{{ url('forgot-password') }}">Reset Password</x-link>
                </div>
            </fieldset>
        </form>
    @endfragment
</x-layout>
