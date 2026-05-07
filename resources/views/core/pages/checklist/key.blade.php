@php
    global $SERVER_ROOT;
    include_once(legacy_path('/classes/KeyDataManager.php'));

    $dataManager = new KeyDataManager();
    $attrsValues = Array();

    $langValue = 'English';
    $clValue = request('clid');
    $dynClid = array_key_exists('dynclid', $_REQUEST) ? filter_var($_REQUEST['dynclid'], FILTER_SANITIZE_NUMBER_INT) : 0;

    $taxonValue = request('taxon'); //array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']:'';
    $rv = request('rv') ?? '';
    $pid = request('pid')? filter_var(request('pid'), FILTER_SANITIZE_NUMBER_INT) : '';
    $langValue = request('lang') ?? '';
    $sortBy = request('sortby')? filter_var(request('sortby'), FILTER_SANITIZE_NUMBER_INT) : 0;
    $displayCommon = request('displaycommon') ? filter_var(request('displaycommon'), FILTER_SANITIZE_NUMBER_INT) : 0;
    $displayImages = request('displayimages') ? filter_var(request('displayimages'), FILTER_SANITIZE_NUMBER_INT) : 0;
    $action = request('submitbutton') ?? '';

    if(!$action && is_array(request('attr'))) {
        $attrsValues = request('attr');	//Array of: cid + '-' + cs (ie: 2-3)
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
        $filterList[] = item(trim($value), $value);
    }

    $isKeyEditor = Gate::check('KEY_EDITOR');
@endphp

<x-margin-layout>
    <x-breadcrumbs
        :items="[
        ['title' => __('header.H_HOME'), 'href' => '/'],
        ['title' => $dataManager->getClName(), 'href' => url('/checklists/' . $clid) ],
        ['title' => __('ident_key.NEW_ID_KEY')]
    ]"
    />
    <div class="relative block">
        <x-page-title>{{ $dataManager->getClName() }}</x-page-title>
    </div>
    <x-accordion :label="__('ident_key.FILTER_OPTIONS')">
        <form class="bg-base-100 flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <x-select
                    class="w-full min-w-72"
                    defaultValue="{{ $taxonValue ?? 'All Species' }}"
                    id="taxon"
                    :label="__('ident_key.TAXON_SEARCH')"
                    :items="$filterList"
                />
                <x-select
                    class="w-full min-w-72"
                    id="sortby"
                    :label="__('ident_key.SORT')"
                    default="0"
                    defaultValue="{{ $sortBy }}"
                    :items="[
                    item('0', __('ident_key.SORT_SCINAME_FAMILY')),
                    item('1', __('ident_key.SORT_SCINAME'))
                ]"
                />
            </div>

            <div class="flex items-center gap-2">
                <x-checkbox id="displaycommon" :label="__('ident_key.DISPLAY_COMMON')" :checked="$displayCommon" />
                <x-checkbox id="displayimages" :label="__('ident_key.DISPLAY_IMAGES')" :checked="$displayImages" />
            </div>
            <div class="flex gap-2">
                <x-button type="submit"> {{ __('checklists_checklist.BUILD_LIST') }} </x-button>
                <x-button href="{{ url()->current() }}" hx-boost="true"> {{ __('map.RESET') }} </x-button>
            </div>
        </form>
    </x-accordion>

    <div>
        @if($count)
            <x-text-label class="text-xl" :label="__('ident_key.SPECCOUNT')">{{ $count }}</x-text-label>
        @endif
        @if($isKeyEditor || true)
            <x-link href="{{ legacy_url('/ident/tools/matrixeditor.php?clid=' . $clid) }}">
                <x-icons.edit />
                {{ __('ident_key.EDIT_CHAR_MATRIX') }}
            </x-link>
        @endif
    </div>

    {{-- Renders Plain Taxa list --}}
    @if($displayImages)
        <div id="photo-gallery" class="mt-4 flex flex-row flex-wrap gap-3">
            @foreach($taxa as $taxaArr)
                @foreach($taxaArr as $tid => $taxon)
                    <div class="bg-base-200 flex w-48 flex-col">
                        <a class="flex" target="_blank" href="{{ url('taxon/' . $tid) }}">
                            <img class="h-72 w-48 object-cover" loading="lazy" src="{{ $taxon['i'] }}" />
                        </a>
                        <div class="text-neutral-content bg-neutral w-full grow-1 p-2 text-sm">
                            <x-link
                                class="text-neutral-content hover:text-neutral-content/50"
                                href="{{ url('taxon/' . $tid) }}"
                            >
                                {{ $taxon['s'] }}
                            </x-link>
                            @if($displayCommon)
                                {{ !empty($taxon['v'])? ' - ' . $taxon['v']: '' }}
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @elseif($sortBy == 1)
        @foreach($taxa as $taxaArr)
            <div>
                @foreach($taxaArr as $tid => $taxon)
                    <div>
                        @if($isKeyEditor)
                            <x-link href="{{ legacy_url('ident/tools/editor.php?tid=' . $tid) }}">
                                <x-icons.edit />
                            </x-link>
                        @endif
                        <x-link href="{{ url('/taxon/' . $tid) }}" target="_blank">
                            <i>{{ $taxon['s'] }}</i>
                        </x-link>
                        @if($displayCommon)
                            {{ !empty($taxon['v'])? ' - ' . $taxon['v']: '' }}
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        {{-- Renders taxa grouped by family --}}
        @foreach($taxa as $family => $taxaArr)
            <div>
                <div class="text-xl font-bold">{{ $family }}</div>
                <div class="pl-4">
                    @foreach($taxaArr as $tid => $taxon)
                        <div>
                            @if($isKeyEditor)
                                <x-link href="{{ legacy_url('ident/tools/editor.php?tid=' . $tid) }}">
                                    <x-icons.edit />
                                </x-link>
                            @endif
                            <x-link href="{{ url('/taxon/' . $tid) }}" target="_blank">
                                <i>{{ $taxon['s'] }}</i>
                            </x-link>
                            @if($displayCommon)
                                {{ !empty($taxon['v'])? ' - ' . $taxon['v']: '' }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</x-margin-layout>
