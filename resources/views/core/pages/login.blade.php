<x-layout>
    <form class="flex justify-center m-auto max-w-screen-sm text-center mt-5">
        <fieldset class="w-full p-4">
            <legend class="text-primary text-2xl font-bold">Portal Login</legend>
            <x-input :id="'username'" :label="'Username or Email'" />
            <x-input type="password" :id="'password'" :label="'Password'" />
            <div class="mt-4">
                <x-button>Sign In</x-button>
            </div>
            <div class="mt-4">
                <p>
                    Don't have an account?
                </p>
                <x-link>Create an Account</x-link>
                <p>
                    Can't remember your password?
                </p>
                <x-link>Reset Password</x-link>
                <p>
                    Can't remember your login name?
                </p>
                <x-link>Retrieve Login</x-link>
            </div>
        </fieldset>
    </form>
</x-layout>
