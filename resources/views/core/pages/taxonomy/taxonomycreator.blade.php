<x-layout class="p-10">
    <h1 class="text-5xl font-bold text-primary mb-8">Add New Taxon</h1>
    <div id="sciname-display-div">
        <h1 class="text-3xl text-primary mb-8">Sciname will be saved as:
            <span id="sciname-display" name="sciname-display"></span>
        </h1>
        <form>
            <x-fieldset label="Optional Quick Parser" class="mb-3">
                <x-input label="Paste name here for parsing: " class="w-1/2 mb-0" type="text" id="quickparser" name="quickparser" value="" onchange="parseName(this.form)" />
            </x-fieldset>
            <x-fieldset label="Add New Taxon" class="mb-3">
                <x-input required label="Taxon Rank: " class="w-1/12 mb-8" type="text" id="rank-id" name="rank-id" value="" />
                <x-input required label="Genus Name: " class="w-1/12 mb-8" type="text" id="unit1-name" name="unit1-name" value="" />
                <x-input required label="Specific Epithet: " class="w-1/12 mb-8" type="text" id="unit2-name" name="unit2-name" value="" />
                <x-input label="Author: " class="w-1/12 mb-8" type="text" id="author" name="author" value="" />
                <x-input required label="Parent Taxon: " class="w-1/12 mb-8" type="text" id="parent-name" name="parent-name" value="" />
                <x-input label="Notes: " class="w-1/12 mb-8" type="text" id="notes" name="notes" value="" />
                <x-input label="Source: " class="w-1/12 mb-8" type="text" id="source" name="source" value="" />
                @php
                $localitySecurity=[
                    ['title'=> 'No Security', 'value' => 0, 'disabled' => false ],
                    ['title'=> 'Hide Locality Details', 'value' => 1, 'disabled' => false ]
                ];
                @endphp
                <x-select class="mb-8" label="Locality Security" :items="$localitySecurity" id="security-status" defaultValue="'No Security'"/>
                <x-fieldset label="Acceptance Status" class="mb-3">
                    <x-radio :default_value="1"
                        :options="[ ['label' => 'Accepted', 'value' => 1], ['label' => 'Not accepted', 'value' => 2]]"
                        name="radio_options" />
                </x-fieldset>
                <x-button type="button" class="justify-center text-base" variant="neutral">Submit New Name</x-button>
            </x-fieldset>
        </form>
    </div>
</x-layout>