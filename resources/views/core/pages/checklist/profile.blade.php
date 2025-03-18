@props(['' => $checklist, 'taxons' => []])
@php
$families = [];
$genera = [];
$species = [];

//Construct Family Tree
foreach($taxons as $taxon) {
    if($taxon->family) {
        if(isset($families[$taxon->family])) {
            $families[$taxon->family] += 1;
        } else {
            $families[$taxon->family] = 1;
        }
    }

    // Todo Add Rank Id check
    if($taxon->unitName1) {
        if(isset($genera[$taxon->unitName1])) {
            $genera[$taxon->unitName1] += 1;
        } else {
            $genera[$taxon->unitName1] = 1;
        }
    }

    if($taxon->unitName2) {
        if(isset($species[$taxon->unitName2])) {
            $species[$taxon->unitName2] += 1;
        } else {
            $species[$taxon->unitName2] = 1;
        }
    }
}

//Set Display Settings
$defaultSettings = json_decode($checklist->defaultSettings);
$show_synonyms = $defaultSettings->dsynonyms ;
$show_common = $defaultSettings->dcommon ;
$show_notes_vouchers = $defaultSettings->dvouchers;
$show_taxa_authors = $defaultSettings->dauthors;

//Override defaults if using the search form
if(request('partial') === 'taxa-list') {
    $show_synonyms = request('show_synonyms');
    $show_common = request('show_common');
    $show_notes_vouchers = request('show_notes_vouchers');
    $show_taxa_authors = request('show_taxa_authors');
}

@endphp
<x-layout class="grid grid-cols-1 gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => $checklist->projname, 'href' => url( config('portal.name') . '/projects/index.php?pid='. $checklist->pid) ],
        $checklist->name
    ]" />
    <div class="flex items-center">
        <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
        <div class="flex flex-grow justify-end gap-4">
            @can('SUPER_ADMIN')
            <a href="{{url(config('portal.name') . '/checklists/checklistadmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> A
            </a>
            <a href="{{url(config('portal.name') . '/checklists/voucheradmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> V
            </a>
            {{-- TODO (Logan) Toggle Spp controls --}}
            <a href="">
                <i class="flex-end fas fa-edit"></i> Spp
            </a>
            @endcan
        </div>
    </div>
    {{-- TODO (Logan) figure out alternatives to this --}}
    <x-accordion label='More Details' variant="clear-primary">
        <div class="flex flex-col gap-2">
            @isset($checklist->abstract)
                <div><span class="font-bold">Abstract:</span> {{ $checklist->abstract }}</div>
            @endisset

            @isset($checklist->authors)
                <div><span class="font-bold">Authors:</span> {{ $checklist->authors }}</div>
            @endisset

            @isset($checklist->locality)
                <div><span class="font-bold">Locality:</span> {{ $checklist->locality }}</div>
            @endisset
        </div>
    </x-accordion>
    {{-- TODO (Logan) scope to clid --}}


    <div class="flex items-center gap-2">
        <div class="flex w-fit">
            <x-popover class="w-[500px]">
                <form hx-get="{{ url()->current() }}" class="flex flex-col gap-2" hx-target="#taxa-list">
                    <input type="hidden" name="partial" value="taxa-list">
                    <x-taxa-search />
                    <x-link href="">Open Symbiota Key</x-link>
                    <x-link href="">Games</x-link>

                    <x-select class="w-64" default="0" :items="[
                        ['title' => 'Original Checklist', 'value' => 'Original Checklist', 'disabled' => false],
                        ['title' => 'Central Thesaurus', 'value' => 'Central Thesaurus', 'disabled' => false]
                    ]" />

                    <div class="text-lg font-bold">Taxonmic Filter</div>
                    <x-checkbox
                        label="Display Synonyms"
                        :checked="$show_synonyms"
                        name="show_synonyms"
                    />
                    <x-checkbox
                        label="Common Names"
                        :checked="$show_common"
                        name="show_common"
                    />

                    {{-- <x-checkbox
                        label="Display as Images"
                        :checked="$defaultSettings->dimages ?? false"
                        name="show_as_images"
                    /> --}}

                    <x-checkbox
                        label="Notes & Vouchers"
                        :checked="$show_notes_vouchers"
                        name="show_notes_vouchers"
                    />
                    <x-checkbox
                        label="Taxon Authors"
                        :checked="$show_taxa_authors"
                        name="show_taxa_authors"
                    />
                    {{-- They currently show Alphabetically already
                    <x-checkbox
                        label="Show Taxa Alphabetically"
                        :checked="$defaultSettings->dalpha ?? false"
                        name="sort_alphabetically"
                    />
                    --}}
                    <div class="flex items-center">
                        <x-button x-on:click="popoverOpen=false">Build List</x-button>
                        <div class="flex flex-grow justify-end gap-4 text-xl">
                            <i class="fa-solid fa-download"></i>
                            <i class="fa-solid fa-print"></i>
                            <i class="fa-regular fa-file-word"></i>
                        </div>
                    </div>
                </form>
            </x-popover>
        </div>
        <div class="flex items-center gap-2 w-full">
            <div><span class="font-bold">Families:</span> {{ count($families) }}</div>
            <div><span class="font-bold">Genera:</span> {{ count($genera) }}</div>
            <div><span class="font-bold">Species:</span> {{ count($species) }}</div>
            <div><span class="font-bold">Total Taxa:</span> {{ count($taxons) }}</div>

            <div class="flex-grow">
                <x-button
                    class="ml-auto"
                    href="{{ url(config('portal.name') . '/collections/map/index.php')}}?db=all&type=1&reset=1&taxonfilter&cltype=vouchers&clid={{$checklist->clid}}">
                        <i class="fas fa-earth-americas"></i> Map
                </x-button>
            </div>
        </div>
    </div>
    @fragment('taxa-list')
    <div id="taxa-list">
        @php $previous @endphp
        @foreach ($taxons as $taxon)
        @if($loop->first || $taxons[$loop->index - 1]->family !== $taxon->family)
        <div class="text-lg font-bold">{{ $taxon->family }}</div>
        @endif
        <div class="pl-4">
            <x-link class="text-base" href="{{ url('taxon/' . $taxon->tid) }}">
                {{ $taxon->sciname }}
                @if($show_taxa_authors && isset($taxon->author))
                {{ $taxon->author }}
                @endif
            </x-link>

            <x-nav-link href="{{url('collections/list')}}?usethes=1&clid={{$checklist->clid}}&taxa={{$taxon->tid}}">
            <i class="ml-4 fa-solid fa-list"></i>
            </x-nav-link>

            @if($show_common && isset($taxon->vernacularNames))
            <div class="pl-2">
                <span class="font-bold">Vernacular Names:</span>
                {{ $taxon->vernacularNames }}
            </div>
            @endif

            @if($show_synonyms && isset($taxon->synonyms))
            <div class="pl-2">
                <span class="font-bold">Synonyms:</span>
                {{ $taxon->synonyms }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endfragment
</x-layout>
