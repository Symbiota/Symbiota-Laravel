{{-- TODO (Logan) create uuid as default --}}
{{-- TODO (Logan) make data-chip optional trait --}}
@props([
    'id' => 'input',
    'label' => 'label',
    'assistive_text',
    'chip',
    'name' => false,
    'type' => 'text',
])
<div class="input-text-container">
    <label for="{{ $id }}" class="input-text--outlined">
        <span class="skip-link">{!! $label !!}</span>
        {{-- TODO (Logan) make data-chip optional trait --}}
        <input type="{{ $type }}" name="{{ $name?? $id}}" id="{{ $id }}" data-chip="{{ $chip }}">
        <span data-label="{!! $label !!}"></span>
    </label>
    @if(!empty($assistive_text))
        <span class="assistive-text">{{ $assistive_text }}</span>
    @endif
</div>
