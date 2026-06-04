@props(['material_samples' => []])
<x-fieldset :legend="__('includes_materialsampleinclude.ADD_SAMPLE')">
   <div class="flex items-center gap-2">
        <x-input :label="__('material_sample.SAMPLE_TYPE')" />
        <x-input :label="__('collections_list.CATALOG_NUMBER')" />
        <x-input :label="__('material_sample.MATERIAL_SAMPLE_GUID')" />
   </div>

   <div class="flex items-center gap-2">
        <x-input :label="__('material_sample.SAMPLE_CONDITION')" />
        <x-input :label="__('individual.DISPOSITION')" />
        <x-input :label="__('material_sample.PRESERVATION_TYPE')" />
   </div>

   <div class="flex items-center gap-2">
        <x-input type="date" :label="__('material_sample.PREPARATION_DATE')" />
        <x-input :label="__('material_sample.PREPARED_BY')" />
   </div>

    <x-input :label="__('material_sample.PREPARATION_DETAILS')" />

   <div class="flex items-center gap-2">
        <x-input :label="__('individual.INDIVIDUAL_COUNT')" />
        <x-input :label="__('material_sample.SAMPLE_SIZE')" />
        <x-input :label="__('individual.STORAGE_LOC')" />
   </div>

    <x-input :label="__('georef_batchgeoreftool.REMARKS')" />

   <x-button>{{ __('includes_materialsampleinclude.ADD_SAMPLE') }}</x-button>

   @foreach($material_samples as $sample)

   @endforeach
</x-fieldset>
