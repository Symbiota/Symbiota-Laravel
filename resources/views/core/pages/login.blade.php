<x-layout>
    @fragment('form')
    <form hx-post="{{url('/login')}}" hx-swap="outerHTML" class="flex justify-center m-auto max-w-screen-sm mt-5">
        @csrf
        <fieldset class="w-full p-4 grid grid-cols-1 gap-4">
            <legend class="text-primary text-2xl font-bold">Portal Login</legend>
            <x-input required :id="'email'" value="{{ old('email') }}" :label="'Email'" />
            <x-input required type="password" :id="'password'" value="{{ old('password') }}" :label="'Password'" />
            <x-checkbox :id="'remember-me'" :label="'Remember me on this computer'" />
            <x-button class="w-fit" type="submit">Sign In</x-button>
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
                <p>
                    Don't have an account?
                </p>
                <x-link href="{{ url('/signup') }}">Create an Account</x-link>
                <p>
                    Can't remember your password?
                </p>
                <x-link>Reset Password</x-link>
            </div>
        </fieldset>
    </form>
    @endfragment
</x-layout>
