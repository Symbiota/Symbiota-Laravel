<x-layout>
    @fragment('login-form')
    <form action="{{url('/login')}}" method="post" class="flex justify-center m-auto max-w-screen-sm mt-5">
        @csrf
        <fieldset class="w-full p-4 grid grid-cols-1 gap-4">
            <legend class="text-primary text-2xl font-bold">Portal Login</legend>
            <x-input required :id="'email'" :label="'Email'" />
            <x-input required type="password" :id="'password'" :label="'Password'" />
            <x-checkbox :id="'remember-me'" :label="'Remember me on this computer'" />
            <x-button class="w-fit" type="submit">Sign In</x-button>
            <div>
                <p>
                    Don't have an account?
                </p>
                <x-link href="/signup">Create an Account</x-link>
                <p>
                    Can't remember your password?
                </p>
                <x-link>Reset Password</x-link>
            </div>
        </fieldset>
    </form>
    @endfragment
</x-layout>
