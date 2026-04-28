@props([
    'images',
    'collId' => request('collid'),
    'traitID',
    'attrManager',
    'mode' => 1,
])
@php

$REVIEW = 2;
$EDIT = 1;

$taxonFilter = $attrManager->getFilterAttribute('taxonfilter');
$tidFilter = $attrManager->getFilterAttribute('tidfilter');
$reviewUid = $attrManager->getFilterAttribute('reviewuid');
$reviewDate = $attrManager->getFilterAttribute('reviewdate');
$reviewStatus = $attrManager->getFilterAttribute('reviewstatus');
$sourceFilter = $attrManager->getFilterAttribute('sourcefilter');
$localFilter = $attrManager->getFilterAttribute('localfilter');

$traitArr = $attrManager->getTraitArr($traitID, ($mode == 2 ? true : false));

$editStatusItems = [
    item(5, __('includes_traittab.EXPERT_NEEDED')),
];
@endphp

@if(!empty($images))
<form id="trait-image-form" method="post" class="flex gap-2" x-data="{ activeImg: 0, hasTrait: false}">
    @csrf
    @if(count($images) > 1)
    <x-button type="button" @click="activeImg = activeImg + 1">Next</x-button>
    @endif

    @foreach ($images as $image)
    <div class="mx-auto w-150 h-150 bg-base-300" x-show="activeImg === {{ $loop->index }}" @cloak(!$loop->first)>
        <img class="w-150 h-150" src="{{ $image['web'] ?? $image['lg'] }}" loading="lazy" />
    </div>
    @endforeach

    <div class="border border-base-300 flex-grow p-4 flex flex-col gap-4">
        <x-traits.form
            :traits="$traitArr"
            :traitId="$traitID"
            @change="hasTrait=event?.target?.name?.includes('traitid')"
        />
        <x-input id="notes" :label="__('projects.NOTES')" />
        <x-select class="w-full" id="status"
            :label="__('taxonomy_batchloader.STATUS')"
            :items="$editStatusItems"
        />

        <input type="hidden" name="taxonfilter" value="{{ $taxonFilter }}" />
        <input type="hidden" name="tidfilter" value="{{ $tidFilter }}" />
        <input type="hidden" name="localfilter" value="{{ $localFilter }}" />
        <input type="hidden" name="traitid" value="{{ $traitID }}" />

        <input type="hidden" name="reviewuid" value="{{ $reviewUid }}" />
        <input type="hidden" name="reviewdate" value="{{ $reviewDate }}" />
        <input type="hidden" name="reviewstatus" value="{{ $reviewStatus }}" />
        <input type="hidden" name="sourcefilter" value="{{ $sourceFilter }}" />
        <input type="hidden" name="targetoccid" value="{{ $occid }}" />

        <div @cloak($mode !== $REVIEW) x-show="mode === {{ $REVIEW }}">
            <input type="hidden" name="submitform" value="Set Status and Save">
            <input type="hidden" name="mode" value="{{ $REVIEW }}" />
            <x-button
                x-bind:disabled="!hasTrait"
                hx-post="{{ url('collections/' . $collId . '/review') }}"
                hx-target="#trait-image-form"
                hx-swap="outerHTML"
            >
                {{ __('traitattr_occurattributes.SET_STATUS_SAVE') }}
            </x-button>
        </div>
        <div @cloak($mode !== $EDIT) x-show="mode === {{ $EDIT }}">
            <input type="hidden" name="submitform" value="Save and Next">
            <input type="hidden" name="mode" value="{{ $EDIT }}" />
            <x-button
                x-bind:disabled="!hasTrait"
                hx-post="{{ url('collections/' . $collId . '/edit') }}"
                hx-target="#trait-image-form"
                hx-swap="outerHTML"
            >
                {{ __('traitattr_occurattributes.SAVE_NEXT') }}
            </x-button>
        </div>
    </div>
</form>
@elseif(request('submitform'))
<div id="trait-image-form" class="font-bold">
    {{ __('traitattr_occurattributes.NO_IMAGES_MATCHING_CRITERIA') }}
</div>
@else
<div id="trait-image-form" class="font-bold">
</div>
@endif
