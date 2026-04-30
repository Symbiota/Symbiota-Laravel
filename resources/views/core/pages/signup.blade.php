@props(['name', 'email', 'password'])
<x-layout>
    @fragment('form')
        <form
            hx-post="{{ url('/register') }}"
            hx-swap="outerHTML"
            hx-target="this"
            class="m-auto mt-5 flex max-w-screen-sm flex-col justify-center"
        >
            @csrf
            <fieldset class="grid w-full grid-cols-1 gap-4 p-4">
                <legend class="text-primary text-2xl font-bold">Signup</legend>
                <x-input required label="Name" id="name" value="{{ $name ?? old('name') }}" />
                <x-input required label="Email" id="email" type="email" value="{{ $email ?? old('email') }}" />
                <x-input
                    required
                    label="Password"
                    id="password"
                    type="password"
                    value="{{ $password ?? old('password') }}"
                />
                <x-input
                    required
                    label="Password Confirmation"
                    id="password_confirmation"
                    type="password"
                    value="{{ $password ?? old('password_confirmation') }}"
                />
                <x-button class="w-fit" type="submit"> Sign Up </x-button>
            </fieldset>

            @if(count($errors) > 0)
                <div class="mb-4 p-4">
                    @foreach($errors->all() as $error)
                        <div class="bg-error text-error-content rounded-md p-4">{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </form>
    @endfragment
</x-layout>
