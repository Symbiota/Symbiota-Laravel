@php global $SERVER_ROOT;
include_once(legacy_path('/classes/OccurrenceAttributes.php'));
include_once(legacy_path('/classes/utilities/GeneralUtil.php'));

$REVIEW = 2;
$EDIT = 1;

$collid = request('collid');
$submitForm = request('submitform') ?? '';
$mode = request('mode') == $REVIEW? $REVIEW: $EDIT;
$traitID = request('traitid') ?? '';
$paneX = request('panex') ?? '575';
$paneY = request('paney') ?? '550';
$imgRes = request('imgres') ?? 'med';

//Sanitation
if (!is_numeric($collid)) $collid = 0;
if (!is_numeric($traitID)) $traitID = '';
if (!is_numeric($paneX)) $paneX = '';
if (!is_numeric($paneY)) $paneY = '';

$imgArr = [];

// TODO (Logan) resolve this question of whether review or not can happen by collEditors
$canReview = Gate::check('COLL_ADMIN', $collid);
if($mode === $REVIEW && !$canReview) {
    $mode = $EDIT;
}

$attrManager = new OccurrenceAttributes();
$attrManager->setCollid($collid);
$attrManager->setFilterAttributes(request()->all());
$taxonFilter = $attrManager->getFilterAttribute('taxonfilter');
$tidFilter = $attrManager->getFilterAttribute('tidfilter');
$reviewUid = $attrManager->getFilterAttribute('reviewuid');
$reviewDate = $attrManager->getFilterAttribute('reviewdate');
$reviewStatus = $attrManager->getFilterAttribute('reviewstatus');
$sourceFilter = $attrManager->getFilterAttribute('sourcefilter');
$localFilter = $attrManager->getFilterAttribute('localfilter');
$start = $attrManager->getFilterAttribute('start');

$statusStr = '';

if ($submitForm == 'Save and Next') {
    $attrManager->setOccid(request('targetoccid'));
    if (!$attrManager->addAttributes(request()->all(), request()->user()->uid)) {
        $statusStr = $attrManager->getErrorMessage();
    }
} elseif ($submitForm == 'Set Status and Save') {
    $attrManager->setOccid(request('targetoccid'));
    $attrManager->editAttributes(request()->all());
}

$imgArr = array();
$occid = 0;
$catNum = '';

if ($traitID) {
	$imgRetArr = array();
	if ($mode == $EDIT) {
		$imgRetArr = $attrManager->getImageUrls();
		$imgArr = current($imgRetArr);
	} elseif ($mode == $REVIEW) {
		$imgRetArr = $attrManager->getReviewUrls($traitID);
		if ($imgRetArr) $imgArr = current($imgRetArr);
	}
	if ($imgRetArr) {
		$catNum = $imgArr['catnum'];
		unset($imgArr['catnum']);
		$occid = key($imgRetArr);
		if ($occid) $attrManager->setOccid($occid);
	}
}

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

$editStatusItems = [
    item(5, __('includes_traittab.EXPERT_NEEDED')),
];

$sourceItems = itemize_flat($attrManager->getSourceControlledArr(), [
    item('', __('traitattr_occurattributes.ALL_SOURCE_TYPE'))
]);

$countryItems = itemize_flat($attrManager->getLocalFilterOptions(), [
    item('', __('traitattr_occurattributes.ALL_COUNTRIES_STATES'))
]);

