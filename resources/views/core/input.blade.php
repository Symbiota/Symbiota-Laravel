@props(['id'=> uniqid(), 'name' => $id, 'label' => false, 'error_text', 'assistive_text', 'area' => false, 'inline' => false, 'x-show'])

<!-- resources/views/core/input.blade.php -->
<div
    @class([
        'group text-base-content',
        'flex flex-wrap items-center gap-1' => $inline,
        'w-full' => !$inline
    ])
    @isset(${'x-show'}) x-show="{{ ${'x-show'} }}"@endisset
>
    @if($label)
        <x-form-label
            :label="$label"
            :for="$id"
            :required="$attributes['aria-required'] || $attributes['required']"
            :inline="$inline"
        />
    @endif

    @php
        $inputStyles = [
            'px-1 py-0.25 border-base-300 border rounded-md focus:ring-accent focus:ring-2 focus:outline-none',
            $inline? 'grow': 'w-full'
        ];
    @endphp

    @if($area)
        <textarea name="{{ $name }}" id="{{ $id }}" {{ $attributes->twMerge($inputStyles) }}>{{ $slot }}</textarea>
    @else
        <input {{ $attributes->twMerge($inputStyles) }} name="{{ $name }}" id="{{ $id }}" />
    @endif

    @if(isset($error_text))
        <p class="text-xs text-red-500 italic">{{ $error_text }}</p>
    @elseif(!empty($assistive_text))
        <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>
