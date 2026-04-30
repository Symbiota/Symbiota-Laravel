@php $REVIEW = 2; $EDIT = 1; @endphp
@props([
    'attrManager',
    'traitID' => '',
    'images' => [],
    'occid' => 0,
    'catNum' => '',
    'mode' => request('mode') == $REVIEW? $REVIEW: $EDIT,
])

@php global $SERVER_ROOT;
include_once(legacy_path('/classes/utilities/GeneralUtil.php'));

$collId = request('collid');
$submitForm = request('submitform') ?? '';
$traitID = request('traitid') ?? '';
$paneX = request('panex') ?? '575';
$paneY = request('paney') ?? '550';
$imgRes = request('imgres') ?? 'med';

$canReview = Gate::check('COLL_ADMIN', $collId);

$traitItems = itemize($attrManager->getTraitNames());

$editorItems = itemize($attrManager->getEditorArr(), [
    item('', __('editor_editreviewer.ALL_EDITORS'))
]);

$dateItems = itemize_flat($attrManager->getEditDates(), [
    item('', __('traitattr_occurattributes.ALL_DATES'))
]);

$reviewStatusItems = [
    item(0, __('includes_traittab.NOT_REVIEWED')),
    item(5, __('includes_traittab.EXPERT_NEEDED')),
    item(10, __('misc_commentlist.REVIEWED')),
];

$sourceItems = itemize_flat($attrManager->getSourceControlledArr(), [
    item('', __('traitattr_occurattributes.ALL_SOURCE_TYPE'))
]);

$countryItems = itemize_flat($attrManager->getLocalFilterOptions(), [
    item('', __('traitattr_occurattributes.ALL_COUNTRIES_STATES'))
]);

@endphp

<x-margin-layout x-data="{ mode: {{ $mode }} }">
    <x-breadcrumbs :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        ['title' => __('traitattr_attributemining.COLLECTION_MANAGEMENT'), 'href' => url('collections/' . request('collid')) ],
        ['title' => __('traitattr_occurattributes.ATTRIBUTE_EDITOR') ]
    ]" />
    <x-page-title>
        {{ __('traitattr_occurattributes.OCC_ATTRIBUTE_BATCH_EDIT') }}
    </x-page-title>

    <p class="font-bold">
        {{ __('traitattr_occurattributes.SELECT_UNSCORED_IMAGE_TRAIT') }}
    </p>

    <p>
        {{ __('traitattr_occurattributes.TRAIT_TOOL_EXPLAIN') }}
        <x-link href="https://tools.gbif.org/dwca-validator/extension.do?id=http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact" target="_blank">
        {{ __('traitattr_attributemining.MEASUREMENT_OR_FACT') }}
        </x-link>
        {{ __('traitattr_occurattributes.DWC_EXTEN_FILE') }}
    </p>

    <hr/>

    <div @cloak($mode === $EDIT) x-show="mode !== {{ $EDIT }}">
        <x-button
            @click="mode = {{ $EDIT }}"
        >
            {{ __('projects.EDIT') }}
        </x-button>
    </div>

    @if($canReview)
    <div @cloak($mode === $REVIEW) x-show="mode !== {{ $REVIEW }}">
        <x-button
            @click="mode = {{ $REVIEW }}"
        >
            {{ __('traitattr_occurattributes.REVIEW') }}
        </x-button>
    </div>
    @endif

    <div @cloak($mode !== $EDIT) x-show="mode === {{ $EDIT }}">
        <x-accordion :label="__('misc_sharedterms.FILTER')" :open="true" >
            <fieldset>
                <legend class="text-lg font-bold">
                    {{ __('misc_sharedterms.FILTER') }}
                </legend>
                <form
                    hx-post="{{ url()->current() }}"
                    hx-target="#trait-image-form"
                    hx-swap="outerHTML"
                    id="filterform"
                    class="flex flex-col gap-4"
                    name="filterform"
                    method="post"
                >
                    @csrf
                    <x-select class="w-full flex-grow" id="traitid"
                        :defaultValue="$traitID"
                        :items="$traitItems"
                        :select_text="__('traitattr_occurattributes.SELECT_TRAIT_REQ')"
                    />
                    <div class="flex flex-wrap gap-4">
                        <x-select class="w-auto flex-grow" id="localfilter" default="0" :items="$countryItems"/>
                    </div>
                    <x-taxa-search />
                    <input type="hidden" name="mode" x-bind:value="mode" value="{{ $mode }}" />
                    <input type="hidden" name="submitform" value="Load Images">
                    <input id="panex1" name="panex" type="hidden" value="{{ $paneX }}" />
                    <input id="paney1" name="paney" type="hidden" value="{{ $paneY }}" />
                    <input id="imgres1" name="imgres" type="hidden" value="{{ $imgRes }}" />

                    <x-button>{{ __('traitattr_occurattributes.GET_IMAGES') }}</x-button>
                </form>
            </fieldset>
        </x-accordion>
    </div>

    @if($canReview)
    <div @cloak($mode !== $REVIEW) x-show="mode === {{ $REVIEW }}">
        <x-accordion :label="__('traitattr_occurattributes.REVIEWER')" :open="true">
            <fieldset>
                <legend class="text-lg font-bold">
                    {{ __('traitattr_occurattributes.REVIEWER') }}
                </legend>
                <form
                    hx-post="{{ url()->current() }}"
                    hx-target="#trait-image-form"
                    hx-swap="outerHTML"
                    id="reviewform"
                    class="flex flex-col gap-4"
                    name="reviewform"
                    method="post"
                >
                    @csrf

                    <x-select class="w-full flex-grow" default="0" id="traitid" :items="$traitItems"
                        :select_text="__('traitattr_occurattributes.SELECT_TRAIT_REQ')"
                    />
                    <div class="flex flex-wrap gap-4">
                        <x-select class="min-w-60 w-auto flex-grow" id="reviewuid" default="0" :items="$editorItems"/>
                        <x-select class="w-auto flex-grow" id="reviewdate" default="0" :items="$dateItems"/>
                        <x-select class="w-auto flex-grow" id="reviewstatus" default="0" :items="$reviewStatusItems"/>
                        <x-select class="w-auto flex-grow" id="sourcefilter" default="0" :items="$sourceItems"/>
                        <x-select class="w-auto flex-grow" id="localfilter" default="0" :items="$countryItems"/>
                        </div>
                    <x-taxa-search />
                    <input type="hidden" name="mode" x-bind:value="mode" value="{{ $mode }}" />
                    <input type="hidden" name="submitform" value="Get Images">
                    <x-button>{{ __('traitattr_occurattributes.GET_IMAGES') }}</x-button>
                </form>
            </fieldset>
        </x-accordion>
    </div>
    @endif

    <hr/>

    <x-traits.image-form
        :images="$images"
        :collId="$collId"
        :traitID="$traitID"
        :attrManager="$attrManager"
        :mode="$mode"
    />
</x-margin-layout>
