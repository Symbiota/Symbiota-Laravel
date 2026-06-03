<div class="flex flex-col gap-4">
    <x-fieldset :legend="__('includes_queryform.ASSOC_OCCS')">
        <x-fieldset :legend="__('collections_associations.CREATE_NEW_ASSOC')">
            <div class="flex gap-2">
                <x-select :label="__('collections_associations.ASSOCIATION_TYPE')" />
                <x-select :label="__('collections_associations.RELATIONSHIP')" />
                <x-select :label="__('collections_associations.REL_SUBTYPE')" />
            </div>

            <div class="flex gap-2">
                <x-select :label="__('collections_associations.BASIS_OF_RECORD')" />
                <x-input :label="__('collections_associations.LOC_ON_HOST')" />
            </div>
            <x-input :label="__('projects.NOTES')" />
            <x-button> {{ __('collections_associations.CREATE_ASSOC') }} </x-button>
        </x-fieldset>

        <div>{{ __('collections_associations.NO_ASSOCS') }}</div>
    </x-fieldset>

    <x-fieldset :legend="__('includes_resourcetab.CHECKLIST_VOUCHER_LINKAGES')">
        <div>{{ __('includes_resourcetab.NO_CHECKLISTS_LINKAGES') }}</div>

        <x-select
            :items="[
            ['title' => 'Select a Checklist', 'value'=> null, 'disabled' => false ]
        ]"
        />
        <x-button>{{ __('includes_resourcetab.LINK_TO_CHECKLIST') }}</x-button>
    </x-fieldset>

    <x-fieldset :legend="__('includes_resourcetab.SPEC_DUPES')">
        <div>{{ __('includes_resourcetab.NO_LINKED') }}</div>
        <x-button> {{ __('includes_resourcetab.SEARCH_RECS') }} </x-button>
    </x-fieldset>

    <x-fieldset :legend="__('includes_resourcetab.GEN_RES')">
        <div>{{ __('includes_resourcetab.NO_GENETIC_RESOURCES') }}</div>

        <x-fieldset :legend="__('includes_resourcetab.ADD_NEW_GEN')">
            <x-input label="Name" />
            <x-input label="Identifier" />
            <x-input label="Locus" />
            <x-input label="URL" />
            <x-input label="Notes" />
            <x-button> {{ __('includes_resourcetab.ADD_NEW_GEN_2') }} </x-button>
        </x-fieldset>
    </x-fieldset>
</div>
