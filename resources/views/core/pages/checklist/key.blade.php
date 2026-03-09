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

    $isKeyEditor = Gate::check('KEY_EDITOR');
@endphp

<x-margin-layout>
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => '/'],
        ['title' => $dataManager->getClName(), 'href' => url('/checklists/' . $clid) ],
        ['title' => 'Identification Key' ]
    ]" />
    <div class="relative block">
        <div class="text-4xl font-bold" >{{ $dataManager->getClName() }}</div>
    </div>
    <x-accordion label="Filter/Display Options">
        <form
            class="bg-base-100 flex flex-col gap-4"
        >
            <div class="flex items-center gap-2">
            <x-select
                class="min-w-72 w-full"
                defaultValue="{{ $taxonValue ?? 'All Species' }}"
                id="taxon"
                label="Family/Genus Filter"
                :items="$filterList"
            />
            <x-select
                class="min-w-72 w-full"
                default="0" id="sortby" label="Sort By"
                defaultValue="{{ $sortBy }}"
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
            </div>

            <div class="flex items-center gap-2">
            <x-checkbox id="displaycommon" label="Display Common Names" :checked="$displayCommon"/>
            <x-checkbox id="displayimages" label="Display Images" :checked="$displayImages"/>
            </div>
            <div class="flex gap-2">
                <x-button type="submit">Filter</x-button>
                <x-button href="{{ url()->current() }}" hx-boost="true">Reset</x-button>
            </div>
        </form>
    </x-accordion>

    <div>
        @if($count)
                <div class="text-xl"><span class="font-bold">Species Count:</span> {{ $count }}</div>
        @endif
        @if($isKeyEditor || true)
        <x-link href="{{ legacy_url('/ident/tools/matrixeditor.php?clid=' . $clid) }}">
            <x-icons.edit />
            Edit Character Matrix
        </x-link>
        @endif
    </div>


    {{--rRenders Plain Taxa list --}}
    @if($displayImages)
        <div id="photo-gallery" class="flex flex-wrap flex-row gap-3 mt-4">
        @foreach ($taxa as $taxaArr)
            @foreach ($taxaArr as $tid => $taxon)
            <div class="flex flex-col bg-base-200 w-48">
                <a class="flex" target="_blank" href="{{ url('taxon/' . $tid) }}">
                    <img class="h-72 w-48 object-cover" loading="lazy" src="{{ $taxon['i'] }}" />
                </a>
                <div
                    class="text-neutral-content w-full p-2 bg-neutral grow-1 text-sm">
                    <x-link class="text-neutral-content hover:text-neutral-content/50" href="{{ url('taxon/' . $tid) }}">
                        {{ $taxon['s'] }}
                    </x-link>
                    @if($displayCommon)
                    {{ !empty($taxon['v'])? ' - ' . $taxon['v']: ''}}
                    @endif
                </div>
            </div>
            @endforeach
        @endforeach
        </div>
    @elseif($sortBy == 1)
        @foreach ($taxa as $taxaArr)
            <div>
            @foreach ($taxaArr as $tid => $taxon)
            <div>
                @if($isKeyEditor)
                <x-link href="{{ legacy_url('ident/tools/editor.php?tid=' . $tid) }}">
                    <x-icons.edit/>
                </x-link>
                @endif
                <x-link href="{{ url('/taxon/' . $tid) }}" target="_blank">
                    <i>{{ $taxon['s'] }}</i>
                </x-link>
                @if($displayCommon)
                {{ !empty($taxon['v'])? ' - ' . $taxon['v']: ''}}
                @endif
            </div>
            @endforeach
            </div>
        @endforeach
    @else
    {{-- Renders taxa grouped by family --}}
    @foreach ($taxa as $family => $taxaArr)
    <div>
        <div class="font-bold text-xl">{{ $family }}</div>
        <div class="pl-4">
        @foreach ($taxaArr as $tid => $taxon)
        <div>
            @if($isKeyEditor)
            <x-link href="{{ legacy_url('ident/tools/editor.php?tid=' . $tid) }}">
                <x-icons.edit/>
            </x-link>
            @endif
            <x-link href="{{ url('/taxon/' . $tid) }}" target="_blank">
                <i>{{ $taxon['s'] }}</i>
            </x-link>
            @if($displayCommon)
            {{ !empty($taxon['v'])? ' - ' . $taxon['v']: ''}}
            @endif
        </div>
        @endforeach
        </div>
    </div>
    @endforeach
    @endif
</x-layout>
