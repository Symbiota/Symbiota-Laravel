@props(['occurrence', 'traits' => []])
<div class="flex flex-col gap-4">
    <div class="flex flex-col gap-4 border p-4">
        @foreach($traits as $traitID => $traitData)
        @php if(isset($traitData['dependentTrait'])) continue @endphp
        <x-fieldset :legend="$traitData['name']">
            {{-- Numerical --}}
            <x-traits.form-input :traits="$traits" :traitId="$traitID" />
        </x-fieldset>
        @endforeach
        <div>
            <div class="flex">
                <h3 class="text-lg font-bold">Anglosperm Phenolgical Traits</h3>
            </div>
        </div>

        {{-- TODO (Logan) Update this to have nested attribute tree--}}
        <x-radio
            name="pheno-traits"
            :options="[
            ['label' => 'Reproductive', 'value' => 'reproductive'],
            ['label' => 'Sterile', 'value' => 'sterile'],
            ['label' => 'Not Scorable', 'value' => 'not-scorable']
        ]"
        />

        <div class="font-bold">Add New Resource</div>
        <x-input label="Notes" />
        <x-select
            class="w-60"
            label="Source"
            :items="[
            ['title' => 'Machine Learning', 'value' => 'machine_learning', 'disabled' => false],
            ['title' => 'Physical Specimen', 'value' => 'physical_specimen', 'disabled' => false],
            ['title' => 'Viewing Image', 'value' => 'viewing_image', 'disabled' => false],
            ['title' => 'Verbatim Text Mining', 'value' => 'verbatim_text_mining', 'disabled' => false],
        ]"
        />

        <x-select
            class="w-60"
            label="Status"
            :items="[
            ['title' => 'Not Reviewed', 'value' => 'not_reviewed', 'disabled' => false],
            ['title' => 'Expert Needed', 'value' => 'expert_needed', 'disabled' => false],
            ['title' => 'Reviewed', 'value' => 'reviewed', 'disabled' => false],
        ]"
        />
        <x-button>Save Edits</x-button>
        <x-button variant="error">Delete Coding</x-button>
    </div>
</div>
