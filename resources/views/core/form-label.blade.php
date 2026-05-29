@props(['label', 'for' => null, 'id' => null, 'required' => false, 'inline' => false ])

<label
    id="{{ $id }}"
    for="{{ $for }}"
    @class(['text-base-content text-base font-bold', 'mb-1' => !$inline, 'flex items-center' => $inline])
>
    {{ $label }}
    @if($required)
        <span class="vertical-align text-error pr-1 italic">*</span>
    @endif
    @if($inline)
        <span>:</span>
    @endif
</label>
<span data-label="{{ $label }}"></span>
