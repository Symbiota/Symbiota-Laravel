<x-layout>
    <form
        hx-post="{{ url('forgot-password') }}"
        hx-swap="outerHTML"
        class="m-auto mt-5 flex max-w-screen-sm justify-center"
    >
        @csrf
        <fieldset class="grid w-full grid-cols-1 gap-4 p-4">
            <legend class="text-primary text-2xl font-bold">Reset Password</legend>
            <x-input type="email" label="Email" id="email" />
            <x-button type="submit">Send Email</x-button>
            @if(session('status'))
                <div class="mb-4 text-sm font-medium text-green-600">{{ session('status') }}</div>
            @endif
            @if(count($errors) > 0)
                <div class="mb-4">
                    @foreach($errors->all() as $error)
                        <div class="bg-error text-error-content rounded-md p-4">{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <div>
                <x-link hx-boost="true" href="{{ url('/login') }}">Back to Login</x-link>
            </div>
        </fieldset>
    </form>
</x-layout>
