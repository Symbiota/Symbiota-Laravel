@props(['occurrence'])

@php
    global $SERVER_ROOT;
    include_once legacy_path('/classes/OmMaterialSample.php');

    $params = request()->all();
    $materialSampleManager = new \OmMaterialSample();
    $materialSampleManager->cleanFormData($params);
    $materialSampleManager->setOccid($occurrence->occid);

    $material_samples = $materialSampleManager->getMaterialSampleArr();
    $controlTermArr = $materialSampleManager->getMSTypeControlValues();
@endphp

<div class="flex flex-col gap-4">
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
    </x-fieldset>

    @if(!empty($material_samples))
        <x-fieldset :legend="__('includes_materialsampleinclude.MAT_SAMP')">
            @foreach($material_samples as $sample)
                <span class="flex items-center">
                    <x-text-label :label="__('material_sample.SAMPLE_TYPE')">
                        {{ $sample['sampleType'] }}
                    </x-text-label>
                    <x-icons.edit class="ml-auto" />
                </span>
            @endforeach
        </x-fieldset>
    @endif(!empty($material_samples))
</div>
