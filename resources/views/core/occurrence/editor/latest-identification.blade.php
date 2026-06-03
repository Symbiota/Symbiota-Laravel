<x-fieldset :legend="__('individual.LATEST_ID')">
    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->sciname" :label="__('imagelib_imgdetails.SCIENTIFIC_NAME')" />
        <x-input :value="$occurrence->scientificNameAuthorship" :label="__('fieldterms_occurrenceterms.SCIENTIFIC_NAME_AUTHORSHIP')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input class="w-50" :value="$occurrence->identificationQualifier" :label="__('individual.ID_QUALIFIER')" />
        <x-input :value="$occurrence->family" :label="__('taxa.FAMILY')" />
        <x-input :value="$occurrence->identifiedBy" :label="__('editor_occurrencetabledisplay.ID_BY')" />
        <x-input :value="$occurrence->dateIdentified" :label="__('fieldterms_occurrenceterms.DATE_IDENTIFIED')" />
    </div>

    {{-- TODO (Logan) toggle --}}
    <x-input :value="$occurrence->identificationReferences" :label="__('includes_queryform.IDENTIFICATION_REFERENCES')" />
    <x-input :value="$occurrence->identificationRemarks" :label="__('includes_queryform.IDENTIFICATION_REMARKS')" />
    <x-input :value="$occurrence->taxonRemarks" :label="__('individual.TAXON_REMARKS')" />
</x-fieldset>
