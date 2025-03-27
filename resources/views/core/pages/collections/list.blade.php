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
