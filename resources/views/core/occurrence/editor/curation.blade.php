@props(['occurrence'])
<x-fieldset :legend="__('editor_occurrenceeditor.CURATION')">
    <div class="flex items-center gap-2">
        <x-input :label="__('individual.TYPE_STATUS')" />
        <x-input :label="__('individual.DISPOSITION')" />
        <x-input :label="__('individual.OCCURRENCE_ID')" />
        <x-input :label="__('includes_queryform.FIELD_NUMBER')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :label="__('glossary_addterm.LANGUAGE')" />
        <x-input :label="__('includes_queryform.LAB_PROJECT')" />
        <x-input :label="__('fieldterms_occurrenceterms.DUPLICATE_COUNT')" />
    </div>

    <x-input :label="__('fieldterms_occurrenceterms.DATA_GENERALIZATIONS')" />

    <div class="flex items-center gap-2">
        <x-input :label="__('fieldterms_occurrenceterms.INST_CODE_OVERRIDE')" />
        <x-input :label="__('includes_queryform.COL_CODE')" />
        <x-input :label="__('fieldterms_occurrenceterms.OWNER_INSTITUTION_CODE')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :label="__('fieldterms_occurrenceterms.STORAGELOCATION_CODE')" />
        <x-select
            class="min-w-60"
            :label="__('editor_occurrencetabledisplay.BASIS_REC')"
            :items="[
            [
                'title' => 'Fossil Specimen',
                'value' => 'FossilSpecimen',
                'disabled' => false
            ],
            [
                'title' => 'Human Observation',
                'value' => 'HumanObservation',
                'disabled' => false
            ],
            [
                'title' => 'Preserved Specimen',
                'value' => 'PreservedSpecimen',
                'disabled' => false
            ],
            [
                'title' => 'Living Specimen',
                'value' => 'LivingSpecimen',
                'disabled' => false
            ],
            [
                'title' => 'Machine Observation',
                'value' => 'MachineObservation',
                'disabled' => false
            ],
        ]"
        />

        <x-select
            :label="__('editor_occurrencetabledisplay.PROCESS_STATUS')"
            class="min-w-50"
            :items="[
            [
                'title' => 'No Set Status',
                'value' => 'No Set Status',
                'disabled' => false
            ],
        ]"
        />
    </div>
    <hr />

    <div class="flex">
        <x-text-label class="flex-auto" :label="__('ident_key.KEY')"> {{ $occurrence->occid }} </x-text-label>

        <x-text-label class="flex-auto" :label="__('editor_occurrenceeditor.MODIFIED')">
            {{ $occurrence->dateLastModified }}
        </x-text-label>

        <x-text-label class="flex-auto" :label="__('editor_occurrencetabledisplay.RECORD_ENTERED_BY')">
            {{ $occurrence->recordEnteredBy ?? 'not recorded' }} {{
$occurrence->dateEntered?'['
            . $occurrence->dateEntered . ']': ''
}}
        </x-text-label>
    </div>
</x-fieldset>
