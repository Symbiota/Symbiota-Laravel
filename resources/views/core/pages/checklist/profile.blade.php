@props(['checklist', 'taxons' => [], 'vouchers' => []])
@php
global $SERVER_ROOT, $LANG;
include_once(legacy_path('/classes/ChecklistManager.php'));
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('checklists/checklist');


$isClAdmin = Gate::check('CL_ADMIN', $checklist->clid);
$statusStr = false;

//Set Display Settings
$defaultSettings = json_decode($checklist->defaultSettings ?? "{}");
$show_synonyms = request('show_synonyms') ?? $defaultSettings->dsynonyms ?? false;
$show_common = request('show_common') ?? $defaultSettings->dcommon ?? false;
$show_notes_vouchers = request('show_notes_vouchers') ?? $defaultSettings->dvouchers ?? false;
$show_taxa_authors = request('show_taxa_authors') ?? $defaultSettings->dauthors ?? false;
$show_taxa_alphabetically = request('show_taxa_alphabetically') ?? $defaultSettings->dalpha ?? false;

$clManager = new ChecklistManager();
$clManager->setClid($checklist->clid);

if($isClAdmin){
	if(request('formsubmit') === 'AddSpecies'){
		$statusStr = $clManager->addNewSpecies(request()->all());
	}
}

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
<x-margin-layout x-data="{ sppEditToggle: false }">
    <div>
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>
    <div class="flex items-center">
        <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
        <div class="flex flex-grow justify-end gap-2">
            @can('CL_ADMIN', $checklist->clid)
            <x-button href="{{legacy_url('/checklists/checklistadmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> A
            </x-button>
            <x-button href="{{legacy_url('/checklists/voucheradmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> V
            </x-button>

            <x-button @click="sppEditToggle = !sppEditToggle" >
                <i class="flex-end fas fa-edit"></i>Spp
                <span x-show="sppEditToggle">- ON</span>
            </x-button>
            @endcan
        </div>
    </div>

    @if($checklist->abstract || $checklist->authors || $checklist->locality || ($checklist->latCentroid && $checklist->longCentroid) || $checklist->notes || count($exclusions) || count($children) || count($parent))
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

    @if($statusStr)
    <div class="bg-error text-error-content p-2 rounded-md">{{ $statusStr }}</div>
    @endif

    <div class="flex items-center gap-2">
        <div class="flex w-fit">
            <x-popover class="w-[500px]">
                <form x-data="{ form: $el }" target="_blank" id="checklist-display-form" class="flex flex-col gap-2">
                    <x-taxa-search />
                    <x-link href="{{ legacy_url('/ident/key.php') }}?dynclid={{ 0 }}&clid={{ $checklist->clid }}">Open Symbiota Key</x-link>
                    <x-link href="{{ legacy_url('/games/flashcards.php') }}?dynclid={{ 0 }}&listname={{ $checklist->name }}&clid={{ $checklist->clid }}">Flash Cards</x-link>
                    <x-link href="{{ legacy_url('/games/namegame.php') }}?dynclid={{ 0 }}&listname={{ $checklist->name }}&clid={{ $checklist->clid }}">Name Game</x-link>

                    <x-select class="w-64" default="0" :items="[
                        ['title' => 'Original Checklist', 'value' => 'Original Checklist', 'disabled' => false],
                        ['title' => 'Central Thesaurus', 'value' => 'Central Thesaurus', 'disabled' => false]
                    ]" />

                    <input type="hidden" name="clid" value="{{ $checklist->clid }}" />
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
                        <x-button type="button"
                            x-on:click="popoverOpen=false"
                            hx-target="#taxa-list"
                            hx-vals='{"partial": "taxa-list"}'
                            hx-include="#checklist-display-form"
                            hx-get="{{ url()->current() }}"
                        >
                        {{ $LANG['BUILD_LIST'] }}
                        </x-button>
                        <div class="flex flex-grow items-center justify-end gap-4 text-xl">
                            <button name="dllist_x" type="submit"
                                data-action="{{ legacy_url('checklists/checklist.php') }}"
                                x-on:click="form.action=$el.getAttribute('data-action'); target='_blank';form.method='post'">
                                <x-icons.download />
                            </button>
                            <button type="submit"
                                data-action="{{ url('checklists/' . $checklist->clid. '/pdf') }}"
                                x-on:click="form.action=$el.getAttribute('data-action');form.target='_blank';form.method='get'">
                                <x-icons.print />
                            </button>
                            <button name="exportdoc" type="submit"
                                data-action="{{ legacy_url('checklists/mswordexport.php') }}"
                                x-on:click="form.action=$el.getAttribute('data-action'); form.target='_blank';form.method='post'">
                                <x-icons.word />
                            </button>
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

            <x-modal>
                <x-slot name="button">{{ $LANG['ADD_SPECIES'] }}</x-slot>
                <x-slot name="title" class="text-2xl">{{ $LANG['ADD_SPECIES']}}</x-slot>
                <x-slot name="body">
                    <form method="post" class="flex flex-col gap-4">
                        @csrf
                        <input type="hidden" name="partial" value="taxa-list" />
                        <input type="hidden" name="formsubmit" value="AddSpecies" />

                        <x-input :label="$LANG['TAXON']" id="speciestoadd" />
                        <x-input :label="$LANG['MORPHOSPECIES']" id="morphospecies" />
                        <x-input :label="$LANG['FAMILYOVERRIDE']" id="familyoverride" />
                        <x-input :label="$LANG['HABITAT']" id="habitat" />
                        <x-input :label="$LANG['ABUNDANCE']" id="abundance" />
                        <x-input :label="$LANG['NOTES']" id="notes" />
                        <x-input :label="$LANG['INTNOTES']" id="internalnotes" />
                        <x-input :label="$LANG['SOURCE']" id="source" />
                        <x-button>{{ $LANG['ADD_SPECIES'] }}</x-button>
                        <x-link href="{{ legacy_url('checklists/tools/checklistloader.php?clid=' . $checklist->clid .'&pid=' . $checklist->pid) }}">{{ $LANG['BATCH_LOAD_SPREADSHEET'] }}</x-link>
                    </form>
                </x-slot>
            </x-modal>
        </div>
    </div>

    <x-checklist.taxa-list
        :checklist="$checklist"
        :taxa="$taxaList"
        :taxa_vouchers="$voucherArr"
        :children="$children"
        :show_synonyms="$show_synonyms"
        :show_common="$show_common"
        :show_notes_vouchers="$show_notes_vouchers"
        :show_taxa_authors="$show_taxa_authors"
        :show_taxa_alphabetically="$show_taxa_alphabetically"
        sppEditToggle="sppEditToggle"
    />
</x-layout>
