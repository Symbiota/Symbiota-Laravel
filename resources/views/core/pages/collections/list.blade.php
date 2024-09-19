@props(['occurrences' => [1, 2, 3, 1, 2, 3,1, 2, 3,1, 2, 3]])
<x-layout class="grid grid-col-1 gap-4">
    <h1 class="text-5xl font-bold">Occurrence Records (WIP)</h1>
    <div>
        Dataset: All collections
        Taxa: (Taxa list)
        Search Criteria: ( Figure out what this is for )
    </div>

    <div class="text-2xl font-bold">Occurrence Records Tab</div>
    <div class="flex items-center gap-4">
        <x-button class="w-fit">Table Button ( Table Display )</x-button>
        <x-button class="w-fit">Download ( Download Specimen Data )</x-button>
        <x-button class="w-fit">Link ( Copy to Url )</x-button>
    </div>

    <div class="text-2xl font-bold">Species List Tab</div>
    <div class="flex items-center gap-4">
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
                Download Checklist Data
            </a>
        </x-button>
    </div>

    <div class="text-2xl font-bold">Maps Tab</div>
    <div class="flex items-center gap-4">
        <x-button class="w-fit">Display coordinates in Map</x-button>
        <x-button class="w-fit">Create KML</x-button>
        {{-- Investigate but pretty sure this just adds info to download --}}
        <x-link>Add Extra Fields</x-link>
    </div>

    <div class="grid grid-col-1 gap-4">
        @foreach ($occurrences as $occurrence)
        <x-collections.list.item/>
        @endforeach
    </div>
</x-layout>
