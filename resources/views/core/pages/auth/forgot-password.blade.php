<x-layout>
    <form hx-post="{{ url('forgot-password') }}" hx-swap="outerHTML">
    @csrf
    <fieldset class="w-full p-4 grid grid-cols-1 gap-4">
        <legend class="text-primary text-2xl font-bold">Reset Password</legend>
        <x-input type="email" label="Email" id="email" />
        <x-button type="submit">Send Email</x-button>
    </fieldset>
    </form>

    @if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
    </div>
    @endif
</x-layout>
