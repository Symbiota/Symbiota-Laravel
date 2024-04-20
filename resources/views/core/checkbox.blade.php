@props([
    'id' => 'checkbox',
    'label' => 'label',
    'name',
    'chip' => 'chip value',
    'checked' => false
])
<div>
    <input
        class="accent-primary text-primary-content"
        type="checkbox"
        name="{{$name?? $id}}"
        id="{{ $id }}"
        data-chip="{{ $chip }}"
        @checked(old($id, $checked))
        />
    <label class="text-base-content" for="{{ $id }}">{{ $label }}</label>
</div>
