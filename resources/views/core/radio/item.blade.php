@props(['label', 'name', 'id' => uniqid(), 'value', 'checked' => false])
<div class="flex items-center items-start gap-2">
    <div class="grid place-items-center">
        <input
            class="peer border-accent disabled:border-neutral col-start-1 row-start-1 h-6 w-6 shrink-0 cursor-pointer appearance-none rounded-full border-2 outline-none hover:border-4"
            type="radio"
            autocomplete="off"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            data-value="{{ old($id, $checked) }}"
            @checked($checked)
            {{ $attributes }}
        />
        <div class="peer-checked:bg-accent pointer-events-none col-start-1 row-start-1 h-4 w-4 rounded-full"></div>
        <div
            class="ring-accent pointer-events-none col-start-1 row-start-1 h-7 w-7 rounded-full opacity-70 peer-hover:ring-4 peer-focus:ring-4"
        ></div>
    </div>
    <label class="my-auto cursor-pointer" for="{{ $id }}"> {{ $label }} </label>
</div>
