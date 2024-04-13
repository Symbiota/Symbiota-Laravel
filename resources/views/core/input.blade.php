@props(['id', 'label', 'error_text', 'assistive_text'])
<!-- resources/views/core/input.blade.php -->
<div class="group mt-6 w-full text-[#494440]">
    <span class="relative inline-block w-full">
        <label class="text-base absolute top-[-0.80rem] bg-white ml-3 px-1" for="{{ $id }}">
            {{ $label }}
            @if($attributes['aria-required'] || $attributes['required'])
            <span class="text-red-500 italic pr-1">*</span>
            @endif
        </label>
        <input
            class="p-4 bg-transparent border-[#5b616b] border focus:ring-secondary focus:ring-2 focus:outline-none w-full"
            {{ $attributes }} name="{{ $id }}" id="{{ $id }}" />
        <span data-label="{{ $label }}" />
    </span>
    @if(isset($error_text))
    <p class="text-red-500 text-xs italic">{{ $error_text }}</p>
    @elseif(!empty($assistive_text))
    <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>
