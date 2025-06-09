@props([
    'id' => uniqid(),
    'taxa_value' => '' ,
    'use_thes_value' => false,
    'taxa_type_value' => '',
    'include' => '',
    'hide_selector' => false,
    'hide_synonyms_checkbox' => false,
])
<div>
    <label class="text-lg" for="{{ $id }}">Search Taxa</label>
    <div class="flex items-center group">
        @if(!$hide_selector)
        <x-select
            name="taxa-type"
            id="taxa-type-{{$id}}"
            :default="0"
            class="mr-1"
            class:button="rounded-r-none"
            :items="[
            ['value' => 'Any Name', 'title' => 'Any Name', 'disabled' => false],
            ['value' => 'Family', 'title' => 'Family', 'disabled' => false],
            ['value' =>'Taxonomy Group', 'title' => 'Taxonomy Group', 'disabled' => false]
        ]"/>
        @endif

        <x-autocomplete-input
            name="taxa"
            :id="$id"
            :value="$taxa_value"
            placeholder="Type to search..."
            search="{{url('/api/taxa/search')}}"
            include="#usethes-{{$id}}, #taxa-type-{{$id}} {{$include ? ', ' . $include: ''}}"
        >
            <x-slot:input class="peer-input z-20 rounded-l-none"></x-slot>
                <x-slot:menu></x-slot>
        </x-autocomplete-input>
    </div>

    @if(!$hide_synonyms_checkbox)
    <x-checkbox
        :id="'usethes-' . $id"
        :checked="$use_thes_value === 1"
        class="mt-2"
        name="usethes"
        label="Include Synonyms"
    />
    @endif
</div>
