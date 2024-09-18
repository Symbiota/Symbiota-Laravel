@props(['name' => '', 'email' => '', 'password' => ''])
<x-layout>
    @fragment('signup-form')
    <form hx-post="{{ url('/signup') }}" hx-swap="outerHTML" hx-target="this" class="flex flex-col justify-center m-auto max-w-screen-sm mt-5">
        @csrf
        <fieldset class="w-full p-4 grid grid-cols-1 gap-4">
            <legend class="text-primary text-2xl font-bold">Signup</legend>
            <x-input required label="Name" id="name" value="{{ $name }}" />
            <x-input required label="Email" id="email" type="email" value="{{ $email }}" />
            <x-input required label="Password" id="password" type="password" value="{{ $password }}" />
            <x-button class="w-fit" type="submit">
                Sign Up
            </x-button>
        </fieldset>

        @if(count($errors) > 0)
        <div class="p-4 mb-4">
            @foreach ($errors as $error)
            <div class="bg-error text-error-content rounded-md p-4">
                {{ $error }}
            </div>
            @endforeach
        </div>
        @endif
    </form>
    @endfragment
</x-layout>
