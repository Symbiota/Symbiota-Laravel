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
    @if (session('success'))
        <div class="alert alert-success">
            <span class="text-2xl" style="color: var(--color-info-darker)">{{ session('success') }}</span>
        </div>
    @endif
    @fragment('taxon_editor')
        @include('core.pages.taxon._core_taxon_create_and_edit')
    @endfragment
</x-layout>
