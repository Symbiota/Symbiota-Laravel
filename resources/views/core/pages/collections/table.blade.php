@props(['collection', 'occurrences' => [], 'page' => 0])
@php
    function remapAssoc(array $input, array $renames = [], array $mutations = []) {
        $remapped = [];

        foreach($input as $key => $value) {
            $new_key = $renames[$key] ?? $key;
            $new_value = !empty($mutations[$key]) ?
                $mutations[$key]($value):
                $value;

            $remapped[$new_key] = $new_value;
        }

        return $remapped;
    }

    // TODO (Logan) Rework when occurrence editor gets transfered
    // This is need to interop with old occurrence editor form
    function getOccurrenceEditorUrl($other_params) {
        $editor_renames = [
            'recordedBy' => 'q_recordedby',
            'catalogNumber' => 'q_catalognumber',
            'otherCatalogNumbers' => 'q_othercatalognumbers',
            'recordNumber' => 'q_recordnumber',
            'eventDate' => 'q_eventdate',
            'recordEnteredBy' => 'q_recordenteredby',
            'dateEntered' => 'q_dateentered',
            'dateLastModified' => 'q_datelastmodified',
            'exsiccatiid' => 'q_exsiccatiid',
            'processingStatus' => 'q_processingstatus',
        ];

        $editor_mutations = [
            'hasImages' => fn($v) => match($v) {
                "with_images" => 'q_imgonly',
                "without_images" => 'q_withoutimg',
                default => $v
            }
        ];

        $base_url = url(config('portal.name') . '/collections/editor/occurrenceeditor.php?');

        $remapped_params = remapAssoc($other_params, $editor_renames, $editor_mutations);
        $remapped_params['reset'] = true;

        $query = http_build_query($remapped_params);

        return $base_url . $query;
    }

    // TODO (Logan) Rework when map search gets transfered
    // This is need to interop with old occurrence editor form
    function getMapSearchUrl() {
        $params = request()->all();
        $legacy_search_renames = [
            'collid' => 'db'
        ];

        $base_url = url(config('portal.name') . '/collections/map/index.php?');
        $remapped_params = remapAssoc(request()->all(), $legacy_search_renames);

        $query = http_build_query($remapped_params);

        return $base_url . $query;
    }

     $custom_fields = [];
     for($i = 1; $i < 10; $i++) {
         $type = request('q_customtype' . $i);
         $field = request('q_customfield' . $i);
         $value = request('q_customvalue' . $i);

        if($type && $field && $value) {
            array_push($custom_fields,[
                [
                'id' => $i,
                'type' => $type,
                'field' => $value,
                'value' => $field,
                ]
            ]);
        }
    }

    if(count($custom_fields) === 0) {
        $custom_fields = [
            [
            'id' => 1,
            'type' => 'EQUALS',
            'field' => NULL,
            'value' => "",
            ]
        ];
    }
