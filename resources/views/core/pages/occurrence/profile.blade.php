{{-- TODO (Logan) add options to have layout without header, footer --}}
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

if($occur->localitySecurity == 1) {
// notice Locality details protected
// Locality details protected
$locality_attributes['protection typically due to rare or threatened status'] = $occurrence->localitySecurityReason;
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
<x-layout>
    {{-- JS for Facebook and Twitter --}}
    <script>
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        window.twttr = (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {};
            if (d.getElementById(id)) return; js = d.createElement(s);
            js.id = id; js.src = "https://platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs); t._e = [];
            t.ready = function (f) {t._e.push(f);};
            return t;
        }(document, "script", "twitter-wjs"));
    </script>

    <div class="flex items-center gap-4 mb-4">
        <img class="w-16" src="https://cch2.org/portal/content/collicon/blmar.jpg">
        <div class="text-2xl font-bold">
            BLMAR - BLM Arcata Field Office Herbarium (BLMAR)
        </div>
    </div>

    <x-tabs :tabs="['Details', 'Map', 'Commments', 'Linked Resources', 'Edit History']" :active="0">
        {{-- Occurrence Details --}}
        <div class="relative">
            <div class="absolute right-3 top-0 flex gap-2">
                <div
                    class="fb-share-button"
                    data-href="{{ url('') }}"
                    data-layout="button_count">
                </div>
                <a
                    class="twitter-share-button"
                    href="https://twitter.com/share"
                    data-url="{{ url('') }}">
                </a>
            </div>

            @foreach ($attributes as $title => $value)
                @if($value)
                <div>{{$title}}: {{$value}}</div>
                @endif
            @endforeach

            <div>For additional information about his specimen, please contact: [Content]</div>

            <div>Do you see an error? If so, errors can be fixed using the [Occurrence Editor link]</div>
        </div>

        {{-- Map (Only render if lat long data present)--}}
        <div>
            TODO laravel leaflet
        </div>

        {{-- Comments --}}
        <div class="grid grid-cols-1 gap-2">
            <div class="text-lg font-bold">No Comments have been submitted</div>
            <form class="grid grid-cols-1 gap-2">
                <x-input label="New Comment" id="comment-input" name="comment" />
                <x-button class="w-fit">Submit Comment</x-button>
            </form>
            <p>Messages over 500 words long may be automatically truncated. All comments are moderated</p>
        </div>

        {{-- Linked Resources --}}
        <div>
            <fieldset class="relative border border-base-300 p-4">
                <legend>Species Checklist Relationship</legend>
                <p>This Occurrence has not been designated as a voucher for a species</p>
                <i class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
            </fieldset>
            <fieldset class="relative border border-base-300 p-4">
                <legend>Dataset Linkages</legend>
                <p>Occurrence is not linked to any datasets</p>
                <i class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
            </fieldset>
        </div>

        {{-- Edit History --}}
        <div>
            <div>
                Entered By: [Content]
            </div>
            <div>
                Date Entered: [Content]
            </div>
            <div>
                Date Modified: [Content]
            </div>
            <div>
                Source Date Modified: [Content]
            </div>
            <div class="my-4">
                Record has not been edited since being entered [Empty Case]
            </div>
            <div>
                Note: Edits are only viewable by collection administrators and editors [Empty Case]
            </div>
        </div>

    </x-tabs>
</x-layout>
