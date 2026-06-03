<x-fieldset :legend="__('editor_occurrenceeditor.MISC')">
    <x-input :value="$occurrence->habitat" :label="__('checklists_checklist.HABITAT')" />
    <x-input :value="$occurrence->substrate" :label="__('individual.SUBSTRATE')" />
    <x-input area rows="1" :label="__('editor_observationsubmit.ASSOC_TAXA')" />
    <x-input :value="$occurrence->verbatimAttributes" :label="__('fieldterms_occurrenceterms.VERBATIM_ATTRIBUTES')" />
    <x-input :value="$occurrence->occurrenceRemarks" :label="__('projects.NOTES')" />

    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->lifeStage" :label="__('individual.LIFE_STAGE')" />
        <x-input :value="$occurrence->sex" :label="__('individual.SEX')" />
        <x-input :value="$occurrence->individualCount" class="min-w-35" :label="__('individual.INDIVIDUAL_COUNT')" />
        <x-input
            :value="$occurrence->samplingProtocol"
            class="min-w-37"
            :label="__('fieldterms_occurrenceterms.SAMPLING_PROTOCOL')"
        />
    </div>
    <x-input area rows="1" :label="__('individual.PREPARATIONS')" />

    <div class="flex items-center gap-2">
        <x-input
            class="min-w-50"
            :value="$occurrence->reproductiveCondition"
            :label="__('individual.REPRODUCTIVE_CONDITION')"
        />
        <x-input :value="$occurrence->behavior" :label="__('includes_queryform.BEHAVIOR')" />
        <x-input :value="$occurrence->vitality" :label="__('fieldterms_occurrenceterms.VITALITY')" />
        <x-input
            class="min-w-50"
            :value="$occurrence->establishmentMeans"
            :label="__('fieldterms_occurrenceterms.ESTABLISHMENT_MEANS')"
        />
    </div>
    <x-checkbox
        :checked="$occurrence->cultivationStatus"
        :label="__('fieldterms_occurrenceterms.CULTIVATION_STATUS')"
    />
</x-fieldset>
