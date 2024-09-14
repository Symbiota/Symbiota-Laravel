@props(['options'=> [], 'default_value' => null, 'name', 'label' => '', 'required' => false])
<fieldset class="flex flex-row gap-2">
    <legend class="text-xl text-bold">{{ $label }}</legend>

    @foreach ($options as $option)
    @php
       $id = $option['id']?? uniqid();
    @endphp
    <div class="flex gap-2 items-start mb-1 items-center">
        <div class="grid place-items-center">
            <input
                class="peer col-start-1 row-start-1 appearance-none shrink-0
                w-6 h-6 border-2 hover:border-4 border-accent disabled:border-neutral rounded-full outline-none"
                type="radio"
                autocomplete="off"
                name="{{ $name }}"
                id="{{ $id }}"
                value="{{ $option['value'] }}"
                data-value="{{old($id, $default_value === $option['value'])}}"
                @checked($default_value == $option['value'])
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
        <label for="{{ $id }}">
            {{ $option['label'] }}
        </label>
    </div>
    @endforeach
</fieldset>
