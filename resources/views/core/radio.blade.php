@props(['options'=> [], 'default_value' => null, 'name', 'label' => '', 'required' => false])
<fieldset>
    <legend class="text-lg text-bold">{{ $label }}</legend>

    @foreach ($options as $option)
    @php
       $id = $option['id']?? uniqid();
    @endphp
    <div class="flex gap-2 items-start mb-1">
        <div class="grid place-items-center">
            <input
                class="peer col-start-1 row-start-1 appearance-none shrink-0
                w-6 h-6 border-2 border-accent disabled:border-neutral rounded-full"
                type="radio"
                autocomplete="off"
                name="{{ $name }}"
                id="{{ $id }}"
                value="{{ $option['value'] }}"
                data-value="{{old($id, $default_value === $option['value'])}}"
                @checked(old($id, $default_value == $option['value']))
            >
            <div class="
                pointer-events-none
                col-start-1 row-start-1 w-4 h-4 rounded-full
                peer-checked:bg-accent
            "
            ></div>
        </div>
        <label for="{{ $id }}">
            {{ $option['label'] }}
        </label>
    </div>
    @endforeach
</fieldset>
