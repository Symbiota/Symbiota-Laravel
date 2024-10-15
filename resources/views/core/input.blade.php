@props(['id'=> uniqid(), 'label' => false, 'error_text', 'assistive_text'])
<!-- resources/views/core/input.blade.php -->
<div class="group w-full text-base-content">
    @if($label)
    <label class="text-base-content text-lg text-bold mb-1">
        {{ $label }}
        @if($attributes['aria-required'] || $attributes['required'])
        <span class="vertical-align text-error italic pr-1">*</span>
        @endif
    </label>
    <span data-label="{{ $label }}" />
    @endif
    <input
        {{ $attributes->twMerge('px-3 py-2 bg-opacity-50 border-base-300 border rounded-md focus:ring-accent focus:ring-2 focus:outline-none w-full
        ') }} name="{{ $id }}" id="{{ $id }}" />
    @if(isset($error_text))
    <p class="text-red-500 text-xs italic">{{ $error_text }}</p>
    @elseif(!empty($assistive_text))
    <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>
