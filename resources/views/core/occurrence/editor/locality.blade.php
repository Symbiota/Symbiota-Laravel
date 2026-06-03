@props(['occurrence'])
<x-fieldset :legend="__('imagelib_imgdetails.LOCALITY')">
    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->country" :label="__('collections_list.COUNTRY')" />
        <x-input :value="$occurrence->stateProvince" :label="__('fieldterms_occurrenceterms.STATEPROVINCE')" />
        <x-input :value="$occurrence->county" :label="__('collections_list.COUNTY')" />
        <x-input :value="$occurrence->municipality" :label="__('includes_queryform.MUNICIPALITY')" />
        <x-input :value="$occurrence->locationID" :label="__('includes_queryform.LOCATION_ID')" />
    </div>
    <x-input :value="$occurrence->locality" rows="1" area :label="__('imagelib_imgdetails.LOCALITY')" />
    {{-- todo toggle --}}
    <x-input :value="$occurrence->locationRemarks" :label="__('includes_queryform.LOC_REMARKS')" />

    <div class="flex items-center gap-2">
        {{-- TODO (Logan) todo add inline when pr passes --}}
        <x-select
            name="locality_security"
            :defaultValue="$occurrence->locationSecurity ?? 0"
            :label="__('taxonomy_taxoneditor.LOC_SECURITY')"
            :items="[
            [
                'title' => 'Security Applied',
                'value' => 1,
                'disabled' => false
            ],
            [
                'title' => 'Full Security applied',
                'value' => 1,
                'disabled' => false
            ],
            [
                'title' => 'Security Not Applied',
                'value' => 0,
                'disabled' => false
            ],
        ]"
        />
        <x-checkbox :label="__('editor_occurrenceeditor.DEACTIVATE_LOOKUP')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->decimalLatitude" class="min-w-22" :label="__('search.LATITUDE')" />
        <x-input :value="$occurrence->decmialLongitude" class="min-w-22" :label="__('search.LONGITUDE')" />
        <x-input :value="$occurrence->coordinateUncertaintyInMeters" :label="__('fieldterms_occurrenceterms.COORDINATE_UNCERTAINITY_IN_METERS')" />
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

        <x-input :value="$occurrence->geodeticDatum" :label="__('fieldterms_occurrenceterms.GEODETIC_DATUM')" />
        <div class="mt-5"><<</div>
        <x-input :value="$occurrence->verbatimCoordinates" class="min-w-45" :label="__('editor_occurrencetabledisplay.VERB_COORDINATES')" />
    </div>

    <div class="flex items-center gap-2">
        {{-- Todo form label--}}
        <div class="min-w-37">
            <label id="elev-label">{{ __('fieldterms_occurrenceterms.ELEVATION_IN_METERS') }}</label>
            <div class="flex items-center gap-2">
                <x-input :value="$occurrence->minimumElevationInMeters" aria-labeledBy="elev-label" />
                -
                <x-input :value="$occurrence->maximumElevationInMeters" aria-labeledBy="elev-label" />
            </div>
        </div>
        <div class="mt-5"><<</div>
        <x-input :value="$occurrence->verbatimElevation" :label="__('includes_queryform.VERBATIM_ELE')" />

        <div class="min-w-33">
            <label id="depth-label">{{ __('fieldterms_occurrenceterms.DEPTH_IN_METERS') }}</label>
            <div class="flex items-center gap-2">
                <x-input :value="$occurrence->minimumDepthInMeters" aria-labeledBy="depth-label" />
                -
                <x-input :value="$occurrence->maximumElevationInMeters" aria-labeledBy="depth-label" />
            </div>
        </div>
        <div class="mt-5"><<</div>
        <x-input :value="$occurrence->verbatimDepth" :label="__('individual.VERBATIM_DEPTH')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->georeferencedBy" :label="__('fieldterms_occurrenceterms.GEOREFERENCED_BY')" />
        <x-input :value="$occurrence->georeferenceSources" :label="__('fieldterms_occurrenceterms.GEOREFERENCE_SOURCES')" />
        <x-input :value="$occurrence->georeferenceRemarks" :label="__('fieldterms_occurrenceterms.GEOREFERENCE_REMARKS')" />
    </div>

    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->georeferenceProtocol" :label="__('fieldterms_occurrenceterms.GEOREFERENCE_PROTOCOL')" />
        <x-input :value="$occurrence->georeferenceVerificationStatus" :label="__('fieldterms_occurrenceterms.GEOREFERENCE_VERIFICATION_STATUS')" />
    </div>

    {{-- TODO (Logan) Is this deprecated? --}}
    <x-input area :value="$occurrence->footprintwkt" :label="__('fieldterms_occurrenceterms.FOOTPRINT_POLYGON')" />
</x-fieldset>
