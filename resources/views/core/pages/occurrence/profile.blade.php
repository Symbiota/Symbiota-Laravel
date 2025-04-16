{{-- TODO (Logan) add options to have layout without header, footer --}}
@props(['occurrence', 'images' => [], 'audio' => [], 'collection_contacts' => []])
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
function format_notes($occurrence) {
$arr = [];
if($occurrence->occurrenceRemarks) {
array_push($arr, $occurrence->occurrenceRemarks);
}

if($occurrence->establishmentMeans) {
array_push($arr, $occurrence->establishmentMeans);
}

if($occurrence->cultivationStatus) {
array_push($arr, 'Cultivated or Captive');
}

return $arr;
}

function format_range($min, $max, $units = null) {
$range_str = null;
if($min && $max) {
$range_str = $min . ' - ' . $max;
} else if($min) {
$range_str = $min;
} else if($max) {
$range_str = $max;
}

if($range_str && $units) {
$range_str .= ' ' . $units;
}

return $range_str;
}

function format_latlong_err($occurrence) {
$arr = [
$occurrence->decimalLatitude,
$occurrence->decimalLongitude
];

if($occurrence->coordinateUncertaintyInMeters) {

$arr[] = '+-' . $occurrence->coordinateUncertaintyInMeters . 'm.';
}
if($occurrence->geodeticDatum) {
$arr[] = $occurrence->geodeticDatum;
}

return implode(' ', $arr);
}

//echo '<pre>' . var_export($occurrence, true) . '</pre>';
$attributes = [
//'On Loan To' => $occurrence->loan,
//'Related Occurreces' => $occurrence->relation,
'Catalog #' => $occurrence->catalogNumber,
'Occurrence ID' => $occurrence->occurrenceID? $occurrence->occurrenceID: $occurrence->recordID,
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
'Date' => format_range($occurrence->eventDate, $occurrence->eventDate2),
'Verbatim Date' => $occurrence->verbatimEventDate,
'Additional Collectors' => $occurrence->associatedCollectors,
'Locality' => getLocalityStr($occurrence),
'Latiude/Longitude' => format_latlong_err($occurrence),
'Verbatim Coordinates' => $occurrence->verbatimCoordinates,
'Location Remarks' => $occurrence->locationRemarks,
'Georeference Remarks' => $occurrence->georeferenceRemarks,
'Elevation' => format_range($occurrence->minimumElevationInMeters, $occurrence->maximumElevationInMeters, 'Meters'),
'Verbatim Elevation' => $occurrence->verbatimElevation,
'Depth' => format_range($occurrence->minimumDepthInMeters, $occurrence->maximumDepthInMeters, 'Meters'),
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
'Notes' => format_notes($occurrence),
'Disposition' => $occurrence->disposition,
'Paleontology Terms' => null,
'Exsiccati series' => null,
'Material Samples' => null,
//'Collector' => $occurrence->,
//'Number' => $occurrence->,
//'Date' => $occurrence->,
//'Verbatim Date' => $occurrence->,
//'Verbatim Locality' => $occurrence->,
//'Latiude/Longitude' => $occurrence->,
//'Creative Commons' => $occurrence->,
//'Record ID' => $occurrence->,
];

// NOTES
if($occurrence->occurrenceRemarks) {
array_push($attributes['Notes'], $occurrence->occurrenceRemarks);
}

if($occurrence->establishmentMeans) {
array_push($attributes['Notes'], $occurrence->establishmentMeans);
}

if($occurrence->cultivationStatus) {
array_push($attributes['Notes'], 'Cultivated or Captive');
}

