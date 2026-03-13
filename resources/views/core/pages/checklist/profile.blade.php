@props(['checklist', 'taxons' => [], 'vouchers' => []])
@php
$families = [];
$genera = [];
$species = [];
$taxa_vouchers = [];

global $SERVER_ROOT, $LANG;
include_once(legacy_path('/classes/ChecklistManager.php'));
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('checklists/checklist');

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

//Construct Voucher Lookup
foreach($vouchers as $voucher) {
    if(array_key_exists($voucher->tid, $taxa_vouchers)) {
        array_push($taxa_vouchers[$voucher->tid], $voucher);
    } else {
        $taxa_vouchers[$voucher->tid] = [$voucher];
    }
}

//Set Display Settings
$defaultSettings = json_decode($checklist->defaultSettings ?? "{}");
$show_synonyms = $defaultSettings->dsynonyms ?? false;
$show_common = $defaultSettings->dcommon ?? false;
$show_notes_vouchers = $defaultSettings->dvouchers ?? false;
$show_taxa_authors = $defaultSettings->dauthors ?? false;
$show_taxa_alphabetically = $defaultSettings->dalpha ?? false;

//Override defaults if using the search form
if(request('partial') === 'taxa-list') {
    $show_synonyms = request('show_synonyms');
    $show_common = request('show_common');
    $show_notes_vouchers = request('show_notes_vouchers');
    $show_taxa_authors = request('show_taxa_authors');
    $show_taxa_alphabetically = request('show_taxa_alphabetically');
}

$clManager = new ChecklistManager();
$clManager->setClid($checklist->clid);
$clManager->setShowCommon(true);
$clManager->setShowSynonyms(true);
$clManager->setShowCommon(true);
$clManager->setShowVouchers(true);

if($show_taxa_authors) {
    $clManager->setShowAuthors(true);
}
if($show_taxa_alphabetically) {
    $clManager->setShowAlphaTaxa(true);
}
//$clManager->setShowSubgenera(true);

$taxaList = $clManager->getTaxaList(1, 0);
$voucherArr = $clManager->getVoucherArr();
$parent = $clManager->getParentChecklist();
$children = $clManager->getChildClidArr();
$exclusions = $clManager->getExclusionChecklist();

//Handling Dynamic Breadcrumbs
$breadcrumbs = [
    ['title' => 'Home', 'href' => url('') ],
];

if($checklist->projname && $checklist->pid) {
    $breadcrumbs[] = [
        'title' => $checklist->projname,
        'href' => legacy_url('/projects/index.php?pid='. $checklist->pid)
    ];
}

$breadcrumbs[] = $checklist->name;

@endphp
<x-margin-layout>
    <div>
    <x-breadcrumbs :items="$breadcrumbs" />
    </div>
    <div class="flex items-center">
        <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
        <div class="flex flex-grow justify-end gap-4">
            @can('CL_ADMIN', $checklist->clid)
            <a href="{{legacy_url('/checklists/checklistadmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> A
            </a>
            <a href="{{legacy_url('/checklists/voucheradmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> V
            </a>
            {{-- TODO (Logan) Figure out what this is. It is a js toggle but can we just provide options? if authorized?
            <a href="">
                <i class="flex-end fas fa-edit"></i> Spp
            </a>
            --}}
            @endcan
        </div>
    </div>
    {{-- TODO (Logan) figure out alternatives to this --}}
    @if(isset($checklist->abstract) || isset($checklist->authors) || isset($checklist->locality))
    <x-accordion label='More Details' variant="clear-primary">
        <div class="flex flex-col gap-2">
            <x-checklist.metadata
                :checklist="$checklist"
                :parent="$parent"
                :children="$children"
                :exclusions="$exclusions"
            />
        </div>
    </x-accordion>
    @endif
    {{-- TODO (Logan) scope to clid --}}


    <div class="flex items-center gap-2">
        <div class="flex w-fit">
            <x-popover class="w-[500px]">
                <form hx-get="{{ url()->current() }}" class="flex flex-col gap-2" hx-target="#taxa-list">
                    <input type="hidden" name="partial" value="taxa-list">
                    <x-taxa-search />
                    <x-link href="{{ legacy_url('/ident/key.php') }}?dynclid={{ 0 }}&clid={{ $checklist->clid }}">Open Symbiota Key</x-link>
                    <x-link href="{{ legacy_url('/games/flashcards.php') }}?dynclid={{ 0 }}&listname={{ $checklist->name }}&clid={{ $checklist->clid }}">Flash Cards</x-link>
                    <x-link href="{{ legacy_url('/games/namegame.php') }}?dynclid={{ 0 }}&listname={{ $checklist->name }}&clid={{ $checklist->clid }}">Name Game</x-link>

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
                    <x-checkbox
                        label="Show Taxa Alphabetically"
                        :checked="$defaultSettings->dalpha ?? false"
                        name="show_taxa_alphabetically"
                    />
                    <div class="flex items-center">
                        <x-button x-on:click="popoverOpen=false">Build List</x-button>
                        <div class="flex flex-grow justify-end gap-4 text-xl">
                            <i class="fa-solid fa-download"></i>
                                <a target="_blank" href="{{ url('checklists/' . $checklist->clid. '/pdf') }}">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            <i class="fa-regular fa-file-word"></i>
                        </div>
                    </div>
                </form>
            </x-popover>
        </div>
        <div class="flex items-center gap-2 w-full">
            @foreach([
                $LANG['FAMILIES'] => $clManager->getFamilyCount(),
                $LANG['GENERA'] => $clManager->getGenusCount(),
                $LANG['SPECIES'] => $clManager->getSpeciesCount(),
                $LANG['TOTAL_TAXA'] => $clManager->getTaxaCount(),
            ] as $label => $value)
            <div><span class="font-bold">{{ $label }}: </span>{{ $value }}</div>
            @endforeach

            <div class="flex-grow">
                <x-button
                    class="ml-auto"
                    href="{{ legacy_url('/collections/map/index.php')}}?db=all&type=1&reset=1&taxonfilter&cltype=vouchers&clid={{$checklist->clid}}">
                        <i class="fas fa-earth-americas"></i> Map
                </x-button>
            </div>
        </div>
    </div>
    <x-checklist.taxa-list
        :taxa="$taxaList"
        :taxa_vouchers="$voucherArr"
        :checklist="$checklist"
        :show_synonyms="$show_synonyms"
        :show_common="$show_common"
        :show_notes_vouchers="$show_notes_vouchers"
        :show_taxa_authors="$show_taxa_authors"
        :show_taxa_alphabetically="$show_taxa_alphabetically"
    />
</x-layout>
