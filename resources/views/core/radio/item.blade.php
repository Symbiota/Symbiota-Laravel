@props(['label', 'name', 'id' => uniqid(), 'value', 'checked' => false])
<div class="flex gap-2 items-start items-center">
    <div class="grid place-items-center">
        <input
            class="peer col-start-1 row-start-1 appearance-none shrink-0
            w-6 h-6 border-2 hover:border-4 border-accent disabled:border-neutral rounded-full outline-none cursor-pointer"
            type="radio"
            autocomplete="off"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            data-value="{{old($id, $checked)}}"
            @checked($checked)
            {{ $attributes }}
        >
        <div class="
            pointer-events-none
            col-start-1 row-start-1 w-4 h-4 rounded-full
            peer-checked:bg-accent
        "
        ></div>
        <div class="
            pointer-events-none
            col-start-1 row-start-1 w-7 h-7 rounded-full
            opacity-70
            ring-accent
            peer-hover:ring-4
            peer-focus:ring-4
        "
        ></div>
    </div>
    <label class="my-auto cursor-pointer" for="{{ $id }}">
        {{ $label }}
    </label>
</div>
