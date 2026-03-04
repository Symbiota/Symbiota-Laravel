@php
    global $SERVER_ROOT;
    include_once(legacy_path('/classes/KeyDataManager.php'));

    $dataManager = new KeyDataManager();
    $attrsValues = Array();

    $langValue = 'English';
    $clValue = request('clid');
    $dynClid = array_key_exists('dynclid', $_REQUEST) ? filter_var($_REQUEST['dynclid'], FILTER_SANITIZE_NUMBER_INT) : 0;

    $taxonValue = request('taxon'); //array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']:'';
    $rv = array_key_exists('rv',$_REQUEST)?$_REQUEST['rv']:'';
    $pid = array_key_exists('pid', $_REQUEST) ? filter_var($_REQUEST['pid'], FILTER_SANITIZE_NUMBER_INT) : '';
    $langValue = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']:'';
    $sortBy = array_key_exists('sortby', $_REQUEST) ? filter_var($_REQUEST['sortby'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $displayCommon = array_key_exists('displaycommon', $_REQUEST) ? filter_var($_REQUEST['displaycommon'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $displayImages = array_key_exists('displayimages', $_REQUEST) ? filter_var($_REQUEST['displayimages'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $action = array_key_exists('submitbutton',$_REQUEST)?$_REQUEST['submitbutton']:'';
    if(!$action && array_key_exists('attr',$_REQUEST) && is_array($_REQUEST['attr'])){
        $attrsValues = $_REQUEST['attr'];	//Array of: cid + '-' + cs (ie: 2-3)
    }

    //if(!$langValue) $langValue = $DEFAULT_LANG;
    if($sortBy) $dataManager->setSortBy($sortBy);
    if($displayCommon) $dataManager->setDisplayCommon(1);
    if($displayImages) $dataManager->setDisplayImages(true);
    $dataManager->setLanguage($langValue);
    if($pid) $dataManager->setProject($pid);
    if($dynClid) $dataManager->setDynClid($dynClid);
    $clid = $dataManager->setClValue($clValue);
    if($taxonValue) $dataManager->setTaxonFilter($taxonValue);
    if($attrsValues) $dataManager->setAttrs($attrsValues);
    if($rv) $dataManager->setRelevanceValue($rv);

    $taxa = $dataManager->getTaxaArr();
    ksort($taxa);
    $clType =$dataManager->getClType();
    $chars = $dataManager->getCharArr();
    $count = $dataManager->getTaxaCount();
    $taxaValues = $dataManager->getTaxaFilterList();
    $filterList = [];
    foreach($dataManager->getTaxaFilterList() as $idx => $value) {
        $filterList[] = ['value' => trim($value), 'title' => $value, 'disabled' => false];
    }
@endphp

<x-margin-layout>
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => '/'],
        ['title' => 'Checklist Name' ],
        ['title' => 'Previous version of key' ],
        ['title' => 'New Version' ]
    ]" />
    <div class="relative block">
        <div><x-link class="text-2xl" href="{{ url('/checklists/' . $clid) }}">{{ $dataManager->getClName() }}</x-link></div>
        <div>
            <x-link href="{{ legacy_url('/ident/tools/matrixeditor.php?clid=' . $clid) }}">
                <x-icons.edit />
                Edit Character Matrix
            </x-link>
        </div>
        @if($count)
            <div>Species Count: {{ $count }}</div>
        @endif

        <form
            class="bg-base-100 p-4 border rounded-md border-base-300 absolute top-0 right-0 flex flex-col gap-2"
        >
            <h4 class="text-lg font-bold font-sans">Filter/Display Options</h4>
            <hr class="w-full"/>
            <x-select
                class="w-72"
                default="{{ array_search($taxonValue, $taxaValues) ?? 0 }}" id="taxon" label="Family/Genus Filter"
                :items="$filterList"
            />
            <x-select
                default="0" id="sortby" label="Sort By"
                :items="[
                     [
                        'title' => 'Family/ScientificName',
                        'value' => '0',
                        'disabled' => false
                     ],
                     [
                        'title' => 'ScientificName',
                        'value' => '1',
                        'disabled' => false
                     ]
                ]"
            />
            <x-checkbox id="displaycommon" label="Display Common Names"/>
            <x-checkbox id="displayimages" label="Display Images"/>
            <div class="flex gap-2">
                <x-button type="submit">Filter</x-button>
                <x-button>Reset</x-button>
            </div>
        </form>
    </div>

    {{-- Renders Plain Taxa list --}}
    @if($sortBy == 1)
        @foreach ($taxa as $taxaArr)
            @foreach ($taxaArr as $tid => $taxon)
            <div>
                <x-link href="../taxa/index.php?taxon={{ $tid }}&clid={{ $clType == 'static' ? $clid : '' }}" target="_blank">
                    <i>{{ $taxon['s'] }}</i>
                </x-link>
            </div>
            @endforeach
        @endforeach
    @else
    {{-- Renders taxa grouped by family --}}
    @foreach ($taxa as $family => $taxaArr)
        <div class="font-bold text-xl">{{ $family }}</div>
        <div class="pl-4">
        @foreach ($taxaArr as $tid => $taxon)
        <div>
            <x-link href="../taxa/index.php?taxon={{ $tid }}&clid={{ $clType == 'static' ? $clid : '' }}" target="_blank">
                <i>{{ $taxon['s'] }}</i>
            </x-link>
            @if($displayCommon)
            {{ !empty($taxon['v'])? ' - ' . $taxon['v']: ''}}
            @endif
        </div>
        @endforeach
        </div>
    @endforeach
    @endif
</x-layout>
