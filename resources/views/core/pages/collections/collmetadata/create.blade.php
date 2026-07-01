@props([
    'collection' => [],
    'collid' => null,
    'fullCatArr' => [],
    'selectedCategories' => [],
    'rightsTerms' => [],
    'rightsState' => ['selected' => '', 'hasOrphan' => false],
    'showGbifPublishing' => false,
])

@php
    $breadcrumbs = [
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('misc_collmetadata.COL_PROFS'), 'href' => url('collections')],
        ['title' => __('misc_collmetadata.CREATE_COLL')],
    ];
    $tabs = [__('misc_collmetadata.COL_META_EDIT')];
@endphp

<x-margin-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>

    <div class="collmetadata-page">
        @if(session('status'))
            <div
                class="rounded border px-4 py-3 {{ session('statusType') === 'success' ? 'text-success' : 'text-error' }}"
            >
                {!! Purify::clean(session('status')) !!}
            </div>
        @endif

        <h1 class="text-2xl font-bold">{{ __('misc_collmetadata.CREATE_COLL') }}</h1>

        <x-tabs :tabs="$tabs" :active="0">
            <div>
                <x-collections.collmetadata.form
                    :collection="$collection"
                    :collid="$collid"
                    :full-cat-arr="$fullCatArr"
                    :selected-categories="$selectedCategories"
                    :rights-terms="$rightsTerms"
                    :rights-state="$rightsState"
                    :show-gbif-publishing="$showGbifPublishing"
                    :action="route('collections.collmetadata.store')"
                    :heading="'Add New ' . __('misc_collmetadata.COL_INFO')"
                    submit-action="newCollection"
                    :submit-label="__('misc_collmetadata.CREATE_COLL_2')"
                />
            </div>
        </x-tabs>
    </div>
</x-margin-layout>