$traitArr = $attrManager->getTraitArr($traitID, ($mode == 2 ? true : false));
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
        <x-button @click="mode = {{ $EDIT }}">{{ __('projects.EDIT') }}</x-button>
    </div>

    <div @cloak($mode === $REVIEW) x-show="mode !== {{ $REVIEW }}">
        <x-button  @click="mode = {{ $REVIEW }}">{{ __('traitattr_occurattributes.REVIEW') }}</x-button>
    </div>

    <div @cloak($mode !== $EDIT) x-show="mode === {{ $EDIT }}">
        <x-accordion :label="__('misc_sharedterms.FILTER')" :open="true" >
            <fieldset>
                <legend class="text-lg font-bold">
                    {{ __('misc_sharedterms.FILTER') }}
                </legend>
                <form id="filterform" class="flex flex-col gap-4" name="filterform" method="post">
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
                    <input type="hidden" name="mode" value="{{ $EDIT }}" />
                    <input type="hidden" name="submitform" value="Load Images">
                    <input id="panex1" name="panex" type="hidden" value="{{ $paneX }}" />
                    <input id="paney1" name="paney" type="hidden" value="{{ $paneY }}" />
                    <input id="imgres1" name="imgres" type="hidden" value="{{ $imgRes }}" />

                    <x-button>{{ __('traitattr_occurattributes.GET_IMAGES') }}</x-button>
                </form>
            </fieldset>
        </x-accordion>
    </div>

    <div @cloak($mode !== $REVIEW) x-show="mode === {{ $REVIEW }}">
        <x-accordion :label="__('traitattr_occurattributes.REVIEWER')" :open="true">
            <fieldset>
                <legend class="text-lg font-bold">
                    {{ __('traitattr_occurattributes.REVIEWER') }}
                </legend>
                <form id="reviewform" class="flex flex-col gap-4" name="reviewform" method="post">
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
                    <input type="hidden" name="mode" value="{{ $EDIT }}" />
                    <input type="hidden" name="submitform" value="Get Images">
                    <x-button>{{ __('traitattr_occurattributes.GET_IMAGES') }}</x-button>
                </form>
            </fieldset>
        </x-accordion>
    </div>

    <hr/>

    @if(!empty($imgArr))
    <div class="flex items-center gap-2">
        <x-radio class="m-0" name="resradio" default_value="high" :options="[
            [ 'value' => 'high', 'label' => __('traitattr_occurattributes.HIGH_RES') ],
            [ 'value' => 'med', 'label' => __('traitattr_occurattributes.MED_RES') ],
        ]" />
        <span class="flex-grow">
            <x-link href="{{ url('occurrence/' . $occid) }}">{{ $catNum ?? 'specimen details' }}</x-link>
        </span>
        <x-button type="button" @click="document.getElementById('filterform').submit()">{{ __('traitattr_occurattributes.SKIP')}} >></x-button>
    </div>

    <form method="post" class="flex gap-2" x-data="{ activeImg: 0}">
        @csrf
        @if(count($imgArr) > 1)
        <x-button type="button" @click="activeImg = activeImg + 1">Next</x-button>
        @endif

        @foreach ($imgArr as $image)
        <div class="mx-auto w-150 h-150 bg-base-300" x-show="activeImg === {{ $loop->index }}" @cloak(!$loop->first)>
            <img class="w-150 h-150" src="{{ $image['web'] ?? $image['lg'] }}" loading="lazy" />
        </div>
        @endforeach

        <div class="border border-base-300 flex-grow p-4 flex flex-col gap-4">
            <x-trait-form :traits="$traitArr" :traitId="$traitID" />
            <x-input id="notes" :label="__('projects.NOTES')" />
            <x-select class="w-full" id="status"
                :label="__('taxonomy_batchloader.STATUS')"
                :items="$editStatusItems"
            />

            <input type="hidden" name="taxonfilter" value="{{ $taxonFilter }}" />
            <input type="hidden" name="tidfilter" value="{{ $tidFilter }}" />
            <input type="hidden" name="reviewuid" value="{{ $reviewUid }}" />
            <input type="hidden" name="reviewdate" value="{{ $reviewDate }}" />
            <input type="hidden" name="reviewstatus" value="{{ $reviewStatus }}" />
            <input type="hidden" name="sourcefilter" value="{{ $sourceFilter }}" />
            <input type="hidden" name="localfilter" value="{{ $localFilter }}" />
            <input type="hidden" name="targetoccid" value="{{ $occid }}" />

            <div @cloak($mode !== $EDIT) x-show="mode === {{ $EDIT }}">
                <input type="hidden" name="submitform" value="Set Status and Save">
                <x-button>
                    {{ __('traitattr_occurattributes.SET_STATUS_SAVE') }}
                </x-button>
            </div>
            <div @cloak($mode !== $REVIEW) x-show="mode === {{ $REVIEW }}">
                <input type="hidden" name="submitform" value="Save and Next">
                <input type="hidden" name="mode" value="{{ $EDIT }}" />
                {{-- TODO (Logan) toggle logic fro when this is not disabled --}}
                <x-button :disabled="true">
                    {{ __('traitattr_occurattributes.SAVE_NEXT') }}
                </x-button>
            </div>
        </div>
    </form>

    @elseif($submitForm)
    <div class="font-bold">
        {{ __('traitattr_occurattributes.NO_IMAGES_MATCHING_CRITERIA') }}
    </div>
    @endif
</x-margin-layout>
