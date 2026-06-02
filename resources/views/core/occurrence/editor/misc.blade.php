<x-fieldset :legend="__('editor_occurrenceeditor.MISC')">
    <x-input :label="__('checklists_checklist.HABITAT')" />
    <x-input :label="__('individual.SUBSTRATE')" />
    <x-input area rows="1" :label="__('editor_observationsubmit.ASSOC_TAXA')" />
    <x-input :label="__('fieldterms_occurrenceterms.VERBATIM_ATTRIBUTES')" />
    <x-input :label="__('projects.NOTES')" />

    <div class="flex items-center gap-2">
        <x-input :label="__('individual.LIFE_STAGE')" />
        <x-input :label="__('individual.SEX')" />
        <x-input class="min-w-35" :label="__('individual.INDIVIDUAL_COUNT')" />
        <x-input class="min-w-37" :label="__('fieldterms_occurrenceterms.SAMPLE_PROTOCOL')" />
    </div>
    <x-input area rows="1" :label="__('individual.PREPARATIONS')" />

    <div class="flex items-center gap-2">
        <x-input :label="__('individual.REPRODUCTIVE_CONDITION')" />
        <x-input :label="__('includes_queryform.BEHAVIOR')" />
        <x-input :label="__('fieldterms_occurrenceterms.VITALITY')" />
        <x-input class="min-w-50" :label="__('fieldterms_occurrenceterms.ESTABLISHMENT_MEANS')" />
    </div>
    <x-checkbox :label="__('fieldterms_occurrenceterms.CULTIVATION_STATUS')" />
</x-fieldset>
