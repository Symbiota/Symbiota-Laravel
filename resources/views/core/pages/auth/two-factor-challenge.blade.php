<x-layout :hasHeader="false" :hasFooter="false" :hasNavbar="false" class="flex flex-col bg-base-100">
    <div class="border border-base-300 rounded-md w-[32rem] mx-auto my-auto h-fit">
        <div class="bg-primary rounded-t-md">
            <img src="{{ url('icons/brand.svg') }}" class="w-64 mx-auto" />
        </div>
        <form hx-post="{{ url('/two-factor-challenge') }}" hx-target="body" hx-swap="outerHTML"
            class="flex flex-col gap-4 p-10" x-data="{ six_digits: false, recovery_code_mode: false }">
            <h1 class="font-bold text-2xl text-base-content">Verify Two-Factor Authentication</h1>
            <p class="text-base-content/50">
                Enter the six digit code from your authentication app
            </p>
            <hr />
            @csrf

            <div class="flex flex-col gap-4" x-show="!recovery_code_mode">
                <x-input
                    required
                    x-bind:required="!recovery_code_mode"
                    label="Verification Code"
                    id="code"
                    autocomplete="off"
                    x-on:input="six_digits = $el.value && $el.value.length >= 6" />
                <x-link @click="recovery_code_mode = true" href="#code">Use Recovery Code</x-link>
            </div>
            <div class="flex flex-col gap-4" x-show="recovery_code_mode" x-cloak >
                <x-input required x-bind:required="recovery_code_mode" label="Recovery Code" id="recovery_code"
                    autocomplete="off"
                />
                <x-link @click="recovery_code_mode = false" href="#recovery_code">Use Verification Code</x-link>
            </div>

            <x-checkbox id="remember-me" label="Trust Device" />
            @foreach ($errors->all() as $error)
            <div class="bg-error text-error-content rounded-md p-4">
                {{ $error }}
            </div>
            @endforeach
            <hr />
            <div class="flex gap-2">
                <x-button class="w-fit" type="submit" x-bind:disabled="!six_digits && !recovery_code_mode">Submit</x-button>
                <x-button href="{{ url('login') }}" hx-boost="true" class="w-fit" type="button"
                    variant="neutral">Cancel</x-button>
            </div>
        </form>
    </div>
</x-layout>