@endphp
<x-layout>
    {{-- JS for Facebook and Twitter --}}
    <div id="fb-root"></div>
    <script type="text/javascript">
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
        @if($occurrence->icon)
        <img class="w-16" src="{{ $occurrence->icon }}">
        @endif
        <div class="text-2xl font-bold">
            {{ $occurrence->collectionName }} ({{ $occurrence->institutionCode }})
        </div>

        <div class="text-2xl font-bold">
            @can('COLL_EDIT', $occurrence->collid)
            <x-nav-link hx-boost="true" href="{{url()->current() . '/edit'}}">
                <x-icons.edit></x-icons.edit>
            </x-nav-link>
            @endcan
        </div>
    </div>

    <x-tabs :tabs="['Details', 'Map', 'Commments', 'Linked Resources', 'Edit History']" :active="0">
        {{-- Occurrence Details --}}
        <div class="relative flex flex-col gap-4">
            <div class="absolute right-3 top-0 h-fit">
                <div class="flex items-center gap-2">
                    <div class="fb-share-button p-0 m-0" data-href="{{ url('') }}" data-size="large"
                        data-layout="button_count">
                    </div>
                    <a class="twitter-share-button" data-size="large" href="https://twitter.com/share"
                        data-url="{{ url('') }}">
                    </a>
                </div>
            </div>

            <div>
                @foreach ($attributes as $title => $value)
                @if($value)
                <div><span class="font-bold">{{$title}}:</span> {{$value}}</div>
                @endif
                @endforeach
            </div>

            @if(count($images))
            <div>
                <div class="font-bold text-lg">Images</div>
                <hr />
            </div>
            <div class="w-fit">
                @foreach ($images as $item)
                <x-media.image :image="$item">
                    <div class="flex flex-col gap-2">
                        @if($item->thumbnailUrl)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->thumbnailUrl }}">Low Resolution</x-link>
                        @endif

                        @if($item->url && $item->originalUrl != $item->url)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->url }}">Normal Resolution</x-link>
                        @endif

                        @if($item->originalUrl)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->originalUrl }}">High Resolution</x-link>
                        @endif

                        @if($item->sourceUrl)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->sourceUrl}}">Source</x-link>
                        @endif
                    </div>
                </x-media>
                @endforeach
            </div>
            @endif

            @if(count($audio))
            <div>
                <div class="font-bold text-lg">Audio</div>
                <hr />
            </div>
            <div class="w-fit">
                @foreach ($audio as $item)
                {{-- TODO (Logan) audio player --}}
                @endforeach
            </div>
            @endif

            <div>
                <span>
                For additional information about his specimen, please contact:
                </span>
                <span>
                @foreach ($collection_contacts as $contact)
                    @if($contact->firstName && $contact->firstName)
                        {{ $contact->firstName . ' ' .  $contact->lastName}}
                    @elseif($contact->firstName)
                        {{ $contact->firstName }}
                    @elseif($contact->lastName)
                        {{ $contact->lastName }}
                    @endif

                    @if($contact->role)
                        ({{ $contact->role }})
                    @endif

                    @if($contact->email)
                        <x-link href="mailto:{{ $contact->email }}">
                            {{ $contact->email }}
                        </x-link>
                    @endif
                @endforeach
                </span>
            </div>
        </div>

        {{-- Map (Only render if lat long data present)--}}
        <div>
            <div id="occurrence-map-data" data-lat="{{ $occurrence->decimalLatitude }}" data-lng="{{ $occurrence->decimalLongitude }}" data-error="{{ $occurrence->coordinateUncertaintyInMeters}}"></div>
            <script>
                document.addEventListener('mapIntialized', function (e) {
                    let map = window.maps['map'];
                    const map_data_elem = document.getElementById('occurrence-map-data');
                    let lat, lng, error;

                    try {
                        lat = parseFloat(map_data_elem.getAttribute('data-lat'));
                        lng = parseFloat(map_data_elem.getAttribute('data-lng'));
                        error = parseFloat(map_data_elem.getAttribute('data-error'));
                    } catch(error) {
                        console.error('Failed to load occurrence map data');
                    }

                    if(lat <= 90 && lat >= -90 && lng <= 180 && lng >= -180) {
                        map.setView([lat,lng], 8)
                        L.marker([lat, lng]).addTo(map);
                        if(error > 0) {
                            L.circle([lat, lng]).addTo(map);
                        }
                    }
                })
            </script>
            <x-map />
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
