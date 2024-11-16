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
    <div class="mb-4 flex">
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('')],
        ['title' => 'Collection Management', 'href' => url( config('portal.name') . '/collections/misc/collprofiles.php?collid=' . $occurrence->collid)],
        ['title' => 'Public Display', 'href' => url('occurrence/' . $occurrence->occid)],
        ['title' => 'Occurrence Editor']
        ]" />
        <x-button class="ml-auto">New Record</x-button>
    </div>

    <div class="flex items-center gap-4 mb-4">
        <img class="w-16" src="https://cch2.org/portal/content/collicon/blmar.jpg">
        <div class="text-2xl font-bold">
            BLMAR - BLM Arcata Field Office Herbarium (BLMAR)
        </div>

        <div class="text-2xl font-bold">
            <x-nav-link href="{{url('occurrence/' . $occurrence->occid)}}">
                <x-tooltip text="Public View">
                    <i class="fas fa-globe"></i>
                </x-tooltip>
            </x-nav-link>
        </div>
    </div>

    <x-tabs :tabs="['Occurrence Data', 'Determination History', 'Images', 'Linked Resources', 'Traits', 'Admin']"
        :active="5">
        {{-- Occurrence Data --}}
        <div class="flex gap-4 flex-col">
            {{-- Collector Info--}}
            <div>
                <h3 class="text-xl font-bold">Collector Info</h3>
                <x-input label="Catalog Number" />
                <div>

                </div>
                <x-input label="Collector/Observer" />
                <x-input label="Number" />
                <x-input label="Date" />
                <x-input label="End Date" />
                <x-input label="Associated Collectors" />
                <x-input label="Verbatim Date" />
                <x-input label="Exiccati Title" />
                <x-input label="Number" />
            </div>

            {{-- Latest Identification --}}
            <div>
                <h3 class="text-xl font-bold">Latest Identification</h3>
                <x-input label="Scientific Name" />
                <x-input label="Author" />
                <x-input label="Identification Qualifier" />
                <x-input label="Family" />
                <x-input label="identified By" />
                <x-input label="Date Identified" />
            </div>

            {{-- Locality --}}
            <div>
                <h3 class="text-xl font-bold">Locality</h3>
                <x-input label="Country" />
                <x-input label="State/Province" />
                <x-input label="Municipality" />
                <x-input label="Location ID" />
                <x-input area label="Locality" />
                <x-input label="Location Remarks" />
                <x-select name="locality_security" label="Locality Security" :items="[
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
                ]" />
                <x-checkbox label="Deactivate Locality Lookup" />
            </div>

            {{-- Misc --}}
            <div>
                <h3 class="text-xl font-bold">Misc</h3>
                <x-input label="Habitat" />
                <x-input label="Substrate" />
                <x-input label="Associated Taxa" />
                <x-input label="Description" />
                <x-input label="Notes" />
                <x-input label="Life Stage" />
                <x-input label="Sex" />
                <x-input label="Individual Count" />
                <x-input label="Sampling Protocal" />
                <x-input label="Preparations" />
                <x-input label="Phenology" />
                <x-input label="Behavior" />
                <x-input label="Vitality" />
                <x-input label="Establishment Means" />
                <x-checkbox label="Cultivated/Captive" />
            </div>

            {{-- Curation --}}
            <div>
                <h3 class="text-xl font-bold">Curation</h3>
                <x-input label="Type Status" />
                <x-input label="Disposition" />
                <x-input label="Occurrence ID" />
                <x-input label="Field Number" />
                <x-input label="Language" />
                <x-input label="Label Project" />
                <x-input label="Dupe Count" />
                <x-input label="Institution Code" />
                <x-input label="Collection Code" />
                <x-input label="Owner Code" />
                <x-input label="Code" />
                <x-select class="w-60" label="Basis of Record" :items="[
                    [
                        'title' => 'Fossil Specimen',
                        'value' => 'FossilSpecimen',
                        'disabled' => false
                    ],
                    [
                        'title' => 'Human Observation',
                        'value' => 'HumanObservation',
                        'disabled' => false
                    ],
                    [
                        'title' => 'Preserved Specimen',
                        'value' => 'PreservedSpecimen',
                        'disabled' => false
                    ],
                    [
                        'title' => 'Living Specimen',
                        'value' => 'LivingSpecimen',
                        'disabled' => false
                    ],
                    [
                        'title' => 'Machine Observation',
                        'value' => 'MachineObservation',
                        'disabled' => false
                    ],
                ]" />
                <x-select label="Processing Status" :items="[
                    [
                        'title' => 'No Set Status',
                        'value' => 'No Set Status',
                        'disabled' => false
                    ],
                ]" />
                <x-input label="Data Generalizations" />
                <div class="flex mt-4">
                    <div class="flex-auto">Key: {{$occurrence->occid}}</div>
                    <div class="flex-auto">Modified: {{ $occurrence->dateLastModified}}</div>
                    <div class="flex-auto">Entered By: {{$occurrence->recordEnteredBy ?? 'not recorded'}} {{
                        $occurrence->dateEntered?'['
                        . $occurrence->dateEntered . ']': ''}}</div>
                </div>
            </div>

            {{-- Options shoudl be the same as proccessing Status--}}
            <x-select label="Status Auto-Set" :items="[
                [
                    'title' => 'No Set Status',
                    'value' => 'No Set Status',
                    'disabled' => false
                ],
            ]" />
            <div>
                <h3 class="text-xl font-bold">Record Cloning</h3>

                <x-radio :default_value="2" :options="[
                        ['label' => 'Collection Event Fields', 'value' => 'CollectionEventFields'],
                        ['label' => 'All Fields', 'value' => 'AllFields']
                    ]" label="Carry Over" name="cloning-type" />
                {{-- TODO (Logan) Load Options for Cloning --}}
                <x-select :items="[
                    [
                        'title' => 'Undefined',
                        'value' => null,
                        'disabled' => false
                    ],
                ]" />
                {{-- TODO (Logan) Prepopulate Catalog numbers work --}}
                <x-button> Create Record(s)</x-button>
            </div>
            <x-button>Save Edits</x-button>
        </div>

        {{-- Determination History --}}
        <div class="flex flex-col gap-4">
            Determination History
            <x-select class="w-40" :items="[
                [
                    'title' => '0 - Unlikely',
                    'value' => 0,
                    'disabled' => false
                ],
                [
                    'title' => '1 - Low',
                    'value' => 1,
                    'disabled' => false
                ],
                [
                    'title' => '2 - Low',
                    'value' => 2,
                    'disabled' => false
                ],
                [
                    'title' => '3 - Low',
                    'value' => 3,
                    'disabled' => false
                ],
                [
                    'title' => '4 - Medium',
                    'value' => 4,
                    'disabled' => false
                ],
                [
                    'title' => '5 - Medium',
                    'value' => 5,
                    'disabled' => false
                ],
                [
                    'title' => '6 - Medium',
                    'value' => 6,
                    'disabled' => false
                ],
                [
                    'title' => '7 - High',
                    'value' => 7,
                    'disabled' => false
                ],
                [
                    'title' => '8 - High',
                    'value' => 8,
                    'disabled' => false
                ],
                [
                    'title' => '9 - High',
                    'value' => 9,
                    'disabled' => false
                ],
                [
                    'title' => '10 - Absolute',
                    'value' => 10,
                    'disabled' => false
                ],
            ]" />
            <x-input label="notes" />
            <x-button>Submit Verification Edits</x-button>

            @php
            //Currently Just Test Data
            $determinations = [
            ['sciname' => 'Pinus aristata', 'author' => 'Engelm.', 'date'=> 's.d.', 'determiner' =>
            'unknown','isCurrent' => true]
            ];
            @endphp
            <div class="">
                <div class="flex items-center">
                    <div class="text-lg font-bold">Determination History</div>
                    <div class="ml-auto">
                        <i class="fas fa-plus"></i>
                    </div>
                </div>
                <hr class="mb-4 mt-1 h-1 border-none bg-base-300" />


                @foreach ($determinations as $determination)
                <div class="border p-2">
                    <div>
                        <span>{{$determination['sciname']}} {{$determination['author']}}</span>
                        @if($determination['isCurrent'])
                        <span class="text-error uppercase">Current Determination</span>
                        @endif
                        <x-icons.edit />
                    </div>
                    <div class="flex gap-4">
                        <div>
                            <span>Determiner:{{$determination['determiner']}}<span>
                        </div>
                        <div>
                            <span>Date: {{$determination['date']}}<span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Images --}}
        <div class="flex flex-col gap-4">
            <x-button>Add Media</x-button>
            @php
            $media = [
            [
            'caption' => 'Pine Specimen',
            'creator' => 'Harry',
            'notes' => 'Pine collected for research',
            'tags' => 'plant, image',
            'copywright' => 'sample-copywright',
            'source_url' => '',
            'url' => '',
            'thumbnail_url' => 'https://s3.msi.umn.edu/mbaenrms3fs/images/MIN_JFBM_PLANTS/01003/1003938_tn.jpg',
            'original_url' => '',
            'sort' => 0
            ],
            ];
            @endphp

            @foreach ($media as $m)
            <div class="flex flex-row gap-4 border p-4">
                <div>
                    <a href="{{ $m['thumbnail_url']}}">
                        <img src="{{ $m['thumbnail_url']}}">
                    </a>
                </div>
                <div>
                    <div class="flex justify-end text-xl">
                        {{-- TODO (Logan) get edit form outline --}}
                        <x-icons.edit />
                    </div>
                    <div>Caption: {{ $m['caption'] }}</div>
                    <div>Creator: {{ $m['creator'] }}</div>
                    <div>Notes: {{ $m['notes'] }}</div>
                    <div>Tags: {{ $m['tags'] }}</div>
                    <div>Source Webpage: {{ $m['source_url'] }}</div>
                    <div>Web URL: {{ $m['url'] }}</div>
                    <div>Large URL: {{ $m['original_url'] }}</div>
                    <div>Thumbnail URL: {{ $m['thumbnail_url'] }}</div>
                    <div>Sort: {{ $m['sort'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Linked Resources --}}
        <div class="flex flex-col gap-4">
            <div class="p-4 border">
                <div class="flex">
                    <h3 class="text-lg font-bold">Asssociated Occurrences</h3>
                    <i class="ml-auto fas fa-plus"></i>
                </div>
                <div>
                    No associations have been established
                </div>
            </div>

            <div class="p-4 border flex flex-col gap-4">
                <div>
                    <div class="flex">
                        <h3 class="text-lg font-bold">Checklist Voucher Linkages</h3>
                    </div>
                    <div>
                        No voucher linkages have been established
                    </div>
                </div>
                <x-select :items="[
                    ['title' => 'Select a Checklist', 'value'=> null, 'disabled' => false ]
                ]" />
                <x-button>Link to Checklist as Voucher</x-button>
            </div>

            <div class="p-4 border flex flex-col gap-4">
                <div>
                    <div class="flex">
                        <h3 class="text-lg font-bold">Genetic Resources</h3>
                    </div>
                    <div>
                        No genetic linkages have been established
                    </div>
                </div>

                <div class="font-bold">Add New Resource</div>
                <x-input label="Name" />
                <x-input label="Identifier" />
                <x-input label="Locus" />
                <x-input label="URL" />
                <x-input label="Notes" />
                <x-button>Add New Genetic Resource</x-button>
            </div>
        </div>

        {{-- Traits --}}
        <div class="flex flex-col gap-4">
            <div class="p-4 border flex flex-col gap-4">
                <div>
                    <div class="flex">
                        <h3 class="text-lg font-bold">Anglosperm Phenolgical Traits</h3>
                    </div>
                </div>

                {{-- TODO (Logan) Update this to have nested attribute tree--}}
                <x-radio name="pheno-traits" :options="[
                    ['label' => 'Reproductive', 'value' => 'reproductive'],
                    ['label' => 'Sterile', 'value' => 'sterile'],
                    ['label' => 'Not Scorable', 'value' => 'not-scorable']
                ]" />

                <div class="font-bold">Add New Resource</div>
                <x-input label="Notes" />
                <x-select class="w-60" label="Source" :items="[
                    ['title' => 'Machine Learning', 'value' => 'machine_learning', 'disabled' => false],
                    ['title' => 'Physical Specimen', 'value' => 'physical_specimen', 'disabled' => false],
                    ['title' => 'Viewing Image', 'value' => 'viewing_image', 'disabled' => false],
                    ['title' => 'Verbatim Text Mining', 'value' => 'verbatim_text_mining', 'disabled' => false],
                ]" />

                <x-select class="w-60" label="Status" :items="[
                    ['title' => 'Not Reviewed', 'value' => 'not_reviewed', 'disabled' => false],
                    ['title' => 'Expert Needed', 'value' => 'expert_needed', 'disabled' => false],
                    ['title' => 'Reviewed', 'value' => 'reviewed', 'disabled' => false],
                ]" />
                <x-button>Save Edits</x-button>
                <x-button variant="error">Delete Coding</x-button>
            </div>
        </div>

        {{-- Admin --}}
        <div class="flex flex-col gap-4">
            <div class="p-4 border flex flex-col gap-4">
                <h3 class="text-lg font-bold">History of Interal Edits</h3>
                @php
                // For Putting of Skeleton Only
                $edits = [
                    [
                        'editor' => 'Me Wa',
                        'date' => '2024-11-15 22:38',
                        'applied_status' => 'Applied',
                        'fields_edited' => [
                            [
                            'name' => 'omoccuridentifiers',
                            'old_value' => 'old value 1',
                            'new_value' => 'new value 1',
                            ],
                            [
                            'name' => 'omoccuridentifiers',
                            'old_value' => 'old value 2',
                            'new_value' => 'new value 2',
                            ],
                        ]
                    ],
                    [
                        'editor' => 'Me Wa',
                        'date' => '2024-11-15 22:38',
                        'applied_status' => 'Applied',
                        'fields_edited' => [
                            [
                            'name' => 'omoccuridentifiers',
                            'old_value' => 'old value 1',
                            'new_value' => 'new value 1',
                            ],
                            [
                            'name' => 'omoccuridentifiers',
                            'old_value' => 'old value 2',
                            'new_value' => 'new value 2',
                            ],
                        ]
                    ]
                ];
                @endphp

                @foreach ($edits as $edit)
                <div>
                    <div class="flex gap-4">
                        <div>
                            Editor: {{$edit['editor']}}
                        </div>
                        <div>
                            Date: {{$edit['date']}}
                        </div>
                    </div>
                    <div>
                        Applied Status: {{$edit['applied_status']}}
                    </div>
                    <div>
                        @foreach ($edit['fields_edited'] as $field)
                        <div class="p-2">
                            <div>Field: {{$field['name']}}</div>
                            <div>Old Value: {{$field['old_value']}}</div>
                            <div>New Value: {{$field['new_value']}}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <hr class="mb-4 mt-1 h-1 border-none bg-base-300" />
                @endforeach
            </div>

            {{-- Transfer Record --}}
            <div class="p-4 border flex flex-col gap-4">
                <h3 class="text-lg font-bold">Transfer Specimen</h3>
                <x-select label="Target Collection" :items="[
                    ['title' => 'Select Collection', 'value' => null, 'disabled' => false]
                ]"/>
                <x-button>Transfer Record</x-button>
            </div>

            {{-- Delete Occurrence Record --}}
            <div class="p-4 border flex flex-col gap-4">
                <h3 class="text-lg font-bold">Delete Occurrence Record</h3>
                <p>Record first needs to be evaluated before it can be deleted from the system. The evaluation ensures that the deletion of this record will not interfere with the integrity of other linked data. Note that all determination and comments for this occurrence will be automatically deleted. Links to images, and checklist vouchers will have to be individually addressed before can be deleted.</p>
                <x-button variant="error">Evaluate Record for Deletion</x-button>
                <div>Image Links:</div>
                <div>Checklist Voucher Links:</div>
            </div>
        </div>
    </x-tabs>
</x-layout>
