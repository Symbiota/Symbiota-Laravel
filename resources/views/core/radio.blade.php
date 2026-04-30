@props(['options'=> [], 'default_value' => null, 'name', 'label' => '', 'required' => false])
<fieldset class="flex flex-row gap-2">
    <legend class="text text-bold">{{ $label }}</legend>

    @foreach($options as $option)
        <x-radio.item
            :label="$option['label']"
            :id="$option['id'] ?? uniqid()"
            :name="$name"
            :value="$option['value']"
            :checked="$default_value == $option['value']"
        />
    @endforeach
</fieldset>
