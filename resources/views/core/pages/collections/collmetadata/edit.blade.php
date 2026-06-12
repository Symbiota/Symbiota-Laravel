@props([
    'collection',
    'collid',
    'fullCatArr' => [],
    'selectedCategories' => [],
    'resourceLinks' => [],
    'contacts' => [],
    'resourceJson' => '',
    'contactJson' => '',
    'address' => [],
    'institutionOptions' => [],
    'languageCodes' => ['en'],
    'rightsTerms' => [],
    'rightsState' => ['selected' => '', 'hasOrphan' => false],
    'showGbifPublishing' => false,
    'tabIndex' => 0,
])

@php
    $displayValue = static fn ($value) => is_string($value)
        ? Purify::clean($value)
        : $value;
    $collectionName = old('collectionName', $displayValue($collection['collectionname'] ?? ''));
    $institutionCode = old('institutionCode', $displayValue($collection['institutioncode'] ?? ''));
    $tabs = [__('misc_collmetadata.COL_META_EDIT'), __('misc_collmetadata.CONT_RES')];
    $breadcrumbs = [
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('misc_collmetadata.COL_PROFS'), 'href' => url('collections')],
        ['title' => $collectionName, 'href' => url('collections/' . $collid)],
        ['title' => __('misc_collmetadata.META_EDIT')],
    ];
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

        <h1 class="text-2xl font-bold">
            {{ __('misc_collmetadata.EDIT_METADATA') }}: {{ $collectionName }}
            @if($institutionCode !== '')
                ({{ $institutionCode }})
            @endif
        </h1>

        <x-tabs :tabs="$tabs" :active="$tabIndex">
            <div>
                <x-collections.collmetadata.form
                    :collection="$collection"
                    :collid="$collid"
                    :full-cat-arr="$fullCatArr"
                    :selected-categories="$selectedCategories"
                    :rights-terms="$rightsTerms"
                    :rights-state="$rightsState"
                    :show-gbif-publishing="$showGbifPublishing"
                    :action="route('collections.collmetadata.update', ['collid' => $collid])"
                    :heading="'Edit ' . __('misc_collmetadata.COL_INFO')"
                    submit-action="saveEdits"
                    :submit-label="__('misc_collmetadata.SAVE_EDITS')"
                />
            </div>

            <x-collections.collmetadata.edit-resources
                :collid="$collid"
                :collection="$collection"
                :resource-links="$resourceLinks"
                :contacts="$contacts"
                :resource-json="$resourceJson"
                :contact-json="$contactJson"
                :address="$address"
                :institution-options="$institutionOptions"
                :language-codes="$languageCodes"
            />
        </x-tabs>
    </div>
</x-margin-layout>
