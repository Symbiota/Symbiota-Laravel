<x-layout>
    <form hx-post="{{ url('/user/confirm-password') }}" class="flex justify-center m-auto max-w-screen-sm mt-5" method="POST">
        <fieldset class="w-full p-4 grid grid-cols-1 gap-4">
            <legend class="text-primary text-2xl font-bold">Confirm Password</legend>
            @csrf
            <x-input id="password" type="password" autocomplete="off" />
            @if(count($errors) > 0)
            <div class="mb-4">
                @foreach ($errors->all() as $error)
                <div class="bg-error text-error-content rounded-md p-4">
                    {{ $error }}
                </div>
                @endforeach
            </div>
            @endif
            <x-button class="w-fit" type="submit">Submit</x-button>
        </fieldset>
    </form>
</x-layout>
