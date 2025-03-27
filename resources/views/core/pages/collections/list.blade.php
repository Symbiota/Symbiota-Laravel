@props(['occurrences' => []])
@php
   $dataset_str = request('db');
   if(is_array($dataset_str)) {
        $dataset_str = implode(',', $dataset_str);
   }

   $taxa_str = request('taxa');
   if(is_array($taxa_str)) {
        $taxa_str = implode(',', $taxa_str);
   }
@endphp
<x-layout class="grid grid-col-1 gap-4 grow-0">
    <div>
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Search Criteria', 'href' => url('/collections/search/') ],
        'Specimen Records'
    ]" />
    </div>
    <x-tabs :tabs="['Species List', 'Occurrence Records', 'Maps']" active="1">
        {{-- Species --}}
        <div class="flex items-center gap-4 h-60">
            <x-button class="w-fit">
                <a href="checklistsymbiota.php?taxatype=&taxa=&usethes=&taxonfilter=0&interface=checklist">
                    Open checklist Explorer
                </a>
            </x-button>
            <x-button class="w-fit">
                <a href="checklistsymbiota.php?taxatype=&taxa=&usethes=&taxonfilter=0&interface=key">
                    Open in Interactive Key Interface
                </a>
            </x-button>
            <x-button class="w-fit">
                <a href="download/index.php?taxatype=&taxa=&usethes=&taxonFilterCode=0&dltype=checklist">
                    <i class="fa-solid fa-download"></i>
                    Download Checklist Data
                </a>
            </x-button>
        </div>

        {{-- Occurrence Records --}}
        <div id="occurrence_result" class="grid grid-col-1 gap-4">
            <div class="flex flex-wrap">
                <div>
                    <div><span class="font-bold">Dataset: </span>{{ $dataset_str ?? 'All collections'}}</div>
                    <div><span class="font-bold">Taxa: </span>{{ $taxa_str ?? '' }}</div>
                    <div><span class="font-bold">Search Criteria: </span>( TODO )</div>
                </div>
                <div class="flex items-center gap-4 grow justify-end">
                    {{-- Todo make this the popover icon?
                    <x-tooltip text="Sort Results">
                        <x-button class="w-fit">
                            <i class="text-xl fa-solid fa-arrow-up-wide-short"></i>
                        </x-button>
                    </x-tooltip>
                    --}}
                    <x-popover>
                        <form hx-get="{{ url()->current() }}"
                              hx-vals="{{ json_encode(request()->except(['sort', 'sortDirection'])) }}"
                              hx-target="body" hx-push-url="true"
                              class="flex flex-col gap-4"
                              >
                            <input type="hidden" name="sortDirection" value="ASC" />
                            <x-select label="Sort By" class="w-full" defaultValue="'{{ request('sort') }}'" name="sort" :items="[
                                ['title' => 'Collection', 'value' => 'collid', 'disabled' => false],
                                ['title' => 'Catalog Number', 'value' => 'catalogNumber', 'disabled' => false],
                                ['title' => 'Family', 'value' => 'family', 'disabled' => false],
                                ['title' => 'Scientific Name', 'value' => 'sciname', 'disabled' => false],
                                ['title' => 'Collector', 'value' => 'recordedBy', 'disabled' => false],
                                ['title' => 'Collector Number', 'value' => 'recordNumber', 'disabled' => false],
                                ['title' => 'Date', 'value' => 'eventDate', 'disabled' => false],
                                ['title' => 'Country', 'value' => 'country', 'disabled' => false],
                                ['title' => 'State/Province', 'value' => 'stateProvince', 'disabled' => false],
                                ['title' => 'County', 'value' => 'county', 'disabled' => false],
                                ['title' => 'Elevation', 'value' => 'minimumElevationInMeters', 'disabled' => false],
                            ]"/>
                            <x-select label="Sort Direction" name="sortDirection" default="0" :items="[
                                ['title' => 'Ascending', 'value' => 'ASC', 'disabled' => false],
                                ['title' => 'Descending', 'value' => 'DESC', 'disabled' => false],
                            ]"/>
                            <x-button type="submit">Sort Results</x-button>
                        </form>
                    </x-popover>

                    <x-tooltip text="Display as Table">
                        <x-button class="w-fit">
                            <i class="text-xl fa-solid fa-table-list"></i>
                        </x-button>
                    </x-tooltip>
                    <x-tooltip text="Download Specimen Data">
                        <x-button class="w-fit" onclick="openWindow(`{{ url('collections/download') }}` + window.location.search)">
                            <i class="text-xl fa-solid fa-download"></i>
                        </x-button>
                    </x-tooltip>
                    <x-tooltip text="Copy Search Url to clipboard">
                        <x-button class="w-fit" onclick="copyUrl()">
                            <i class="text-xl fa-regular fa-copy"></i>
                        </x-button>
                    </x-tooltip>
                </div>
            </div>

            <x-pagination :lengthAwarePaginator="$occurrences"/>

            <div class="grid grid-col-1 gap-4">
                @foreach ($occurrences as $occurrence)
                <x-collections.list.item :occurrence="$occurrence" />
                @endforeach
            </div>
        </div>

        {{-- Maps --}}
        <div class="flex items-center gap-4 h-60">
            <a href="{{ url(config('portal.name') . '/collections/map/index.php?') . http_build_query(request()->all()) }}" target="_blank">
                <x-button class="w-fit">Display coordinates in Map</x-button>
            </a>
            <x-button class="w-fit">Create KML</x-button>
            {{-- Investigate but pretty sure this just adds info to download --}}
            <x-link>Add Extra Fields</x-link>
        </div>
    </x-tabs>

</x-layout>
