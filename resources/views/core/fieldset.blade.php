@props(['id'=> uniqid(), 'name', 'label' => false, 'error_text', 'assistive_text'])
<!-- resources/views/core/input.blade.php -->
<div class="group w-full text-base-content">
    @if($label)
    <legend class="text-base-content text-base text-bold mb-1">
        {{ $label }}
    </legend>
    @endif

    <fieldset
        name="{{ $name?? $id }}" id="{{ $id }}"
        {{ $attributes->twMerge('px-3 py-2 bg-opacity-50 border-base-300 border rounded-md focus:ring-accent focus:ring-3 focus:outline-none w-full') }}>
        {{ $slot }}
    </fieldset>

    @if(isset($error_text))
    <p class="text-red-500 text-xs italic">{{ $error_text }}</p>
    @elseif(!empty($assistive_text))
    <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>