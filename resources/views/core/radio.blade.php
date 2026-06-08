@props(['options'=> [], 'default_value' => null, 'name', 'label' => '', 'required' => false])
<fieldset class="flex flex-row gap-2">
    <legend class="text-base-content text-base font-bold">
        {{ $label }}
        @if($required)
            <span class="vertical-align text-error pr-1 italic">*</span>
        @endif
    </legend>

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
