<x-fieldset :legend="__('individual.LATEST_ID')">
    <div class="flex items-center gap-2">
        <x-input :label="__('imagelib_imgdetails.SCIENTIFIC_NAME')" />
        <x-input :label="__('fieldterms_occurrenceterms.SCIENTIFIC_NAME_AUTHORSHIP')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input class="w-50" :label="__('individual.ID_QUALIFIER')" />
        <x-input :label="__('taxa.FAMILY')" />
        <x-input :label="__('editor_occurrencetabledisplay.ID_BY')" />
        <x-input :label="__('fieldterms_occurrenceterms.DATE_IDENTIFIED')" />
    </div>

    {{-- TODO (Logan) toggle --}}
    <x-input :label="__('includes_queryform.IDENTIFICATION_REFERENCES')" />
    <x-input :label="__('includes_queryform.IDENTIFICATION_REMARKS')" />
    <x-input :label="__('individual.TAXON_REMARKS')" />
</x-fieldset>
