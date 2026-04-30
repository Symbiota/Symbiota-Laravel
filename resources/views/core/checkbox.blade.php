@props([
'id' => uniqid(),
'label' => 'label',
'name',
'checked' => false,
'value' => 1,
'chip' => 'chip value',
])
<div {{ $attributes->twMerge("flex group") }}>
    <div class="relative h-6 w-6">
        <input
            type="checkbox"
            name="{{ $name ?? $id }}"
            id="{{ $id }}"
            data-chip="{{ $chip }}"
            autocomplete="off"
            value="{{ $value }}"
            @bind(checked)
            @bind(disabled)
            @checked($checked)
            class="peer/checkbox before:content[''] border-accent checked:bg-accent z-[5] h-6 w-6 cursor-pointer appearance-none rounded-full border-2 outline-none"
        />
        <i
            class="text-accent-content fa-solid fa-check pointer-events-none absolute top-2/4 left-2/4 z-[5] -translate-x-2/4 -translate-y-2/4 opacity-0 transition-opacity peer-checked/checkbox:opacity-100"
        ></i>

        <div
            class="peer-focus/checkbox:bg-accent/30 group-hover:bg-accent/30 bg-accent/0 pointer-events-none absolute top-2/4 left-2/4 z-0 h-8 w-8 -translate-x-2/4 -translate-y-2/4 rounded-full"
        ></div>
    </div>

    @if($label)
        <label class="text-base-content relative ml-2 inline-block align-middle select-none" for="{{ $id }}">
            {{ $label }}
        </label>
    @endif
</div>
