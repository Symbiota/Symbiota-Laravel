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
    <x-tabs :tabs="['Editor', 'Taxonomic Status', 'Hierarchy', 'Child Taxa', 'Delete']">
        {{-- Editor --}}
        <div>
            @include('core.pages.taxon._core_taxon_create_and_edit')
        </div>

        {{-- Taxonomic Status --}}
        <div>
            <p> A1</p>
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
            <p> A4</p>
        </div>
    </x-tabs>
</x-layout>