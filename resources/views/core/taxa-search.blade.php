@props([
    'id' => uniqid(),
    'name' => 'taxa',
    'tidName' => 'tid',
    'label' => 'Search Taxa',
    'taxa_value' => '' ,
    'tid_value' => '',
    'use_thes_value' => false,
    'taxa_type_value' => '',
    'hide_selector' => false,
    'hide_synonyms_checkbox' => false,
    'label' => 'Search Taxa',
])
@php
$use_thes_id = 'usethes-' . $id;
$taxa_type_id = 'taxa-type-' . $id;
$includes = !$hide_synonyms_checkbox? ['#' . $use_thes_id]: [];
@endphp
<div>
    <label class="text-lg" for="{{ $id }}">{{ $label }}</label>
    <div class="group flex items-center">
        @if(!$hide_selector)
            @php $includes[] = '#' . $taxa_type_id @endphp
            <x-select
                name="taxa-type"
                :id="$taxa_type_id"
                :default="0"
                class="mr-1"
                class:button="rounded-r-none"
                :items="[
            ['value' => 'Any Name', 'title' => 'Any Name', 'disabled' => false],
            ['value' => 'Family', 'title' => 'Family', 'disabled' => false],
            ['value' =>'Taxonomy Group', 'title' => 'Taxonomy Group', 'disabled' => false]
        ]"
            />
        @endif

        @php
            $includeSelectors = collect([
                !$hide_synonyms_checkbox ? "#usethes-{$id}" : null,
                !$hide_selector ? "#taxa-type-{$id}" : null,
            ])->filter()->implode(', ');
            $vals = $name !== 'taxa' ? 'js:{"taxa": (document.getElementById("' . $id . '")?.value ?? "")}' : '';
        @endphp
        <x-autocomplete-input
            :name="$name"
            :id="$id"
            :value="$taxa_value"
            placeholder="Type to search..."
            search="{{ url('/api/taxa/search') }}"
            :include="implode(',', $includes)"
        >
            <x-slot
                name="input"
                @auto_input_select="document.querySelector('#{{ 'tid-' . $id }}').value = event.detail.selection.id"
                @input="document.querySelector('#{{ 'tid-' . $id }}').value = ''"
                hx-on:htmx:config-request="event.detail.parameters.taxa = this.value"
                class="peer-input z-20 rounded-l-none"
            ></x-slot>
            <x-slot name="menu"></x-slot>
        </x-autocomplete-input>
        <input id="{{ 'tid-' . $id }}" type="hidden" name="{{ $tidName }}" value="{{ $tid_value }}" />
    </div>

    @if(!$hide_synonyms_checkbox)
        <x-checkbox
            :id="$use_thes_id"
            :checked="$use_thes_value === 1"
            class="mt-2"
            name="usethes"
            label="Include Synonyms"
        />
    @endif
</div>
