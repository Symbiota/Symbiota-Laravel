<x-fieldset :legend="__('imagelib_imgdetails.LOCALITY')">
    <div class="flex items-center gap-2">
        <x-input :label="__('collections_list.COUNTRY')" />
        <x-input :label="__('fieldterms_occurrenceterms.STATEPROVINCE')" />
        <x-input :label="__('includes_queryform.MUNICIPALITY')" />
        <x-input :label="__('includes_queryform.LOCATION_ID')" />
    </div>
    <x-input area :label="__('imagelib_imgdetails.LOCALITY')" />
    {{-- todo toggle --}}
    <x-input :label="__('includes_queryform.LOC_REMARKS')" />

    <div class="flex items-center gap-2">
        {{-- TODO (Logan) todo add inline when pr passes --}}
        <x-select
            name="locality_security"
            :label="__('taxonomy_taxoneditor.LOC_SECURITY')"
            :items="[
            [
                'title' => 'Security Applied',
                'value' => 'Security Applied',
                'disabled' => false
            ],
            [
                'title' => 'Security Not Applied',
                'value' => 'Security Not Applied',
                'disabled' => false
            ],
        ]"
        />
        <x-checkbox :label="__('editor_occurrenceeditor.DEACTIVATE_LOOKUP')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input class="min-w-22" :label="__('search.LATITUDE')" />
        <x-input class="min-w-22" :label="__('search.LONGITUDE')" />
        <x-input :label="__('fieldterms_occurrenceterms.COORDINATE_UNCERTAINITY_IN_METERS')" />
        <div class="mt-5 flex w-fit items-center gap-2">
            <a href="#" onclick="openWindow('{{ url('/tools/map/pointaid') }}')">
                <x-icons.map />
            </a>
            <a class="h-5 w-5" onclick="openWindow('{{ legacy_url('collections/georef/geolocate.php') }}')">
                {{-- todo pass in geolocate fields --}}
                <img class="h-5 w-5" src="{{ legacy_url('/images/geolocate.png') }}" />
            </a>
            <x-button>C</x-button>
            <x-button>F</x-button>
        </div>

        <x-input :label="__('fieldterms_occurrenceterms.GEODETIC_DATUM')" />
        <div class="mt-5"><<</div>
        <x-input class="min-w-45" :label="__('editor_occurrencetabledisplay.VERB_COORDINATES')" />
    </div>

    <div class="flex items-center gap-2">
        {{-- Todo form label--}}
        <div class="min-w-37">
            <label id="elev-label">{{ __('fieldterms_occurrenceterms.ELEVATION_IN_METERS') }}</label>
            <div class="flex items-center gap-2">
                <x-input aria-labeledBy="elev-label" />
                -
                <x-input aria-labeledBy="elev-label" />
            </div>
        </div>
        <div class="mt-5"><<</div>
        <x-input :label="__('includes_queryform.VERBATIM_ELE')" />

        <div class="min-w-33">
            <label id="depth-label">{{ __('fieldterms_occurrenceterms.DEPTH_IN_METERS') }}</label>
            <div class="flex items-center gap-2">
                <x-input aria-labeledBy="depth-label" />
                -
                <x-input aria-labeledBy="depth-label" />
            </div>
        </div>
        <div class="mt-5"><<</div>
        <x-input :label="__('individual.VERBATIM_DEPTH')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :label="__('fieldterms_occurrenceterms.GEOREFERENCED_BY')" />
        <x-input :label="__('fieldterms_occurrenceterms.GEOREFERENCE_SOURCES')" />
        <x-input :label="__('fieldterms_occurrenceterms.GEOREFERENCE_REMARKS')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :label="__('fieldterms_occurrenceterms.GEOREFERENCE_PROTOCOL')" />
        <x-input :label="__('fieldterms_occurrenceterms.GEOREFERENCE_VERIFICATION_STATUS')" />
    </div>

    <x-input area :label="__('fieldterms_occurrenceterms.FOOTPRINT_POLYGON')" />
</x-fieldset>
