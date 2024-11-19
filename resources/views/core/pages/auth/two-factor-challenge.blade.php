<x-layout>
    <form hx-post="/two-factor-challenge" hx-target="body" hx-swap="outerHTML">
        @csrf
        <x-input label="Code" id="code" />
        @foreach ($errors->all() as $error)
        <div class="bg-error text-error-content rounded-md p-4">
            {{ $error }}
        </div>
        @endforeach
        <x-button class="w-fit" type="submit">Verify</x-button>
        {{-- <x-input label="Recovery Code" id="recovery_code" /> --}}
    </form>
</x-layout>
