@props(['occurrence'])
@php
function getLocalityStr($occur) {
/*
if($occur->localSecure) {
}
*/

$localityArr = [
$occur->country,
$occur->stateProvince,
$occur->county,
$occur->municipality,
];

$locality_attributes = ['Locality' => implode(', ', $localityArr)];
return implode(', ', $localityArr);

if($occur->recordSecurity == 1) {
// notice Locality details protected
// Locality details protected
$locality_attributes['protection typically due to rare or threatened status'] = $occurrence->securityReason;
//Current user has been granted access if $occur->localSecure
}

return $locality_attributes;
}
//echo '<pre>' . var_export($occurrence, true) . '</pre>';
$attributes = [
//'On Loan To' => $occurrence->loan,
//'Related Occurreces' => $occurrence->relation,
'Catalog #' => $occurrence->catalogNumber,
//'Occurrence ID' => $occurrence->occurrenceid,
'Secondary Catalog #' => $occurrence->otherCatalogNumbers,
'Taxon' => $occurrence->sciname,
'Identification Qualifier' => $occurrence->identificationQualifier,
'Family' => $occurrence->family,
'Determiner' => $occurrence->identifiedBy . ($occurrence->dateIdentified ? '(' . $occurrence->dateIdentified .')': ''),
'Taxon Remarks' => $occurrence->taxonRemarks,

'ID References' => $occurrence->identificationReferences,
'ID Remarks' => $occurrence->identificationRemarks,
//'Determinations' => $occurrence->dets,
'Type Status' => $occurrence->typeStatus,
'Event ID' => $occurrence->eventID,
'Observer' => 'Collector' . $occurrence->recordedBy,
'Number' => $occurrence->recordNumber,
'Date' => implode(' - ', [$occurrence->eventDate, $occurrence->eventDate2, /*$occurrence->eventDateEnd*/]),
'Verbatim Date' => $occurrence->verbatimEventDate,
'Additional Collectors' => $occurrence->associatedCollectors,
'Locality' => getLocalityStr($occurrence),
'Latiude/Longitude' => $occurrence->decimalLatitude .' '. $occurrence->decimalLongitude . ' ' .
$occurrence->coordinateUncertaintyInMeters . $occurrence->geodeticDatum,
'Verbatim Coordinates' => $occurrence->verbatimCoordinates,
'Location Remarks' => $occurrence->locationRemarks,
'Georeference Remarks' => $occurrence->georeferenceRemarks,
'Elevation' => $occurrence->minimumElevationInMeters . ' - ' .$occurrence->maximumElevationInMeters . ' Meters' . ' ' .
$occurrence->verbatimElevation,
'Verbatim Elevation' => $occurrence->verbatimElevation,
'Depth' => $occurrence->minimumDepthInMeters . ' - ' . $occurrence->maximumDepthInMeters . ' Meters',
'Verbatim Depth' => $occurrence->verbatimDepth,
'Information withheld' => $occurrence->informationWithheld,
'Habitat' => $occurrence->habitat,
'substrate' => $occurrence->substrate,
'Associated Taxa' => $occurrence->associatedTaxa,
'Description' => $occurrence->verbatimAttributes,
'Dynamic Properties' => $occurrence->dynamicProperties,
'Reproductive Condition' => $occurrence->reproductiveCondition,
'Life Stage' => $occurrence->lifeStage,
'Sex' => $occurrence->sex,
'Individual Count' => $occurrence->individualCount,
'Sampling Protocol' => $occurrence->samplingProtocol,
'Preparations' => $occurrence->preparations,
'Notes' => implode('; ', [$occurrence->occurrenceRemarks, $occurrence->establishmentMeans,
$occurrence->cultivationStatus? 'Cultivated or Captive' : '']),
'Disposition' => $occurrence->disposition,
'Paleontology Terms' => 'TODO',
'Exsiccati series' => 'TODO',
'Material Samples' => 'TODO',
'Images' => 'TODO',
'Audio' => 'TODO',
//'Collector' => $occurrence->,
//'Number' => $occurrence->,
//'Date' => $occurrence->,
//'Verbatim Date' => $occurrence->,
//'Verbatim Locality' => $occurrence->,
//'Latiude/Longitude' => $occurrence->,
//'Creative Commons' => $occurrence->,
//'Record ID' => $occurrence->,
];
@endphp
<x-layout :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="mb-4 flex items-center gap-2">
        @if($collection->icon)
            <img class="w-10" src="{{ $collection->icon }}" />
        @endif
        <div class="text-2xl font-bold">{{ $collection->collectionName }}</div>

        <div class="text-2xl font-bold">
            <x-nav-link href="{{ url('occurrence/' . $occurrence->occid) }}">
                <x-tooltip :text="__('Public View')">
                    <i class="fas fa-globe"></i>
                </x-tooltip>
            </x-nav-link>
        </div>
    </div>
    <div class="mb-4 flex">
        <x-breadcrumbs
            :items="[
        ['title' => __('header.H_HOME'), 'href' => route('home') ],
        ['title' => __('editor_skeletalsubmit.COL_MNGMT'), 'href' => url('collections/' . $occurrence->collid)],
        ['title' => __('editor_occurrenceeditor.OCCEDITOR')]
        ]"
        />
        <x-button class="ml-auto"> {{ __('editor_occurrenceeditor.NEW_REC') }} </x-button>
    </div>

    <x-tabs
        :tabs="[__('editor_occurrenceeditor.OCC_DATA'), __('individual.DET_HISTORY'), __('header.H_MEDIA'), __('includes_materialsampleinclude.MAT_SAMP'), __('individual.LINKED_RESOURCES'), __('individual.TRAITS'), __('Admin')]"
        :active="5"
    >
        {{-- Occurrence Data --}}
        <div class="flex flex-col gap-4">
            <x-occurrence.editor.collector-info :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.latest-identification :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.locality :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.misc :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.curation :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            {{-- Options should be the same as proccessing Status--}}
            <x-select
                label="Status Auto-Set"
                :items="[
                [
                    'title' => 'No Set Status',
                    'value' => 'No Set Status',
                    'disabled' => false
                ],
            ]"
            />

            <x-fieldset :legend="__('editor_occurrenceeditor.RECORD_CLONING')">
                <x-radio
                    :default_value="2"
                    :options="[
                        ['label' => 'Collection Event Fields', 'value' => 'CollectionEventFields'],
                        ['label' => 'All Fields', 'value' => 'AllFields']
                    ]"
                    label="Carry Over"
                    name="cloning-type"
                />
                <x-checkbox label="Carry over media" />
                {{-- TODO (Logan) Load Options for Cloning --}}
                <x-select
                    :items="[
                    [
                        'title' => 'Undefined',
                        'value' => null,
                        'disabled' => false
                    ],
                ]"
                />
                <x-input label="Number of Records" />
                {{-- TODO (Logan) Prepopulate Catalog numbers work --}}
                <x-button> Create Record(s)</x-button>
            </x-fieldset>

            <x-button>Save Edits</x-button>
        </div>

        <x-occurrence.editor.determination-history {{-- TODO (Logan) Prepopulate Catalog numbers work --}} />

        <x-occurrence.editor.media {{-- TODO (Logan) Prepopulate Catalog numbers work --}} />

        <div>Material Sample WIP</div>

        <x-occurrence.editor.linked-resources />

        <x-occurrence.editor.traits />

        <x-occurrence.editor.admin :occurrence="$occurrence" :occurrence="$occurrence" />
    </x-tabs>
</x-layout>
