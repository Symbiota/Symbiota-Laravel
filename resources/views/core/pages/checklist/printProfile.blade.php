@props(['checklist', 'taxons' => [], 'vouchers' => []])

@php

global $SERVER_ROOT, $LANG;
include_once(legacy_path('/classes/ChecklistManager.php'));
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('checklists/checklist');

$defaultSettings = json_decode($checklist->defaultSettings ?? "{}");
$show_synonyms = request('show_synonyms') ?? $defaultSettings->dsynonyms ?? false;
$show_common = request('show_common') ?? $defaultSettings->dcommon ?? false;
$show_notes_vouchers = request('show_notes_vouchers') ?? $defaultSettings->dvouchers ?? false;
$show_taxa_authors = request('show_taxa_authors') ?? $defaultSettings->dauthors ?? false;
$show_taxa_alphabetically = request('show_taxa_alphabetically') ?? $defaultSettings->dalpha ?? false;

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

@endphp

<x-margin-layout :hasHeader="false" :hasFooter="false" :hasNavbar="false">
    <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
    <hr/>

    <div class="flex flex-col">
        <x-checklist.metadata :checklist="$checklist" :parent="$parent" :children="$children" :exclusions="$exclusions"/>
    </div>

    <hr/>

    <div class="flex items-center gap-2 w-full">
    @foreach([
        $LANG['FAMILIES'] => $clManager->getFamilyCount(),
        $LANG['GENERA'] => $clManager->getGenusCount(),
        $LANG['SPECIES'] => $clManager->getSpeciesCount(),
        $LANG['TOTAL_TAXA'] => $clManager->getTaxaCount(),
    ] as $label => $value)
        <div><span class="font-bold">{{ $label }}: </span>{{ $value }}</div>
    @endforeach
    </div>
    <hr/>
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
</x-margin-layout>
