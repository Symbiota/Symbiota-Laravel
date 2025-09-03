@php
    global $SERVER_ROOT;
    include_once(legacy_path('/classes/KeyDataManager.php'));

    $dataManager = new KeyDataManager();
    $attrsValues = Array();

    $langValue = 'English';
    $clValue = request('clid');
    $dynClid = array_key_exists('dynclid', $_REQUEST) ? filter_var($_REQUEST['dynclid'], FILTER_SANITIZE_NUMBER_INT) : 0;

    $taxonValue = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']:'';
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

@endphp

<x-layout>
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => '/'],
        ['title' => 'Checklist Name' ],
        ['title' => 'Previous version of key' ],
        ['title' => 'New Version' ]
    ]" />

    <div>
        <div><x-link class="text-2xl" href="{{url('/checklists/' . $clid)}}">{{ $dataManager->getClName() }}</x-link></div>
        <div>Edit Character Matrix</div>
        <div>Species Count: ?</div>
    </div>

    @php
        $taxa = $dataManager->getTaxaArr();
        ksort($taxa);
        $clType =$dataManager->getClType();
        $chars = $dataManager->getCharArr();
    @endphp

    @foreach ($taxa as $family => $taxaArr)
        <div class="font-bold text-xl">{{ $family }}</div>
        <div class="pl-4">
        @foreach ($taxaArr as $tid => $taxon)

        <div>
            <x-link href="../taxa/index.php?taxon={{$tid}}&clid={{$clType == 'static' ? $clid : ''}}" target="_blank">
                <i>{{$taxon['s']}}</i>
            </x-link>
        </div>

        @endforeach
        </div>
    @endforeach
</x-layout>
