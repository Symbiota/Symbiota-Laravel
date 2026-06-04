<x-margin-layout>
    @fragment('form')
        <form hx-post="{{ url('/register') }}" hx-swap="outerHTML" hx-target="this">
            @csrf
            <fieldset class="grid w-full grid-cols-1 gap-4 p-4">
                <legend class="text-primary text-2xl font-bold">{{ __('profile_newprofile.CREATE_NEW') }}</legend>

                <x-user.form-fields
                    :username="old('username')"
                    :name="old('name')"
                    :email="old('email')"
                    :password="old('password')"
                    :include_passwords="true"
                />

                @if(is_array(__('profile_newprofile.PASSWORD_RULES')))
                    @foreach(__('profile_newprofile.PASSWORD_RULES') as $rule)
                        <div class="text-error">{{ $rule }}</div>
                    @endforeach

                @endif
                <x-button class="w-fit" type="submit"> {{ __('profile_newprofile.CREATE_NEW') }} </x-button>
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
</x-margin-layout>
