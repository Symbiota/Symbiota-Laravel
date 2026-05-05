@php
    $mode = $mode ?? 'create';
@endphp
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs
            :items="[
            ['title' => 'Home', 'href' => url('')],
            [
                'title' => 'Taxononmic Tree View',
                'href' => url('/taxon/'),
            ],
            ['title' => $mode === 'create' ? __('taxonomy_taxonomyloader.CREATE_TAXON') : __('profile_tpeditor.EDIT_TAXON')],
        ]"
        />
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            <span class="text-2xl" style="color: var(--color-info-darker)">{{ session('success') }}</span>
        </div>
    @endif
    @fragment('taxon_editor')
        <x-pages.taxon.taxon-create-and-edit
            :mode="$mode ?? 'create'"
            :canCreateOrEdit="$canCreateOrEdit ?? false"
            :allTaxonRanks="$allTaxonRanks ?? collect()"
            :indContent="$indContent ?? []"
            :securityOptions="$securityOptions ?? []"
            :securitystatusstart="$securitystatusstart ?? 0"
            :taxonInfo="$taxonInfo ?? null"
            :parentName="$parentName ?? ''"
            :acceptedName="$acceptedName ?? ''"
        />
    @endfragment
</x-layout>
