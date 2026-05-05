@php
    $mode = $mode ?? 'create';
    $kingdoms = $kingdoms ?? [];
    $allTaxonRanks = $allTaxonRanks ?? [];
    $indContent = $indContent ?? [];
    $securityOptions = $securityOptions ?? [];
    $errors = $errors ?? [];
    $canCreateOrEdit = $canCreateOrEdit ?? false;
    $taxonInfo = $taxonInfo ?? null;
    $parentName = $parentName ?? '';
    $acceptedName = $acceptedName ?? '';
    $securitystatusstart = $securitystatusstart ?? 0;
    $verifyArr = $verifyArr ?? [];
@endphp
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs
            :items="[
            ['title' => 'Home', 'href' => url('')],
            [
                'title' => 'Taxononmic Tree View',
                'href' => legacy_url('/taxa/taxonomy/taxonomydisplay.php'),
            ],
            ['title' => $mode === 'create' ? __('taxonomy_taxonomyloader.CREATE_TAXON') : __('profile_tpeditor.EDIT_TAXON')],
        ]"
        />
    </div>
    <h1 class="mb-4 text-center text-2xl font-bold">{{ $taxonInfo->sciName ?? '' }}</h1>
    <div id="taxon-edit-tabs-container" name="taxon-edit-tabs-container">
        <x-tabs id="taxon-edit-tabs" :tabs="['Editor', 'Synonyms', 'Hierarchy', 'Child Taxa', 'Delete']">
            {{-- Editor --}}
            <div>
                <x-pages.taxon.taxon-create-and-edit
                    :mode="$mode ?? 'edit'"
                    :canCreateOrEdit="$canCreateOrEdit ?? false"
                    :allTaxonRanks="$allTaxonRanks ?? collect()"
                    :indContent="$indContent ?? []"
                    :securityOptions="$securityOptions ?? []"
                    :securitystatusstart="$securitystatusstart ?? 0"
                    :taxonInfo="$taxonInfo ?? null"
                    :parentName="$parentName ?? ''"
                    :acceptedName="$acceptedName ?? ''"
                />
            </div>

            {{-- Synonyms --}}
            <div>
                <x-taxonomy-synonym-edit :synonyms="$taxonInfo->synonyms ?? []" :mode="$mode" :taxonInfo="$taxonInfo" />
            </div>

            {{-- Hierarchy --}}
            <div>
                <p>A2</p>
            </div>

            {{-- Child Taxa --}}
            <div>
                <p>A3</p>
            </div>

            {{-- Delete --}}
            <div>
                <x-taxonomy-delete :verifyArr="$verifyArr" :taxonInfo="$taxonInfo"></x-taxonomy-delete>
            </div>
        </x-tabs>
    </div>
</x-layout>
