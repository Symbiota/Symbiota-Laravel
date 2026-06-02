<x-fieldset class="w-fit" :legend="__('includes_imgprocessor.LABEL_PROCESSING')">
    <div class="flex items-center gap-2">
        <x-link> {{ __('includes_imgprocessor.ZOOM') }} </x-link>
        <span>
            <i class="fa-solid fa-arrows-up-down-left-right"></i>
        </span>
        <span>
            <i class="fa-solid fa-note-sticky"></i>
        </span>
        <span>
            <i class="fa-solid fa-anchor"></i>
        </span>

        <x-text-label :label="__('includes_imgprocessor.ROTATE')">
            <x-link>L</x-link>
            <span><></span>
            <x-link>R</x-link>
        </x-text-label>

        <x-radio
            id="imgres"
            default_value="med"
            name="imgres"
            :options="[
            ['label' => __('traitattr_occurattributes.MED_RES'), 'value' => 'med'], ['label' => __('traitattr_occurattributes.HIGH_RES'), 'value' => 'lg']
        ]"
        />
    </div>

    <div class="bg-base-300 mx-auto h-100 w-100">
        {{-- TODO (Logan) image --}}
    </div>

    <x-fieldset :legend="__('includes_imgprocessor.TESSERACT_OCR')">
        <x-checkbox :label="__('includes_imgprocessor.OCR_WHOLE_IMG')" />
        <x-checkbox :label="__('includes_imgprocessor.OCR_ANALYSIS')" />
        <x-button> {{ __('includes_imgprocessor.OCR_IMAGE') }} </x-button>
    </x-fieldset>
</x-fieldset>
