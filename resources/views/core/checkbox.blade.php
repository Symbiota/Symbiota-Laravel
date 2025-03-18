@props([
'id' => uniqid(),
'label' => 'label',
'name',
'checked' => false,
'value' => 1,
'chip' => 'chip value',
])
<div {{ $attributes->twMerge("flex group") }}>
    <div class="relative w-fit h-fit my-auto">
        <input
            type="checkbox"
            name="{{$name ?? $id}}"
            id="{{ $id }}"
            data-chip="{{ $chip }}"
            autocomplete="off"
            value="{{ $value }}"
            @checked($checked)
            class="
            z-[5] w-6 h-6 peer/checkbox appearance-none before:content['']
            border-2 border-accent rounded-full
            checked:bg-accent cursor-pointer outline-none
            "
            >
        <i
            class="z-[5] absolute peer-checked/checkbox:opacity-100 opacity-0 text-accent-content transition-opacity pointer-events-none top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4 fa-solid fa-check"></i>

        <div class="z-0 pointer-events-none peer-focus/checkbox:bg-opacity-30 group-hover:bg-opacity-30 bg-opacity-0 absolute bg-accent w-8 h-8 rounded-full top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4">
        </div>
    </div>

    <label class="ml-2 relative inline-block align-middle text-base-content select-none" for="{{ $id }}">
        {{ $label}}
    </label>
</div>
