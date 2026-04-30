@props([
    'images',
    'collId' => request('collid'),
    'traitID',
    'attrManager',
    'mode' => 1,
    'imgRes' => request('imgres') ?? 'med',
    'errors' => null
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
$start = $attrManager->getFilterAttribute('start');
$next = $start + 1;

$traitArr = $attrManager->getTraitArr($traitID, ($mode == 2 ? true : false));

$editStatusItems = $mode === $REVIEW? [
    item(0, __('includes_traittab.NOT_REVIEWED')),
    item(5, __('includes_traittab.EXPERT_NEEDED')),
    item(10, __('misc_commentlist.REVIEWED')),
]: [
    item(5, __('includes_traittab.EXPERT_NEEDED')),
];
@endphp
<x-errors :errors="$errors" />
<div
    id="trait-image-form"
    class="flex flex-col gap-4"
    x-data="{ imgRes: '{{ $imgRes }}' }"
    x-show="mode == {{ $mode }}"
>
    @if(!empty($images))
        <div class="flex items-center gap-2">
            <x-radio
                class="m-0"
                name="resradio"
                :default_value="$imgRes"
                :options="[
            [ 'value' => 'high', 'label' => __('traitattr_occurattributes.HIGH_RES') ],
            [ 'value' => 'med', 'label' => __('traitattr_occurattributes.MED_RES') ],
        ]"
            />
            <span class="flex-grow">
                <x-link href="{{ url('occurrence/' . $occid) }}">{{ $catNum ?? 'specimen details' }}</x-link>
            </span>

            @if($mode === $EDIT)
                <x-button
                    hx-post="{{ url()->current() }}"
                    hx-target="#trait-image-form"
                    hx-include="#filterform"
                    type="button"
                >
                    {{ __('traitattr_occurattributes.SKIP') }}
                </x-button>
            @elseif($mode === $REVIEW && $traitID)
                @php
        $rCnt = $attrManager->getReviewCount($traitID);
        if($next >= $rCnt) $next = 0;
        @endphp
                <span>{{ ($start + 1). '/' . $rCnt }}</span>
                <x-button
                    hx-post="{{ url()->current() }}"
                    hx-target="#trait-image-form"
                    hx-include="#reviewform, #review_start"
                    type="button"
                >
                    {{ __('traitattr_occurattributes.NEXT_RECORD') }}
                </x-button>
            @endif
        </div>
        <form method="post" class="flex gap-2" x-data="{ activeImg: 0, hasTrait: false }">
            @csrf
            @if(count($images) > 1)
                <x-button type="button" @click="activeImg = activeImg + 1">Next</x-button>
            @endif

            @foreach($images as $image)
                <div
                    class="bg-base-300 mx-auto h-150 w-150"
                    x-show="activeImg === {{ $loop->index }}"
                    @cloak(!$loop->first)
                >
                    @if(isset($image['web']) && isset($img['lg']))
                        <img
                            class="h-150 w-150"
                            x-bind:src="imgRes == 'med'? '{{ $image['web'] }}': '{{ $image['lg'] }}'"
                            loading="lazy"
                        />
                    @else
                        <img class="h-150 w-150" src="{{ $image['web'] ?? $image['lg'] }}" loading="lazy" />
                    @endif
                </div>
            @endforeach

            <div class="border-base-300 flex flex-grow flex-col gap-4 border p-4">
                <x-traits.form
                    :traits="$traitArr"
                    :traitId="$traitID"
                    @change="hasTrait = event?.target?.name?.includes('traitid')"
                />

                @php
            $notes = '';
            foreach ($traitArr[$traitID]['states'] as $stArr) {
                if (isset($stArr['notes']) && $stArr['notes']) $notes = $stArr['notes'];
            }
            @endphp
                <x-input id="notes" :label="__('projects.NOTES')" :value="$notes" />
                <x-select
                    class="w-full"
                    id="status"
                    name="setstatus"
                    :default="$mode == $REVIEW? 2: null"
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

                @if($mode === $EDIT)
                    <div>
                        <input type="hidden" name="submitform" value="Save and Next" />
                        <input type="hidden" name="mode" value="{{ $EDIT }}" />
                        <x-button
                            x-bind:disabled="!hasTrait"
                            hx-patch="{{ url('collections/' . $collId . '/traits/edit') }}"
                            hx-target="#trait-image-form"
                            hx-swap="outerHTML"
                        >
                            {{ __('traitattr_occurattributes.SAVE_NEXT') }}
                        </x-button>
                    </div>
                @elseif($mode === $REVIEW)
                    <div>
                        <input type="hidden" name="submitform" value="Set Status and Save" />
                        <input type="hidden" name="mode" value="{{ $REVIEW }}" />
                        <input id="review_start" name="start" type="hidden" value="{{ $next }}" />

                        <x-button
                            x-bind:disabled="!hasTrait"
                            hx-patch="{{ url('collections/' . $collId . '/traits/edit') }}"
                            hx-target="#trait-image-form"
                            hx-swap="outerHTML"
                        >
                            {{ __('traitattr_occurattributes.SET_STATUS_SAVE') }}
                        </x-button>
                    </div>
                @endif
            </div>
        </form>
    @elseif(request('submitform'))
        <div class="font-bold">{{ __('traitattr_occurattributes.NO_IMAGES_MATCHING_CRITERIA') }}</div>
    @endif
</div>
