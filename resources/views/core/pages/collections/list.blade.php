@props(['occurrences' => []])
<x-layout class="grid grid-col-1 gap-4 grow-0">
    <div>
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Search Criteria', 'href' => url(config('portal.name') . '/collections/search/index.php') ],
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
        <div class="grid grid-col-1 gap-4">
            <div class="flex flex-wrap">
                <div>
                    <div>Dataset: All collections</div>
                    <div>Taxa: (Taxa list)</div>
                    <div>Search Criteria: ( Figure out what this is for )</div>
                </div>
                <div class="flex items-center gap-4 grow justify-end">
                    <x-tooltip text="Display as Table">
                        <x-button class="w-fit">
                            <i class="text-xl fa-solid fa-table-list"></i>
                        </x-button>
                    </x-tooltip>
                    <x-tooltip text="Download Specimen Data">
                        <x-button class="w-fit">
                            <i class="text-xl fa-solid fa-download"></i>
                        </x-button>
                    </x-tooltip>
                    <x-tooltip text="Copy Search Url to clipboard">
                        <x-button class="w-fit">
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
            <x-button class="w-fit">Display coordinates in Map</x-button>
            <x-button class="w-fit">Create KML</x-button>
            {{-- Investigate but pretty sure this just adds info to download --}}
            <x-link>Add Extra Fields</x-link>
        </div>
    </x-tabs>

</x-layout>
