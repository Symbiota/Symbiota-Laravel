@props([
    'mode' => 'create',
    'kingdoms' => [],
    'allTaxonRanks' => [],
    'indContent' => [],
    'securityOptions' => [],
    'errors' => [],
    'canCreateOrEdit' => false,
    'taxonInfo' => null,
    'parentName' => '',
    'acceptedName' => '',
    'securitystatusstart' => 0,
    'verifyArr' => [],
])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('')],
            [
                'title' => 'Taxononmic Tree View',
                'href' => legacy_url('/taxa/taxonomy/taxonomydisplay.php'),
            ],
            ['title' => $mode === 'create' ? __('taxonomy_taxonomyloader.CREATE_TAXON') : __('profile_tpeditor.EDIT_TAXON')],
        ]" />
    </div>
    <h1 class="text-center text-2xl font-bold mb-4">
        {{ $taxonInfo->sciName }}
    </h1>
    <div id="taxon-edit-tabs-container" name="taxon-edit-tabs-container">
        <x-tabs id="taxon-edit-tabs" :tabs="['Editor', 'Synonyms', 'Hierarchy', 'Child Taxa', 'Delete']">
            {{-- Editor --}}
            <div>
                @include('core.pages.taxon._core_taxon_create_and_edit')
            </div>

            {{-- Synonyms --}}
            <div>
                @include('core.pages.taxon.taxonomicSynonymEdit')
            </div>

            {{-- Hierarchy --}}
            <div>
            <p> A2</p>
            </div>

            {{-- Child Taxa --}}
            <div>
                <p> A3</p>
            </div>

            {{-- Delete --}}
            <div>
                <x-taxonomy-delete :verifyArr="$verifyArr" :taxonInfo="$taxonInfo"></x-taxonomy-delete>
            </div>
        </x-tabs>
    </div>
</x-layout>