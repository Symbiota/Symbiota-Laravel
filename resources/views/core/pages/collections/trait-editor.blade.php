@php global $SERVER_ROOT;
include_once(legacy_path('/classes/OccurrenceAttributes.php'));
include_once(legacy_path('/classes/utilities/GeneralUtil.php'));

$collid = request('collid');
$submitForm = request('submitform') ?? '';
$mode = request('mode') ?? 1;
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

$attrManager = new OccurrenceAttributes();
$attrManager->setCollid($collid);
$attrManager->setFilterAttributes($_POST);
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
    # $attrManager->setOccid($_POST['targetoccid']);
    # if (!$attrManager->addAttributes($_POST, $SYMB_UID)) {
    #     $statusStr = $attrManager->getErrorMessage();
    # }
} elseif ($submitForm == 'Set Status and Save') {
    $attrManager->setOccid(request('targetoccid'));
    $attrManager->editAttributes(request()->all());
}

$imgArr = array();
$occid = 0;
$catNum = '';
if ($traitID) {
	$imgRetArr = array();
	if ($mode == 1) {
		$imgRetArr = $attrManager->getImageUrls();
		$imgArr = current($imgRetArr);
	} elseif ($mode == 2) {
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

$traitItems = itemize($attrManager->getTraitNames(), [
    item('', __('editor_editreviewer.ALL_EDITORS'))
]);

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

@endphp

<x-margin-layout x-data="{ mode: 0 }">
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

	<fieldset x-cloak x-show="mode === 0">
		<legend class="text-lg font-bold">
            {{ __('traitattr_occurattributes.REVIEWER') }}
        </legend>
		<form id="reviewform" class="flex flex-col gap-4" name="reviewform" method="post">
            @csrf
            <div class="flex flex-wrap gap-2">
                <x-select class="flex-grow" id="traitid" :items="$traitItems"
                    :select_text="__('traitattr_occurattributes.SELECT_TRAIT_REQ')"
                />

                <x-select class="w-auto flex-grow" id="reviewuid" default="0" :items="$editorItems"/>
                <x-select class="w-auto flex-grow" id="reviewdate" default="0" :items="$dateItems"/>
                <x-select class="w-auto flex-grow" id="reviewstatus" default="0" :items="$reviewStatusItems"/>
                <x-select class="w-auto flex-grow" id="sourcefilter" default="0" :items="$sourceItems"/>
                <x-select class="w-auto flex-grow" id="localfilter" default="0" :items="$countryItems"/>
                </div>
            <x-taxa-search />
            <input type="hidden" name="submitForm" value="Get Images">
            <x-button>{{ __('traitattr_occurattributes.GET_IMAGES') }}</x-button>
        </form>
    </fieldset>

    <hr/>

	<fieldset x-cloak x-show="mode === 1">
		<legend class="font-bold">
            {{ __('traitattr_occurattributes.FILTER') }}
        </legend>
		<form id="filterform" name="filterform" method="post">

        </form>
    </fieldset>

    @if(!empty($imgArr))
    <x-radio name="resradio" :options="[
        [ 'value' => 'high', 'label' => __('traitattr_occurattributes.HIGH_RES') ],
        [ 'value' => 'med', 'label' => __('traitattr_occurattributes.MED_RES') ],
    ]" />

    @foreach ($imgArr as $image)
        <div class="w-50 h-50 bg-base-300">
            <img class="w-50 h-50" src="{{ $image['web'] ?? $image['lg'] }}" loading="lazy" />
        </div>
    @endforeach

    @else
    <div class="font-bold">
        {{ __('traitattr_occurattributes.NO_IMAGES_MATCHING_CRITERIA') }}
    </div>
    @endif
</x-margin-layout>
