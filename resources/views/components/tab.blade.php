@props(['id', 'label', 'group_id' => 'tab-group'])
{{--
    If There are seperate tab groups a group_id prop must be passed in
--}}
<input
    type="radio"
    class="tabs__radio"
    name="{{ $group_id }}"
    id="{{ $id }}"
    checked
>
<label
    for="{{ $id }}"
    class="tabs__label"
    tabindex="0"
    >
    {{ $label }}
</label>
<div class="tabs__content">
    {{ $slot }}
</div>
