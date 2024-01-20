@props([
    'id' => 'checkbox',
    'label' => 'label',
    'name',
    'chip' => 'chip value',
    'checked' => false
])
<div>
    <input
        type="checkbox"
        name="{{$name?? $id}}"
        id="{{ $id }}"
        data-chip="{{ $chip }}"
        @checked(old($id, $checked))
        />
    <label for="{{ $id }}">{{ $label }}</label>
</div>
