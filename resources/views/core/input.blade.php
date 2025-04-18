@props(['id'=> uniqid(), 'name', 'label' => false, 'error_text', 'assistive_text', 'area' => false])
<!-- resources/views/core/input.blade.php -->
<div class="group w-full text-base-content">
    @if($label)
    <label class="text-base-content text-base text-bold mb-1">
        {{ $label }}
        @if($attributes['aria-required'] || $attributes['required'])
        <span class="vertical-align text-error italic pr-1">*</span>
        @endif
    </label>
    <span data-label="{{ $label }}" />
    @endif

    @if($area)
    <textarea
        name="{{ $name?? $id }}" id="{{ $id }}"
        {{ $attributes->twMerge('px-3 py-2 bg-opacity-50 border-base-300 border rounded-md focus:ring-accent focus:ring-2 focus:outline-none w-full') }}
    >{{ $slot }}</textarea>
    @else

    <input
        {{ $attributes->twMerge('px-3 py-2 bg-opacity-50 border-base-300 border rounded-md focus:ring-accent focus:ring-2 focus:outline-none w-full
        ') }} name="{{ $name ?? $id }}" id="{{ $id }}" />
    @endif

    @if(isset($error_text))
    <p class="text-red-500 text-xs italic">{{ $error_text }}</p>
    @elseif(!empty($assistive_text))
    <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>
