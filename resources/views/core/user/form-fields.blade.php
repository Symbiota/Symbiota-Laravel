@props([
    'username',
    'name',
    'email',
    'password',
    'include_passwords' => false,
    'accessibilityPref' => false,
    'guid' => null,
    'title' => null,
    'institution' => null,
    'city' => null,
    'state' => null,
    'zip' => null,
    'country' => null,
])
<x-input required label="Username" id="username" value="{{ $username }}" />
<x-input required label="Name" id="name" value="{{ $name }}" />
<x-input required label="Email" id="email" type="email" value="{{ $email }}" />

@if($include_passwords)
<x-input
    required
    label="Password"
    id="password"
    type="password"
    value="{{ $password }}"
/>
<x-input
    required
    label="Password Confirmation"
    id="password_confirmation"
    type="password"
/>
@endif

<x-checkbox
    id="accessibilityPref"
    :label="__('profile_newprofile.ACCESSIBILITY_PREF')"
    :checked="$accessibilityPref"
/>
<label for="accessibilityPref">
    {{ __('profile_newprofile.ACCESSIBILITY_PREF_DESC') }}
</label>

@if(!$include_passwords)
<x-input id="guid" :label="__('profile_newprofile.ORCID')" :value="$guid" />
<x-input id="title" :label="__('exsiccati.TITLE')" :value="$title" />
<x-input id="institution" :label="__('profile_newprofile.INSTITUTION')" :value="$institution" />
<x-input id="city" :label="__('profile_newprofile.CITY')" :value="$city"/>
<x-input id="state" :label="__('profile_newprofile.STATE')" :value="$state"/>
<x-input id="zip" :label="__('profile_newprofile.ZIP_CODE')" :value="$zip" />
<x-input id="country" :label="__('collections_list.COUNTRY')" :value="$country" />
@endif
