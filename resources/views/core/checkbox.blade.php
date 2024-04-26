@props([
'id' => 'checkbox',
'label' => 'label',
'name',
'chip' => 'chip value',
'checked' => false
])
<div class="flex group">
    <div class="relative w-fit h-fit my-auto">
        <input {{$attributes}} type="checkbox" name="{{$name?? $id}}" id="{{ $id }}" data-chip="{{ $chip }}"
            class="z-10 w-6 h-6 peer/checkbox appearance-none before:content[''] border-2 border-secondary rounded-full checked:bg-secondary cursor-pointer outline-none">
        <i
            class="z-10 absolute peer-checked/checkbox:opacity-100 opacity-0 text-secondary-content transition-opacity pointer-events-none top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4 fa-solid fa-check"></i>

        <div class="z-0 peer-focus/checkbox:bg-opacity-30 group-hover:bg-opacity-30 bg-opacity-0 absolute bg-secondary w-8 h-8 rounded-full top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4">
        </div>
    </div>

    <label class="ml-2 relative inline-block align-middle text-base-content select-none" for="{{ $id }}">
        {{ $label}}
    </label>
</div>
