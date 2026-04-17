@props([
    'occurrence',
    'collection' => new StdClass(),
    'images' => [],
    'audio' => [],
    'collection_contacts' => [],
    'identifiers' => [],
    'determinations' => [],
    'editHistory' => [],
    'linked_checklists' => [],
    'linked_datasets' => [],
    'user_checklists' => [],
    'user_datasets' => [],
    'paleo' => null,
    'exsiccati' => null,
    'material_sample' => null,
])

@php

function getLocalityStr($occur) {
    $localityArr = [];

    if($occur->country) $localityArr[] = $occur->country;
    if($occur->stateProvince) $localityArr[] = $occur->stateProvince;
    if($occur->county) $localityArr[] = $occur->county;
    if($occur->municipality) $localityArr[] = $occur->municipality;

    return implode(', ', $localityArr);
    if($occur->recordSecurity == 1) {
        // notice Locality details protected
        // Locality details protected
        $locality_attributes['protection typically due to rare or threatened status'] = $occurrence->securityReason;
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

$user_checklist_options = [];

foreach($user_checklists as $checklist) {
    if(count($linked_checklists) <= 0 || $linked_checklists->search(fn ($v) => $v->clid == $checklist->clid) > 0) {
        $user_checklist_options[] = ['title' => $checklist->name, 'value' => $checklist->clid, 'disabled' => false ];
    }
}

$user_dataset_options = [];
foreach($user_datasets as $datasets) {
    if(count($linked_datasets) <= 0 || $linked_datasets->search(fn ($v) => $v->datasetID == $datasets->datasetID) > 0) {
        $user_dataset_options[] = ['title' => $datasets->name, 'value' => $datasets->datasetID, 'disabled' => false ];
    }
}
@endphp
<x-margin-layout :hasHeader="false" :hasFooter="false" :hasNavbar="false">
    <div class="flex items-center gap-4 mb-4">
        @if($collection->icon)
        <img class="w-16" src="{{ $collection->icon }}">
        @endif
        <x-link href="{{ url('collections/' . $occurrence->collid) }}" class="text-2xl font-bold text-base-content hover:text-base-content/50">
            {{ $collection->collectionName }} ({{ $collection->institutionCode }})
        </x-link>

        <div class="text-2xl font-bold">
            @can('COLL_EDIT', $occurrence->collid)
            <x-nav-link hx-boost="true" href="{{url()->current() . '/edit'}}">
                <x-icons.edit></x-icons.edit>
            </x-nav-link>
            @endcan
        </div>
    </div>

    @php
        $tabs = ['Details'];
        if($occurrence->decimalLatitude && $occurrence->decimalLongitude) {
            $tabs[] = 'Map';
        }

        $comment_tab_name = 'Comments';

        if($count = count($comments)) {
            $comment_tab_name = $count . ' ' . $comment_tab_name;
        }

        $tabs = [
            ...$tabs,
            $comment_tab_name,
            'Linked Resources'
        ];

        if($editHistory) {
            $tabs[] = 'Edit History';
        }
    @endphp
    <x-tabs id="occurrence-tab" :tabs="$tabs" :active="0">
        {{-- Occurrence Details --}}
        <div class="relative flex flex-col gap-4">
            {{-- OCCURRENCE INFORMATION START--}}
            <div>
                <x-text-label :label="__('collections_list.CATALOG_NUMBER') . ' #'">{{ $occurrence->catalogNumber }}</x-text-label>
                <x-text-label :label="__('individual.OCCURRENCE_ID')">{{ $occurrence->occurrenceID}}</x-text-label>

                @if(is_array($identifiers) && count($identifiers))
                    @foreach ($identifiers as $identifier)
                        <x-text-label :label="$identifier->identifierName? $identifier->identifierName: __('OTHER_CATALOG_NUMBERS')">{{ $identifier->identifierValue }}</x-text-label>
                    @endforeach
                @elseif($occurrence->otherCatalogNumbers)
                    <x-text-label :label="__('individual.OTHER_CATALOG_NUMBERS')">{{ $occurrence->otherCatalogNumbers }}</x-text-label>
                @endif

                <x-text-label :label="__('ident_key.TAXON')">
                    @if($occurrence->sciname)
                        <i class="font-italic">{{ $occurrence->sciname }}</i>
                        @if($occurrence->scientificNameAuthorship)
                            ({{$occurrence->scientificNameAuthorship}})
                        @endif
                    @endif
                </x-text-label>

                <x-text-label-list :labels="[
                    __('individual.ID_QUALIFIER') => $occurrence->identificationQualifier,
                    __('taxa.FAMILY') => $occurrence->family,
                    __('individual.DETERMINER') => $occurrence->identifiedBy,
                    __('individual.DATE_DET') => $occurrence->dateIdentified,
                    __('individual.TAXON_REMARKS') => $occurrence->taxonRemarks,
                    __('individual.ID_REFERENCES') => $occurrence->identificationReferences,
                    __('individual.ID_REMARKS') => $occurrence->identificationRemarks,
                    __('individual.TYPE_STATUS') => $occurrence->typeStatus,
                    __('individual.EVENT_ID') => $occurrence->eventID,
                    __('individual.OBSERVER') => $occurrence->recordedBy,
                    //Todo fix conflicting lang tag
                    __('collections_list.NUMBER') => $occurrence->recordNumber,
                    __('individual.DATE') => format_range($occurrence->eventDate, $occurrence->eventDate2),
                    __('individual.VERBATIM_DATE') => $occurrence->verbatimEventDate,
                    __('individual.ADDITIONAL_COLLECTORS') => $occurrence->associatedCollectors,
                ]"/>

                <x-text-label :label="__('imagelib_imgdetails.LOCALITY')">{{ getLocalityStr($occurrence) }}</x-text-label>
                @if($occurrence->recordSecurity)
                <div class="font-bold bg-warning p-1 rounded-md text-warning-content">
                    <div>
                    {{ __('individual.DETAILS_PROTECTED') }}: {{ __('individual.PROTECTED_REASON') }}
                    </div>
                    <div>
                    @can('SECURED_READER', $collection->collID)
                    {{ __('individual.ACCESS_GRANTED') }}
                    @endcan
                    </div>
                </div>
                @endif

                <x-text-label-list :labels="[
                    __('individual.LAT_LNG') => format_latlong_err($occurrence),
                    __('individual.VERBATIM_COORDINATES') => $occurrence->verbatimCoordinates,
                    __('individual.LOCATION_REMARKS') => $occurrence->locationRemarks,
                    __('individual.GEOREF_REMARKS') => $occurrence->georeferenceRemarks,
                    __('collections_list.ELEVATION') => format_range($occurrence->minimumElevationInMeters, $occurrence->maximumElevationInMeters, 'Meters'),
                    __('individual.VERBATIM_ELEVATION') => $occurrence->verbatimElevation,
                    __('individual.DEPTH') => format_range($occurrence->minimumDepthInMeters, $occurrence->maximumDepthInMeters, 'Meters'),
                    __('individual.VERBATIM_DEPTH') => $occurrence->verbatimDepth,
                    __('individual.INFO_WITHHELD') => $occurrence->informationWithheld,
                    __('checklists_checklist.HABITAT') => $occurrence->habitat,
                    __('individual.SUBSTRATE') => $occurrence->substrate,
                    // TODO fix lang conflict
                    __('individual.ASSOCIATED_TAXA') => $occurrence->associatedTaxa,
                    __('taxa.DESCRIPTION') => $occurrence->verbatimAttributes,
                    __('individual.DYNAMIC_PROPERTIES') => $occurrence->dynamicProperties,
                    __('individual.REPRODUCTIVE_CONDITION') => $occurrence->reproductiveCondition,
                    __('individual.LIFE_STAGE') => $occurrence->lifeStage,
                    __('individual.SEX') => $occurrence->sex,
                    __('individual.INDIVIDUAL_COUNT') => $occurrence->individualCount,
                    __('individual.SAMPLE_PROTOCOL') => $occurrence->samplingProtocol,
                    __('individual.PREPARATIONS') => $occurrence->preparations,
                    __('projects.NOTES') => implode(', ', format_notes($occurrence)),
                    __('individual.DISPOSITION') => $occurrence->disposition,
                ]"/>

                {{-- EXSICCATE INFORMATION START --}}
                @if($exsiccati)
                    <x-text-label :label="__('individual.EXCICCATI_SERIES')">
                        <x-link :href="legacy_url('/exsiccati/index.php?omenid=' . $exsiccati->omenid)">{{ $exsiccati->title. ' #' . $exsiccati->exsnumber }}</x-link>
                    </x-text-label>
                </div>
                @endif
                {{-- EXSICCATE INFORMATION END --}}

                {{-- MATERIAL INFORMATION START --}}
                @if($material_sample)
                <div>
                    <div>
                        <div class="font-bold text-lg">
                            {{ __('individual.MATERIAL_SAMPLES') }}
                        </div>
                        <hr />
                    </div>
                    @foreach([
                        'sampleType' => __('material_sample.SAMPLE_TYPE'),
                        'catalogNumber' => __('material_sample.CATALOG_NUMBER'),
                        'guid' => __('material_sample.GUID'),
                        'sampleCondition' => __('material_sample.SAMPLE_CONDITION'),
                        'disposition' => __('material_sample.DISPOSITION'),
                        'preservationType' => __('material_sample.PRESERVATION_TYPE'),
                        'preparationDetails' => __('material_sample.PREPARATION DETAILS'),
                        'preparationDate' => __('material_sample.PREPARATION_DATE'),
                        'preparedBy' => __('material_sample.PREPARED_BY'),
                        'individualCount' => __('material_sample.INDIVIDUAL COUNT'),
                        'sampleSize' => __('material_sample.SAMPLE_SIZE'),
                        'storageLocation' => __('material_sample.STORAGE_LOCATION'),
                        'remarks' => __('material_sample.REMARKS'),
                        'concentration' => __('material_sample.CONCENTRATION'),
                        'concentrationUnit' => __('material_sample.CONCENTRATION_UNIT'),
                        'ratioOfAbsorbance260/230' => __('material_sample.RATIO_OF_ABSORBANCE_260/230'),
                        'ratioOfAbsorbance260/230' => __('material_sample.RATIO_OF_ABSORBANCE_260/280'),
                        'volume' => __('material_sample.VOLUME'),
                        'weight' => __('material_sample.WEIGHT'),
                        'weightMethod' => __('material_sample.WEIGHT_METHOD'),
                        'purification Method' => __('material_sample.PURIFICATION_METHOD'),
                        'quality' => __('material_sample.QUALITY'),
                        'qualityRemarks' => __('material_sample.QUALITY_REMARKS'),
                        'qualityCheckDate' => __('material_sample.QUALITY_CHECK_DATE'),
                        'sampleSize' => __('material_sample.SAMPLE_SIZE'),
                        'sieving' => __('material_sample.SIEVING'),
                        'dnaHybridization' => __('material_sample.DNA_HYBRIDIZATION'),
                        'dnaMeltingPoint' => __('material_sample.DNA_MELTING_POINT'),
                        'estimatedSize' => __('material_sample.ESTIMATED_SIZE'),
                        'poolDnaExtracts' => __('material_sample.POOL_DNA_EXTRACTS'),
                        'sampleDesignation' => __('material_sample.SAMPLE_DESIGNATION')
                    ] as $field => $label)
                        <x-text-label :label="$label">
                            {{ $material_sample[$field] }}
                        </x-text-label>
                    @endforeach
                </div>
                @endif
                {{-- MATERIAL INFORMATION END --}}
            </div>
            {{-- OCCURRENCE INFORMATION END --}}

            {{-- PALEO INFORMATION START --}}
            @if($paleo)
            <div>
                <div>
                    <div class="font-bold text-lg">
                        {{ __('collections_list.GEO_CONTEXT') }}
                    </div>
                    <hr />
                </div>
                <x-text-label :label="__('BASIS_OF_RECORD')">
                    {{ $collection->collType == "Fossil Specimens"? $occurrence->basisofrecord: null }}
                </x-text-label>

                <x-text-label :label="__('individual.CHRONOSTRAT')">
                    {{ format_range($paleo->earlyInterval, $paleo->lateInterval) }}
                    <x-text-label-list class="pl-4" :labels="[
                        __('collections_list.LATE_INT') => $paleo && $paleo->lateInterval && $paleo->lateIntervalHierarchy? $paleo->lateIntervalHierarchy: null,
                        __('collections_list.EARLY_INT') => $paleo && $paleo->earlyInterval && $paleo->earlyIntervalHierarchy? $paleo->earlyIntervalHierarchy: null,
                        __('individual.ABSOLUTE_AGE') => $occurrence->absoluteAge,
                        __('LOCAL_STAGE') => $occurrence->localStage,
                    ]"/>
                </x-text-label>

                <x-text-label :label="__('individual.LITHOSTRAT')">
                    <x-text-label-list class="pl-4" :labels="[
                        __('includes_paleoinclude.LITHOGROUP_LABEL') => $paleo->lithogroup ,
                        __('includes_paleoinclude.FORMATION_LABEL') => $paleo->formation,
                        __('includes_paleoinclude.MEMBER_LABEL') => $paleo->member,
                        __('includes_paleoinclude.BED_LABEL') => $paleo->bed,
                    ]"/>
                </x-text-label>
            </div>
            @endif
            {{-- PALEO INFORMATION END --}}

            @if(count($determinations))
                <div>
                    <div class="font-bold text-lg">
                        {{ __('individual.DET_HISTORY') }}
                    </div>
                    <hr />
                </div>
                <div class="flex flex-col gap-2">
                    @foreach ($determinations as $det)
                        <div>
                            <span class="font-bold font-italic">{{ $det->sciname }}</span>
                            @if($det->scientificNameAuthorship)
                                ({{$det->scientificNameAuthorship}})
                            @endif
                            <x-text-label :label="__('individual.DETERMINER')">
                                {{ $det->identifiedBy }}
                            </x-text-label>
                            <x-text-label :label="__('individual.DATE')">
                                {{ $det->dateIdentified }}
                            </x-text-label>
                        </div>

                        @if(count($determinations) > 0 && !$loop->last)
                        <hr/>
                        @endif
                    @endforeach
                </div>
            @endif

            @if(count($images))
            <div>
                <div class="font-bold text-lg">
                    {{-- todo move to media ligo --}}
                    {{ __('individual.SPECIMEN_IMAGES') }}
                </div>
                <hr />
            </div>
            <div class="w-fit flex flex-wrap gap-2">
            {{-- TODO (Logan) lang for image resolutions --}}
                @foreach ($images as $item)
                <x-media.image :image="$item" :href="$item->url ?? $item->thumbnailUrl ?? $item->originalUrl">
                    <div class="flex flex-col gap-2">
                        @if($item->url && $item->originalUrl != $item->url)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->url }}">
                            {{ __('individual.OPEN_MEDIUM') }}
                        </x-link>
                        @endif

                        @if($item->originalUrl)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->originalUrl }}">
                            {{ __('individual.OPEN_LARGE') }}
                        </x-link>
                        @endif

                        @if($item->sourceUrl)
                        <x-link class="text-base-100" target="_blank" href="{{ $item->sourceUrl}}">
                            {{ __("individual.OPEN_SOURCE") }}
                        </x-link>
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
                    <x-media.audio :item="$item" />
                @endforeach
            </div>
            @endif

            @if($collection->rights)
            <x-text-label :label="__('misc_collmetadata.LICENSE')">
                <x-link :href="$collection->rights">
                    {{ $collection->rights }}
                </x-link>
            </x-text-label>
            @endif

            <div>
                <span>{{ __('individual.ADDITIONAL_INFO') }}</span>
                <span>
                @foreach ($collection_contacts as $contact)
                    @if($contact->firstName && $contact->firstName)
                        {{ $contact->firstName . ' ' .  $contact->lastName}}
                    @elseif($contact->firstName)
                        {{ $contact->firstName }}
                    @elseif($contact->lastName)
                        {{ $contact->lastName }}
                    @endif

                    @if($contact->role ?? false)
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
            <div>
                {{ __('individual.SEE_ERROR') }}
                <x-link :href="url('occurrence/' . $occurrence->occid. '/edit')">{{ __('individual.OCCURRENCE_EDITOR')}}</x-link>
            </div>
        </div>

        {{-- Map (Only render if lat long data present)--}}
        @if($occurrence->decimalLatitude && $occurrence->decimalLongitude)
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

                    // Needed for tiles to render because of dom controlled tab
                    document.getElementById('occurrence-tab').addEventListener('tabChanged', function() {
                        map._onResize();
                    }, { once: true });
                })
            </script>
            <x-map />
        </div>
        @endif

        {{-- Comments --}}
        <div id="comment-tab" class="grid grid-cols-1 gap-2">
            @fragment('comments')
            @if (Auth::check())
            <form hx-post="{{ url('occurrence/' . $occurrence->occid . '/comment') }}" hx-target="#comment-tab" hx-swap="innerHTML" class="grid grid-cols-1 gap-2">
                @csrf
                <x-input area label="New Comment" id="comment-input" name="comment" rows="8"/>
                <x-button type="submit" class="w-fit">Submit Comment</x-button>
                <p>Messages over 500 words long may be automatically truncated. All comments are moderated</p>
            </form>
            @endif

            <x-errors :errors="$comment_errors ?? []"/>

            @if(count($comments))
            <div class="flex flex-col gap-4">
            @foreach ($comments as $comment)
                <div class="p-4 border border-base-300 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-medium">{{ $comment->username}}</span>
                        <span class="text-base-content/50">posted {{ $comment->initialtimestamp }}</span>
                        <span class="flex-grow flex justify-end gap-2">
                            {{-- TODO (Logan) report functionality --}}

                            @if($comment->reviewstatus != 2)
                            <form hx-patch="{{ url('occurrence/' . $occurrence->occid . '/comment/' . $comment->comid . '/report') }}"
                                hx-target="#comment-tab"
                                hx-swap="innerHTML"
                                hx-confirm="Are you sure you wish to report this comment?">
                                @csrf
                                <x-button variant="error">
                                    Report
                                </x-button>
                            </form>
                            @endif

                            @php $user = request()->user(); @endphp
                            @if($user)
                                @if ($user && $user->uid == $comment->uid || Gate::check('COLL_EDIT', [$occurrence->collid]))

                                    @if($comment->reviewstatus == 2)
                                    <form hx-patch="{{ url('occurrence/' . $occurrence->occid . '/comment/' . $comment->comid . '/public') }}"
                                        hx-target="#comment-tab"
                                        hx-swap="innerHTML">
                                        @csrf
                                        <x-button>
                                            Make Public
                                        </x-button>
                                    </form>
                                    @endif

                                    <form hx-delete="{{ url('occurrence/' . $occurrence->occid . '/comment/' . $comment->comid) }}"
                                        hx-target="#comment-tab"
                                        hx-swap="innerHTML"
                                        hx-confirm="Are you sure you wish to delete this comment?">
                                        @csrf
                                        <x-button variant="error">Delete</x-button>
                                    </form>
                                @endif
                            @endif
                        </span>
                    </div>
                    <div>
                        {{ $comment->comment }}
                    </div>
                    @if($comment->reviewstatus == 2)
                        <div class="p-2 bg-error text-error-content rounded-md w-fit">
                            Comment not public due to pending abuse report (viewable to administrators only)
                        </div>
                    @endif
                </div>
            @endforeach
            </div>
            @else
            <div class="text-lg font-bold">No Comments have been submitted</div>
            @endif
            @endfragment
        </div>

        {{-- Linked Resources --}}
        <div class="flex flex-col gap-4">
            @fragment('linked_checklists')
            <div class="relative flex flex-col gap-2" x-data="{ checklist_link_open: false }">
                <div>
                    <span class="font-bold text-xl">
                        Species Checklist Relationship
                    </span>
                    <hr/>
                </div>

                @if(Gate::check('COLL_EDIT', $occurrence->collid) && count($user_checklist_options) > 0)
                <i @click="checklist_link_open = true" class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
                <form hx-put="{{url('occurrence/' . $occurrence->occid . '/link/checklist' )}}" x-show="checklist_link_open" class="flex flex-col gap-4">
                    @csrf
                    <x-select label="Checklist" name="clid" :items="$user_checklist_options" class="w-60"/>
                    <x-input label="Notes" name="notes" />
                    <x-input label="Editor Notes" name="editor_notes" />
                    <input type="hidden" name="voucher_tid" value="{{ $occurrence->tidInterpreted}}"/>
                    <div class="flex gap-2">
                        <x-button>Link Voucher</x-button>
                        <x-button type="button" variant="neutral">Cancel</x-button>
                    </div>
                </form>
                @endif

                <div>
                    @if(count($linked_checklists))
                        <ul>
                        @foreach ($linked_checklists as $checklist)
                            <li><x-link href="{{url('checklists') . $checklist->clid }}">{{ $checklist->name }}</x-link></li>
                        @endforeach
                        </ul>
                    @else
                        <p>This Occurrence has not been designated as a voucher for a species</p>
                    @endif
                </div>
            </div>
            @endfragment

            @fragment('linked_datasets')
            <div class="relative" x-data="{ dataset_link_open: false}">
                <div>
                    <span class="font-bold text-xl">
                        Dataset Linkages
                    </span>
                    <hr/>
                </div>

                @if(!empty($user_dataset_options))

                <i @click="dataset_link_open = true" class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
                <form hx-put="{{url('occurrence/' . $occurrence->occid . '/link/dataset' )}}" x-show="dataset_link_open" class="flex flex-col gap-4">
                    @csrf
                    <x-select label="Dataset" name="datasetID" :items="$user_dataset_options" class="w-60"/>
                    <x-input label="Notes" name="notes" />
                    <input type="hidden" name="voucher_tid" value="{{ $occurrence->tidInterpreted}}"/>
                    <div class="flex gap-2">
                        <x-button>Link to Dataset</x-button>
                        <x-button type="button" variant="neutral">Cancel</x-button>
                    </div>
                </form>
                @endif

                <div>
                    @if(count($linked_datasets))
                        <ul>
                        @foreach ($linked_datasets as $dataset)
                            <li><x-link href="{{ legacy_url('/collections/datasets/public.php')}}?datasetid={{$dataset->datasetID}}">{{ $dataset->name }}</x-link></li>
                        @endforeach
                        </ul>
                    @else
                        <p>Occurrence is not linked to any datasets</p>
                    @endif
                </div>
            </div>
            @endfragment
        </div>

        {{-- Edit History --}}
        @if($editHistory)
        <div>
            <x-text-label label="Entered By">{{ $occurrence->recordEnteredBy ?? 'Not Recorded' }}</x-text-label>
            <x-text-label label="Date Entered">{{ $occurrence->dateEntered }}</x-text-label>
            <x-text-label label="Date Modified">{{ $occurrence->dateLastModified }}</x-text-label>
            <div class="font-bold text-xl mt-4">
                Internal Edits
            </div>
            <hr/>
            @foreach ($editHistory as $editGroup)
                <div class="border-b border-base-300 py-2 flex flex-col gap-2">
                    <div>
                        <x-text-label label="Editor">{{ $editGroup['name'] }}</x-text-label>
                        <x-text-label label="Date">{{ $editGroup['initialTimestamp'] }}</x-text-label>
                        <x-text-label label="Applied Status"> {{ $editGroup['appliedStatus'] ? 'Applied' : 'Not Applied' }}</x-text-label>
                    </div>

                    <div class="ml-4 flex flex-col gap-2">
                        @foreach ($editGroup['edits'] as $edit)
                            {{--
                            <x-text-label label="Field">{{ $edit->fieldName }}</x-text-label>
                            <x-text-label allow_empty="true" label="Old Value">{{ $edit->fieldValueOld }}</x-text-label>
                            <x-text-label label="New Value">{{ $edit->fieldValueNew }}</x-text-label>
                            --}}
                            <div class="flex gap-2">
                                <span class="bg-base-300 px-2 rounded-full">
                                    {{ (!$edit->fieldValueOld? 'Added': 'Updated') }}
                                </span>
                                <x-text-label :label="$edit->fieldName">
                                    @if(!$edit->fieldValueOld)
                                        {{ $edit->fieldValueNew }}
                                    @else
                                        <span>{{ $edit->fieldValueOld }}</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                        <span>{{ $edit->fieldValueNew }}</span>
                                    @endif
                                </x-text-label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="mt-2">
                Note: Edits are only viewable by collection administrators and editors
            </div>
        </div>
        @endif
    </x-tabs>
</x-layout>
