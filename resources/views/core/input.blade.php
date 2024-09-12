@props(['id', 'label', 'error_text', 'assistive_text'])
<!-- resources/views/core/input.blade.php -->
<div class="group w-full text-base-content">
    <label class="text-base-content text-lg text-bold mb-1">
        {{ $label }}
        @if($attributes['aria-required'] || $attributes['required'])
        <span class="vertical-align text-error italic pr-1">*</span>
        @endif
    </label>
    <input
        class="p-1 bg-base-200 bg-opacity-50 focus:bg-base-100 border-base-300 border rounded-md focus:ring-accent focus:ring-2 focus:outline-none w-full"
        {{ $attributes }} name="{{ $id }}" id="{{ $id }}" />
    <span data-label="{{ $label }}" />
    @if(isset($error_text))
    <p class="text-red-500 text-xs italic">{{ $error_text }}</p>
    @elseif(!empty($assistive_text))
    <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>
