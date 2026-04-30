@props([
    'checklist',
    'clManager',

    'taxaList' => [],
    'voucherArr' => [],
    'parent' => [],
    'children' => [],
    'exclusions' => [],

    'show_synonyms' => false,
    'show_common' => false,
    'show_notes_vouchers' => false,
    'show_taxa_authors' => false,
    'show_images' => false,
    'show_taxa_alphabetically' => false,
    'limit_voucher_images' => false,
    'show_subgenera' => false,
    'activate_key' => false,
])

<x-margin-layout :hasHeader="false" :hasFooter="false" :hasNavbar="false">
    <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
    <hr />

    <div class="flex flex-col">
        <x-checklist.metadata
            :checklist="$checklist"
            :parent="$parent"
            :children="$children"
            :exclusions="$exclusions"
        />
    </div>

    <hr />

    <div class="flex w-full items-center gap-2">
        @foreach([
        __('checklists_checklist.FAMILIES') => $clManager->getFamilyCount(),
        __('checklists_checklist.GENERA') => $clManager->getGenusCount(),
        __('checklists_checklist.SPECIES') => $clManager->getSpeciesCount(),
        __('checklists_checklist.TOTAL_TAXA') => $clManager->getTaxaCount(),
    ] as $label => $value)
            <div><span class="font-bold">{{ $label }}: </span>{{ $value }}</div>
        @endforeach
    </div>
    <hr />
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