@endphp
<x-layout class="p-0 h-[100vh] relative" x-data="{ menu_open: false}" :hasFooter="false" :hasHeader="false"
    :hasNavbar="false">
    <div class="pt-4 px-4 flex flex-col gap-2 h-[7rem] relative">
        @if(!empty($collection))
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('')],
            ['title' => 'Collection Management', 'href' => url('/collections/'. request('collid'))],
            ['title' => 'Occurrence Table view'],
        ]" />
        @else
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('')],
            ['title' => 'Search Criteria', 'href' => url('/collections/search')],
            ['title' => 'Specimen Records Table'],
        ]" />
        @endif

        @if(!empty($collection))
        <div class="text-2xl text-primary font-bold">
            {{$collection->collectionName}} ({{$collection->institutionCode}})
        </div>
        @else
        <div class="text-2xl text-primary font-bold">
           Specimen Records Table
        </div>
        @endif

        <div class="flex gap-4 absolute right-4 items-center">
            <x-button x-on:click="menu_open = 'search'">Adjust Search</x-button>
            <x-button x-on:click="menu_open = 'batch_update'">Batch Update</x-button>

            <x-button class="w-fit" href="{{ getMapSearchUrl() }}" target="_blank">
                <i class="text-xl fa-solid fa-earth-americas"></i>
            </x-button>

            <x-button class="w-fit" onclick="copyUrl()">
                <i class="text-xl fa-regular fa-copy"></i>
            </x-button>

            <x-button class="w-fit" onclick="openWindow(`{{ url('collections/download') }}` + window.location.search)">
                <i class="text-xl fa-solid fa-download"></i>
            </x-button>
        </div>
    </div>

    <div class="absolute h-screen w-[50%vw ]min-w-[40rem] bg-base-100 z-[100] top-0 left-0" x-cloak x-show="menu_open === 'batch_update'">
        <button x-on:click="menu_open = false"
            class="float-right mr-2 mt-2 hover:ring-4 focus:outline-none focus:ring-4 rounded-md w-6 h-6 ring-accent">
            <i class="cursor-pointer fa fas fa-close"></i>
        </button>
        <fieldset class="p-4 flex flex-col gap-4">
            <legend class="text-2xl font-bold">Record Search Form</legend>
            <form id="search_form" hx-get="{{ url('/collections/table') . '?' }}" hx-target="#table-container" x-on:htmx:after-request.window="menu_open = false;" hx-swap="outerHTML" class="flex flex-col gap-4" hx-include="#search_form input">
                <x-occurrence-attribute-select name="field_name" class="min-w-72" :select_text="'Select Field Name'" />
                <x-input label="Current Value" id="current_value" />
                <x-input label="New Value" id="new_value" />
                <x-button type="submit">Batch Update Field</x-button>
            </form>
        </fieldset>
    </div>

    <div class="absolute h-screen w-[50%vw ]min-w-[40rem] bg-base-100 z-[100] top-0 left-0" x-cloak x-show="menu_open === 'search'">
        <button x-on:click="menu_open = false"
            class="float-right mr-2 mt-2 hover:ring-4 focus:outline-none focus:ring-4 rounded-md w-6 h-6 ring-accent">
            <i class="cursor-pointer fa fas fa-close"></i>
        </button>
        <fieldset class="p-4 flex flex-col gap-4">
            <legend class="text-2xl font-bold">Record Search Form</legend>
            <form id="search_form" hx-get="{{ url('/collections/table') . '?' }}" hx-target="#table-container" x-on:htmx:after-request.window="menu_open = false;"
                hx-swap="outerHTML" class="flex flex-col gap-4">
                <input type="hidden" name="fragment" value="table">
                <input type="hidden" name="collid" value="{{ request('collid') }}">
                <div class="flex gap-4 items-center">
                    <x-input label="Collector" id="recordedBy"/>
                    <x-input label="Number" id="recordNumber" />
                    <x-input label="Date" id="eventDate" />
                </div>

                <div class="flex gap-4 items-center">
                    <x-input label="Catalog Number" id="catalogNumber" />
                    <x-input label="Other Catalog Numbers" id="otherCatalogNumbers" />
                </div>

                <div class="flex gap-4 items-center">
                    <x-input label="Entered By" id="recordEnteredBy" />
                    <x-input label="Date Entered" id="dateEntered" />
                    <x-input label="Date Modified" id="dateLastModified" />
                    <x-button class="mt-7">CU</x-button>
                </div>

                <div class="flex gap-4 items-center">
                <x-select class="w-60" name="processingStatus" default="0" :items="[
                    ['title' => 'All Records', 'value' => '', 'disabled' => false],
                    ['title' => 'Unprocessed', 'value'=> 'unprocessed', 'disabled' => false],
                    ['title' => 'Unprocessed/NLP','value'=>'unprocessed/nlp', 'disabled' => false],
                    ['title' => 'Stage 1', 'value'=>'stage 1', 'disabled' => false],
                    ['title' => 'Stage 2', 'value'=>'stage 2', 'disabled' => false],
                    ['title' => 'Stage 3', 'value'=>'stage 3', 'disabled' => false],
                    ['title' => 'Pending Review-nfn', 'value'=>'pending review-nfn', 'disabled' => false],
                    ['title' => 'Pending Review', 'value'=>'pending review', 'disabled' => false],
                    ['title' => 'Expert Required', 'value'=>'expert required', 'disabled' => false],
                    ['title' => 'Reviewed', 'value' =>'reviewed', 'disabled' => false],
                    ['title' => 'Closed', 'value' =>'closed', 'disabled' => false],
                    ['title' => 'No Set Status', 'value' => 'isnull', 'disabled' => false]
                ]" />
                    <x-radio id="hasImages"
                        name="hasImages"
                        :options="[ ['label' => 'All', 'value' => 0], ['label' => 'With Images', 'value' => 'with_images'], ['label' => 'Without Images', 'value' => 'without_images']]"
                    />
                </div>
                @if(false)
                <x-select id="exsiccatiid" class="w-full" :items="[
                    ['title' => 'Select Exsiccati', 'value' => null, 'disabled' => false]
                ]" />
                @endif

                <div x-data="{ data: {{ json_encode($custom_fields) }} }" class="flex flex-col gap-4">
                <template x-for="(d, index) of data" :key="'custom-form-' + index">
                <div>
                    <div>
                    Custom Field <span x-text="index + 1"></span>
                    </div>
                    <div class="flex flex-row gap-4 items-center">
                        {{--
                        <x-select x-bind:name="'q_customopenparen' + (index + 1)" :default="0" :items="[
                            ['title' => '---', 'value' => null, 'disabled' => false],
                            ['title' => '(', 'value' => '(', 'disabled' => false],
                            ['title' => '((', 'value' => '((', 'disabled' => false],
                            ['title' => '(((', 'value' => '(((', 'disabled' => false],
                        ]"/>
                        --}}
                        <x-occurrence-attribute-select x-bind:name="'q_customfield' + (index + 1)" class="min-w-72" :select_text="'Select Field Name'" />
                        <x-select class="min-w-72" defaultValue='d.type' onChange="data[index].type = $event.target.value" x-bind:name="'q_customtype' + (index + 1)" x-bind:id="'q_customtype' + (index + 1)" :items="[
                            ['title'=> 'EQUALS', 'value' => 'EQUALS', 'disabled' => false ],
                            ['title'=> 'NOT EQUALS', 'value' => 'NOT_EQUALS', 'disabled' => false ],
                            ['title'=> 'STARTS WITH', 'value' => 'STARTS_WITH', 'disabled' => false ],
                            ['title'=> 'CONTAINS', 'value' => 'LIKE', 'disabled' => false ],
                            ['title'=> 'DOES NOT CONTAIN', 'value' => 'NOT_LIKE', 'disabled' => false ],
                            ['title'=> 'GREATER THAN', 'value' => 'GREATER_THAN', 'disabled' => false ],
                            ['title'=> 'LESS THAN', 'value' => 'LESS_THAN', 'disabled' => false ],
                            ['title'=> 'IS NULL', 'value' => 'IS_NULL', 'disabled' => false ],
                            ['title'=> 'IS NOT NULL', 'value' => 'NOT_NULL', 'disabled' => false ],
                        ]"/>
                        <x-input x-bind:name="'q_customvalue' + (index + 1)" x-bind:value="data && data[index]? data[index].value: ''" x-on:change='data[index].value = $event.target.value;'/>
                        {{--
                        <x-select x-bind:name="'q_customclosedparen' + (index + 1)" :default="0" :items="[
                            ['title' => '---', 'value' => null, 'disabled' => false],
                            ['title' => ')', 'value' => ')', 'disabled' => false],
                            ['title' => '))', 'value' => '))', 'disabled' => false],
                            ['title' => ')))', 'value' => ')))', 'disabled' => false],
                        ]"/>
                        --}}

                        <x-button type="button" @click="data.splice(index, 1)">
                            <i class="fa fa-minus"></i>
                        </x-button>
                    </div>
                </div>
                </template>
                    <x-button type="button" @click="if(data.length < 10) data.push({type: 'EQUALS', field: null, value: ''})">
                        <i class="fa fa-plus"></i> Add Custom Field
                    </x-button>
                </div>

                @if(false)
                <div class="flex gap-4 items-center">
                    <x-select id="sort" class="w-full" label="Sort By" :items="[
                    ['title' => '--------', 'value' => null, 'disabled' => false]
                ]" />
                    <x-select id="sortDirection" class="w-full" label="Order By" :items="[
                    ['title' => '--------', 'value' => null, 'disabled' => false]
                ]" />
                    <x-select class="w-full" label="Record Output" :items="[
                    ['title' => '--------', 'value' => null, 'disabled' => false]
                ]" />
                </div>
                @endif

                <div class="flex gap-4 items-center">
                    {{-- <x-button>Display Editor</x-button> --}}
                    <x-button type="submit">Display Table</x-button>
                    <x-button variant="neutral" onclick="document.getElementById('search_form').reset()">Display Reset
                        Form</x-button>
                </div>
            </form>
        </fieldset>
    </div>
    @php
    $property_display_map = [
    ['label' => 'Symbiota ID', 'name' => 'occid'],
    ['label' => 'Institution Code', 'name' => 'institutionCode'],
    ['label' => 'Catalog Number', 'name' => 'catalogNumber'],
    ['label' => 'Other Catalog Numbers','name' => 'otherCatalogNumbers'],
    ['label' => 'Family', 'name' => 'family'],
    ['label' => 'Scientific Name', 'name' => 'sciname'],
    ['label' => 'Author', 'name' => 'scientificNameAuthorship'],
    ['label' => 'Collector', 'name' => 'recordedBy'],
    ['label' => 'Number', 'name' => 'recordNumber'],
    ['label' => 'Associated Collectors', 'name' => 'associatedCollectors'],
    ['label' => 'Event Date', 'name' => 'eventDate'],
    ['label' => 'Verbatim Date', 'name' => 'verbatimEventDate'],
    ['label' => 'Identified By', 'name' => 'identifiedBy'],
    ['label' => 'Country', 'name' => 'country'],
    ['label' => 'State/Province', 'name' => 'stateProvince'],
    ['label' => 'County', 'name' => 'county'],
    ['label' => 'locality', 'name' => 'locality'],
    ['label' => 'Latitude', 'name' => 'latitudeDecimal'],
    ['label' => 'Longitude', 'name' => 'longitudeDecimal'],
    ['label' => 'Uncertainty In Meters', 'name' => 'coordinateUncertaintyInMeters'],
    ['label' => 'Verbatim Coordinates', 'name' => 'verbatimCoordinates'],
    ['label' => 'Datum', 'name' => 'geodeticDatum'],
    ['label' => 'Georeferenced By', 'name' => 'georeferencedBy'],
    ['label' => 'Georeference Sources', 'name' => 'georeferenceSources'],
    ['label' => 'GeoRef Verification Status', 'name' => 'georeferenceVerificationStatus'],
    ['label' => 'GeoRef Remarks', 'name' => 'georeferenceRemarks'],
    ['label' => 'Elev. Min. (m)', 'name' => 'minimumElevationInMeters'],
    ['label' => 'Elev. Max. (m)', 'name' => 'maximumElevationInMeters'],
    ['label' => 'Verbatim Elev.', 'name' => 'verbatimElevation'],
    ['label' => 'Habitat', 'name' => 'habitat'],
    ['label' => 'Substrate', 'name' => 'substrate'],
    ['label' => 'Notes (Occurrence Remarks)', 'name' => 'occurrenceRemarks'],
    ['label' => 'Associated Taxa', 'name' => 'associatedTaxa'],
    ['label' => 'Description', 'name' => 'lifeStage'],
    ['label' => 'Life Stage', 'name' => 'lifeStage'],
    ['label' => 'Date Last Modified', 'name' => 'dateLastModified'],
    ['label' => 'Processing Status', 'name' => 'processingStatus'],
    ['label' => 'EnteredBy', 'name' => 'recordEnteredBy'],
    ['label' => 'Basis Of Record', 'name' => 'basisOfRecord'],
    ];
    @endphp

    <div class="relative" x-data="{ occid: null, column: null, column_property: null, column_value: null, loading: false }">
        <div x-show="loading" x-cloak id="table-loader" class="z-[100] stroke-accent w-full h-full flex justify-center absolute">
            <x-icons.loading />
        </div>
       <x-context-menu>
            <x-slot:menu>
            </div>
                <x-context-menu-item x-on:click="menu_open = true">
                    Adjust Search
                </x-context-menu-item>

                <x-context-menu-item type='divider' />

                <x-context-menu-item>
                    <a x-bind:href="'{{ url(config('portal.name') . '/collections/individual/index.php') }}?occid=' + occid">Open Occurrence</a>
                </x-context-menu-item>
                <x-context-menu-item>
                    <a target="_blank" x-bind:href="'{{ url(config('portal.name') . '/collections/individual/index.php') }}?occid=' + occid">Open Occurrence in New
                        Tab</a>
                </x-context-menu-item>

                <x-context-menu-item type='divider' />

                <x-context-menu-item>
                    <a
                        x-bind:href="document.getElementById('occid-link-' + occid)">Open
                        Occurrence Editor</a>
                </x-context-menu-item>
                <x-context-menu-item>
                    <a target="_blank"
                        x-bind:href="document.getElementById('occid-link-' + occid)">Open
                        Occurrence Editor in New
                        Tab</a>
                </x-context-menu-item>

                <x-context-menu-item type='divider' />

                <x-context-menu-item hx-include="#sort"
                    hx-get="{{url('collections/table') . '?fragment=table&collid='. request('collid')}}" hx-trigger="click"
                    hx-target='#table-container' hx-swap="outerHTML"

                    x-on:htmx:xhr:loadstart="loading = true"
                    x-on:htmx:xhr:loadend="loading = false"
                    >
                    <input type="hidden" name="sort" id="sort" x-bind:value="column_property" />
                    Sort By&nbsp;<span x-text="column"></span>
                </x-context-menu-item>

                <x-context-menu-item hx-include="#filter"
                    hx-get="{{url('collections/table') . '?fragment=table&collid='. request('collid') }}" hx-trigger="click"
                    hx-target='#table-container' hx-swap="outerHTML"

                    x-on:htmx:xhr:loadstart="loading = true"
                    x-on:htmx:xhr:loadend="loading = false"
                    >
                    <input type="hidden" x-bind:name="column_property" id="filter" x-bind:value="column_value" />
                    Filter By&nbsp;<span x-text="column"></span>
                </x-context-menu-item>

                <x-context-menu-item hx-get="{{ url('collections/table') . '?fragment=table&collid='. request('collid') }}"
                    hx-trigger="click" hx-target='#table-container' hx-swap="outerHTML"

                    x-on:htmx:xhr:loadstart="loading = true"
                    x-on:htmx:xhr:loadend="loading = false"
                    >
                    Clear Filters
                </x-context-menu-item>
            </x-slot:menu>

            @fragment('table')
            <div id="table-container"
                class="overflow-x-scroll overflow-y-scroll w-screen h-[calc(100vh-7rem)] relative">
                <table class="w-full border-seperate">
                    <thead class="text-left">
                        <tr class="bg-base-100">
                            @foreach($property_display_map as $property)
                            <th @class([ 'bg-neutral text-neutral-content sticky top-0 text-nowrap p-2 border border-base-content z-50'
                                , 'pl-4'=> $loop->first,
                                'pr-4' => $loop->last,
                                ])>
                                {{ $property['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @fragment('rows')
                        @foreach ($occurrences as $occurrence)
                        <tr @contextmenu="occid = {{$occurrence->occid}}" @class([ 'bg-base-200'=> $loop->even,
                            'bg-base-300' => $loop->odd,
                            ])>
                            @foreach($property_display_map as $property)
                            <td @contextmenu="column = '{{$property['label']}}'; column_property = '{{$property['name']}}'; column_value = $el.innerHTML.trim();"
                                @class([ 'p-2 text-nowrap border border-base-content' , 'pl-4'=> $loop->first,
                                'pr-4' => $loop->last,
                                'sticky left-0 text-neutral-content' => $property['name'] === 'occid',
                                'bg-neutral' =>$loop->parent->odd && $property['name'] === 'occid',
                                'bg-neutral-lighter' =>$loop->parent->even && $property['name'] === 'occid',
                                ])>
                                @if($property['name'] === 'occid')
                                @php
                                $url = getOccurrenceEditorUrl([
                                    'csmode' => 0,
                                    'collid' => $occurrence->collid,
                                    'occid' => $occurrence->occid,
                                    'occindex' => (100 * $page) + $loop->parent->index
                                ]);
                                @endphp
                                <a id="occid-link-{{$occurrence->occid}}" href="{{ $url }}" class="cursor-pointer">{{ $occurrence->{$property['name']} }}</a>
                                <a target="_blank" href="{{ $url }}" class="cursor-pointer">
                                    <i class="fa-solid fa-up-right-from-square"></i>
                                </a>
                                @else
                                {{ $occurrence->{$property['name']} ?? ''}}
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        @if(count($occurrences) === 100)
                                <tr class="h-0 w-full" hx-get="{{ url('/collections/table') . '?&fragment=rows&page='. $page + 1 . '&' . http_build_query(request()->except(['page', 'fragment'])) }}" hx-indicator="#scroll-loader" hx-trigger="intersect once" hx-swap="afterend"></tr>
                        @endif
                        @endfragment
                    </tbody>
                </table>
                <div id="scroll-loader" class="htmx-indicator">
                    Loading more records...
                </div>
            </div>
            @endfragment
        </x-context-menu>
    </div>
</x-layout>
