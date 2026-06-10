@php
    $mode = $mode ?? 'create';
    $kingdoms = $kingdoms ?? [];
    $allTaxonRanks = $allTaxonRanks ?? [];
    $rankMap = $rankMap ?? [];
    $indContent = $indContent ?? [];
    $securityOptions = $securityOptions ?? [];
    $errors = $errors ?? [];
    $canCreateOrEdit = $canCreateOrEdit ?? false;
    $taxonInfo = $taxonInfo ?? null;
    $parentName = $parentName ?? '';
    $acceptedName = $acceptedName ?? '';
    $securitystatusstart = $securitystatusstart ?? 0;
    $verifyArr = $verifyArr ?? [];
    $parents = $parents ?? [];
    $upperTaxonomyEditInfo = $upperTaxonomyEditInfo ?? [];
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
    <h1 class="mb-4 text-center text-2xl font-bold">{{ $taxonInfo->sciName ?? '' }}</h1>
    <div id="taxon-edit-tabs-container" name="taxon-edit-tabs-container">
        <x-tabs id="taxon-edit-tabs" :tabs="['Editor', 'Taxonomic Status', 'Hierarchy', 'Child Taxa', 'Delete']">
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

            {{-- Taxonomic Status --}}
            <div>
                <x-taxonomy-synonym-edit :mode="$mode" :taxonInfo="$taxonInfo" />
            </div>

            {{-- Hierarchy --}}
            <x-upper-taxonomy-edit :upperTaxonomyEditInfo="$upperTaxonomyEditInfo ?? null" />
            <div id="taxon-tree">
                <x-tree-node :nodes="$parents" :rankMap="$rankMap" :standardizingFraction="5" :parentRankId="null" />
                <form method="POST" action="{{ route('taxon.reconstructHierarchy') }}">
                    @csrf
                    <input type="hidden" name="tid" value="{{ $taxonInfo->tid ?? '' }}">
                    <x-button type="submit" class="w-fit mt-4">{{ __('taxonomy_taxonomyloader.REBUILD_HIERARCHY') }}</x-button>
                </form>
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
