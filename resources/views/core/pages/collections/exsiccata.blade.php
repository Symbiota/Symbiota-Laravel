@php
    $isOccurrencePage = $isOccurrencePage ?? false;
    $isDetailPage = $isDetailPage ?? false;
    $isEditor = $isEditor ?? false;
    $searchTerm = $searchTerm ?? '';
    $collId = $collId ?? 0;
    $specimenOnly = $specimenOnly ?? 0;
    $imagesOnly = $imagesOnly ?? 0;
    $sortBy = $sortBy ?? 0;
    $collections = $collections ?? [];
    $titles = $titles ?? [];
    $numbers = $numbers ?? [];
    $occurrences = $occurrences ?? [];
    $title = $title ?? [];
    $number = $number ?? [];
    $ometid = $ometid ?? null;
    $omenid = $omenid ?? null;
    $selectLookupArr = $selectLookupArr ?? [];
    $unableLocateRecord = $unableLocateRecord ?? false;
    $exsTargetItems = itemize($selectLookupArr);
    $collectionItems = itemize($collections);
    $occAddCollectionItems = itemize($collections);
    $occAddCollectionItems[] = item('occid', __('exsiccati.SYMB_PK_OCCID'));

    $query = array_filter([
        'searchterm' => $searchTerm,
        'specimenonly' => $specimenOnly,
        'imagesonly' => $imagesOnly,
        'collid' => $collId,
        'sortby' => $sortBy,
    ], static fn ($value) => $value !== null && $value !== '' && $value !== 0 && $value !== '0');
    $queryString = $query ? '?' . http_build_query($query) : '';
    $postAction = route('exsiccata.store');
    $currentOmetid = $ometid ?? ($title['ometid'] ?? null);

    // Breadcrumbs building
    $breadcrumbs = [
        ['title' => __('exsiccati.HOME'), 'href' => url('/')],
    ];

    if (! $isDetailPage) {
        $breadcrumbs[] = ['title' => __('exsiccati.EXS_INDEX')];
    } else {
        $breadcrumbs[] = [
            'title' => __('exsiccati.RET_MAIN_EXS_INDEX'),
            'href' => route('exsiccata.index') . $queryString,
        ];
        if (! $unableLocateRecord) {
            $breadcrumbs[] = [
                'title' => Purify::clean($title['title']  ?? ''),
                'href' => route('exsiccata.index') . '?' . http_build_query(array_filter([
                    'ometid' => $currentOmetid,
                    'searchterm' => $searchTerm,
                    'specimenonly' => $specimenOnly,
                    'imagesonly' => $imagesOnly,
                    'collid' => $collId,
                    'sortby' => $sortBy,
                ], static fn ($value) => $value !== null && $value !== '' && $value !== 0 && $value !== '0')),
            ];

            if ($isOccurrencePage)
                $breadcrumbs[] = ['title' => '#' . ($number['exsnumber'] ?? '')];
        }
    }
@endphp


<script>
    function verifyExsAddForm(f) {
        if (f.title.value.trim() === '') {
            alert("{{ __('exsiccati.TITLE_CANNOT_EMPTY') }}");
            return false;
        }
        return true;
    }

    function verifyExsMergeForm(f) {
        if (!f.targetometid.value) {
            alert("{{ __('exsiccati.SEL_TARGET_EXS') }}");
            return false;
        }
        return window.confirm('{{ __('exsiccati.SURE_MERGE_EXS') }}');
    }

    function verifyNumAddForm(f) {
        if (f.exsnumber.value.trim() === '') {
            alert("{{ __('exsiccati.NUM_CANNOT_EMPTY') }}");
            return false;
        }
        return true;
    }

    function verifyOccAddForm(f) {
        if (!f.occaddcollid.value) {
            alert("{{ __('exsiccati.PLS_SEL_COLL') }}");
            return false;
        }
        if (f.identifier.value.trim() === '' && (f.recordedby.value.trim() === '' || f.recordnumber.value.trim() === '')) {
            alert("{{ __('exsiccati.CATNUM_COLL_CANNOT_EMPTY') }}");
            return false;
        }
        if (f.ranking.value.trim() !== '' && Number.isNaN(Number(f.ranking.value))) {
            alert("{{ __('exsiccati.RANKING_MUST_NUM') }}");
            return false;
        }
        return true;
    }

    function verifyOccTransferForm(f) {
        if (!f.targetometid.value) {
            alert("{{ __('exsiccati.PLS_SEL_EXS_TITLE') }}");
            return false;
        }
        if (!f.targetexsnumber.value.trim()) {
            alert("{{ __('exsiccati.PLS_SEL_EXS_NUM') }}");
            return false;
        }
        return true;
    }
</script>

<x-margin-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>

    <div class="exsiccata-page space-y-6">
        @if(session('status'))
            <div class="rounded border px-4 py-3 {{ session('statusType') === 'success' ? ' text-success' : 'text-error' }}">
                {{ session('status') }}
            </div>
        @endif

        @if($unableLocateRecord)
            <div class="text-lg font-semibold">
                {{ __('exsiccati.UNABLE_LOCATE_REC') }}
            </div>
        {{--Index Page--}}
        @elseif(!$isDetailPage)
            <div class="flex items-start gap-6">
                <div x-data="{ toggleName: false }" class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-4">
                        <h1 class="text-2xl font-bold">{{ __('exsiccati.EXS') }}</h1>

                        @if($isEditor)
                            <button
                                type="button"
                                @click="toggleName = !toggleName"
                                class="cursor-pointer px-3 py-2 font-bold leading-none"
                            >
                                <i class="fas fa-add text-base-content hover:text-base-content/50"></i>
                            </button>
                        @endif
                    </div>

                    @if($isEditor)
                        <div x-cloak x-show="toggleName" id="exsadddiv" class="mt-3 max-w-xl rounded border p-3">
                            <form method="POST" action="{{ $postAction }}" onsubmit="return verifyExsAddForm(this)" class="space-y-2">
                                @csrf
                                <div>
                                    <label for="title" class="block text-sm font-medium">{{ __('exsiccati.TITLE') }}</label>
                                    <input id="title" name="title" type="text" class="mt-1 w-[90%] rounded border px-2 py-1.5" />
                                </div>
                                <div>
                                    <label for="abbreviation" class="block text-sm font-medium">{{ __('exsiccati.ABB') }}</label>
                                    <input id="abbreviation" name="abbreviation" type="text" class="mt-1 w-full max-w-[480px] rounded border px-2 py-1.5" />
                                </div>
                                <div>
                                    <label for="editor" class="block text-sm font-medium">{{ __('exsiccati.EDITOR') }}</label>
                                    <input id="editor" name="editor" type="text" class="mt-1 w-full max-w-[300px] rounded border px-2 py-1.5" />
                                </div>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <div>
                                        <label for="exsrange" class="block text-sm font-medium">{{ __('exsiccati.NUM_RANGE') }}</label>
                                        <input id="exsrange" name="exsrange" type="text" class="mt-1 w-full max-w-[180px] rounded border px-2 py-1.5" />
                                    </div>
                                    <div>
                                        <label for="source" class="block text-sm font-medium">{{ __('exsiccati.SOURCE') }}</label>
                                        <input id="source" name="source" type="text" class="mt-1 w-full max-w-[480px] rounded border px-2 py-1.5" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">{{ __('exsiccati.DATE_RANGE') }}</label>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        <input name="startdate" type="text" class="w-full max-w-[180px] rounded border px-2 py-1.5" />
                                        <input name="enddate" type="text" class="w-full max-w-[180px] rounded border px-2 py-1.5" />
                                    </div>
                                </div>
                                <div>
                                    <label for="sourceidentifier" class="block text-sm font-medium">{!! __('exsiccati.SOURCE_ID_INDEXS') !!}</label>
                                    <input id="sourceidentifier" name="sourceidentifier" type="text" class="mt-1 w-[90%] rounded border px-2 py-1.5" />
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium">{{ __('exsiccati.NOTES') }}</label>
                                    <input id="notes" name="notes" type="text" class="mt-1 w-[90%] rounded border px-2 py-1.5" />
                                </div>
                                <x-button name="formsubmit" type="submit" value="Add Exsiccata Title" class="text-sm">
                                    <span>{{ __('exsiccati.ADD_EXS_TITLE') }}</span>
                                </x-button>
                            </form>
                        </div>
                    @endif

                    <div class="mt-6">
                        <div class="mb-3 text-lg font-semibold">{{ __('exsiccati.EXS_TITLES') }}</div>

                        @if(empty($titles))
                            <div class="bg-base-100 px-4 py-6 text-lg">
                                {{ __('exsiccati.NO_EXS_MATCHING') }}
                            </div>
                        @else
                            <ul class="ml-6 list-disc space-y-2">
                                @foreach($titles as $titleId => $titleData)
                                    <li>
                                        <div>
                                            <a href="{{ route('exsiccata.index') . '?' . http_build_query(array_filter([
                                                'ometid' => $titleId,
                                                'searchterm' => $searchTerm,
                                                'specimenonly' => $specimenOnly,
                                                'imagesonly' => $imagesOnly,
                                                'collid' => $collId,
                                                'sortby' => $sortBy,
                                            ], static fn ($value) => $value !== null && $value !== '' && $value !== 0 && $value !== '0')) }}" class="font-medium text-link-darker underline underline-offset-2">
                                                {!! Purify::clean($titleData['title'] ?? '') !!}
                                            </a>
                                        </div>

                                        @if(!empty($titleData['editor']) || !empty($titleData['exsrange']))
                                            <div class="ml-4 text-sm text-base-content/50">
                                                {!! Purify::clean($titleData['editor'] ?? '') !!} {!! !empty($titleData['exsrange']) ? ' [' . Purify::clean($titleData['exsrange']) . ']' : '' !!}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <x-sticky-margin-container class="w-full max-w-[20rem] shrink-0 lg:h-auto lg:ml-0 lg:w-auto">
                    <form method="GET" action="{{ route('exsiccata.index') }}" class="relative z-10 w-full rounded border bg-base-200 p-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block font-semibold">{{ __('exsiccati.SEARCH') }}</label>
                                <input type="text" name="searchterm" value="{{ $searchTerm }}" class="mt-1 w-full rounded border px-3 py-2 bg-base-100" onchange="this.form.submit()"/>
                            </div>

                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="specimenonly" value="1" {{ $specimenOnly ? 'checked' : '' }} onchange="this.form.submit()"/>
                                <span>{{ __('exsiccati.DISP_ONLY_W_SPECS') }}</span>
                            </label>

                            @if($specimenOnly)
                                <div class="space-y-4 pl-6">
                                    <div>
                                        <label class="block font-medium">{{ __('exsiccati.LIMIT_TO') }}</label>
                                        <x-select name="collid" class="mt-1 w-full" :items="$collectionItems":defaultValue="$collId ?: null" :onChange="'this.form.submit()'" :select_text="__('exsiccati.ALL_COLLS')"/>
                                    </div>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="imagesonly" value="1" {{ $imagesOnly ? 'checked' : '' }} onchange="this.form.submit()"/>
                                        <span>{{ __('exsiccati.DISP_ONLY_W_IMGS') }}</span>
                                    </label>
                                </div>
                            @else
                                <input type="hidden" name="imagesonly" value="0" />
                            @endif

                            <div class="flex flex-wrap items-center gap-4">
                                <span class="font-bold">{{ __('exsiccati.DISP_SORT_BY') }}:</span>
                                <div class="flex flex-wrap items-center gap-4">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="sortby" value="0" {{ $sortBy === 0 ? 'checked' : '' }} onchange="this.form.submit()" />
                                        <span>{{ __('exsiccati.TITLE') }}</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="sortby" value="1" {{ $sortBy === 1 ? 'checked' : '' }} onchange="this.form.submit()" />
                                        <span>{{ __('exsiccati.ABB') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <x-button name="formsubmit" type="submit" value="dlexs_titleOnly" class="w-full justify-center text-sm">
                                    <i class="fa-solid fa-download h-4 w-4"></i>
                                    <span>{{ __('exsiccati.TITLES') }}</span>
                                </x-button>
                                <x-button name="formsubmit" type="submit" value="dlexs" class="w-full justify-center text-sm">
                                    <i class="fa-solid fa-download h-4 w-4"></i>
                                    <span>{{ __('exsiccati.OCCS') }}</span>
                                </x-button>
                                <x-button name="formsubmit" type="submit" value="rebuildList" class="w-full justify-center text-sm">
                                    <span>{{ __('exsiccati.REBUILD_LIST') }}</span>
                                </x-button>
                            </div>
                        </div>
                    </form>
                </x-sticky-margin-container>
            </div>
        @elseif(! $isOccurrencePage)
            @php
                // if sourceidentifier matches pattaern, display the link
                $indExsUrl = null;
                if (preg_match('/^http.+IndExs.+={1}(\d+)$/', (string) ($title['sourceidentifier'] ?? ''), $matches))
                    $indExsUrl = ['url' => $title['sourceidentifier'] ?? '', 'label' => 'IndExs #' . $matches[1]];
            @endphp

            <div x-data="{ exsEditOpen: false, numAddOpen: false }">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">{{ $title['title'] ?? '' }}</h1>
                        @if($indExsUrl)
                            <a href="{{ $indExsUrl['url'] }}" target="_blank" class="mt-1 inline-block text-link-darker underline underline-offset-2">
                                {{ $indExsUrl['label'] }}
                            </a>
                        @endif
                        @if(!empty($title['abbreviation']))
                            <div class="mt-2">{{ __('exsiccati.ABB') }}: {{ $title['abbreviation'] }}</div>
                        @endif
                        @if(!empty($title['editor']))
                            <div>{{ __('exsiccati.EDITOR') }}: {{ $title['editor'] }}</div>
                        @endif
                        @if(!empty($title['exsrange']))
                            <div>{{ __('exsiccati.NUM_RANGE') }}: {{ $title['exsrange'] }}</div>
                        @endif
                        @if(!empty($title['notes']))
                            <div>{{ __('exsiccati.NOTES') }}: {{ $title['notes'] }}</div>
                        @endif
                    </div>

                    @if($isEditor)
                        <div class="flex gap-2">
                            <button type="button" @click="exsEditOpen = !exsEditOpen" class="cursor-pointer px-3 py-2">
                                <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                            </button>
                            <button type="button" @click="numAddOpen = !numAddOpen" class="cursor-pointer px-3 py-2 text-lg leading-none font-bold">
                                 <i class="fas fa-add text-base-content hover:text-base-content/50"></i>
                            </button>
                        </div>
                    @endif
                </div>

            @if($isEditor)
                <div x-cloak x-show="exsEditOpen" id="exseditdiv" class="space-y-4 rounded border p-4">
                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyExsAddForm(this)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.TITLE') }}</label>
                            <input name="title" type="text" value="{{ $title['title'] ?? '' }}" class="mt-1 w-[90%] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.ABB') }}</label>
                            <input name="abbreviation" type="text" value="{{ $title['abbreviation'] ?? '' }}" class="mt-1 w-full max-w-[500px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.EDITOR') }}</label>
                            <input name="editor" type="text" value="{{ $title['editor'] ?? '' }}" class="mt-1 w-full max-w-[300px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.NUM_RANGE') }}</label>
                            <input name="exsrange" type="text" value="{{ $title['exsrange'] ?? '' }}" class="mt-1 w-full max-w-[180px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.DATE_RANGE') }}</label>
                            <div class="mt-1 flex flex-wrap gap-3">
                                <input name="startdate" type="text" value="{{ $title['startdate'] ?? '' }}" class="w-full max-w-[180px] rounded border px-3 py-2" />
                                <input name="enddate" type="text" value="{{ $title['enddate'] ?? '' }}" class="w-full max-w-[180px] rounded border px-3 py-2" />
                            </div>
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.SOURCE') }}</label>
                            <input name="source" type="text" value="{{ $title['source'] ?? '' }}" class="mt-1 w-full max-w-[480px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{!! __('exsiccati.SOURCE_ID_INDEXS') !!}</label>
                            <input name="sourceidentifier" type="text" value="{{ $title['sourceidentifier'] ?? '' }}" class="mt-1 w-[90%] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.NOTES') }}</label>
                            <input name="notes" type="text" value="{{ $title['notes'] ?? '' }}" class="mt-1 w-[90%] rounded border px-3 py-2" />
                        </div>
                        <x-button name="formsubmit" type="submit" value="Save" class="text-sm">
                            {{ __('exsiccati.SAVE') }}
                        </x-button>
                    </form>

                    <form method="POST" action="{{ $postAction }}" onsubmit="return window.confirm('{{ __('exsiccati.SURE_DELETE_EXS') }}');">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <x-button name="formsubmit" type="submit" value="Delete Exsiccata" variant="error" class="text-sm">
                            {{ __('exsiccati.DEL_EXS') }}
                        </x-button>
                    </form>

                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyExsMergeForm(this)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.TARGET_EXS') }}</label>
                            <x-select name="targetometid" class="mt-1" :items="$exsTargetItems" select_text="--------"/>
                        </div>
                        <x-button name="formsubmit" type="submit" value="Merge Exsiccatae" class="text-sm">
                            {{ __('exsiccati.MERGE_EXS') }}
                        </x-button>
                    </form>
                </div>

                <div x-cloak x-show="numAddOpen" id="numadddiv" class="rounded border p-4">
                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyNumAddForm(this)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.EXS_NUM') }}</label>
                            <input name="exsnumber" type="text" class="mt-1 w-full max-w-[180px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.NOTES') }}</label>
                            <input name="notes" type="text" class="mt-1 w-[90%] rounded border px-3 py-2" />
                        </div>
                        <x-button name="formsubmit" type="submit" value="Add New Number" class="text-sm">
                            {{ __('exsiccati.ADD_NEW_NUM') }}
                        </x-button>
                    </form>
                </div>
            @endif
            </div>

            <hr class="border border-accent-lighter" />

            @if(empty($numbers))
                <div class="bg-base-100 text-lg font-semibold">
                    {{ __('exsiccati.NO_EXS_NUMS') }}
                </div>
            @else
                <ul class="ml-6 list-disc space-y-2">
                    @foreach($numbers as $numberId => $numberData)
                        <li>
                            <div>
                                <a href="{{ route('exsiccata.index') . '?' . http_build_query(array_filter([
                                    'ometid' => $currentOmetid,
                                    'omenid' => $numberId,
                                    'searchterm' => $searchTerm,
                                    'specimenonly' => $specimenOnly,
                                    'imagesonly' => $imagesOnly,
                                    'collid' => $collId,
                                    'sortby' => $sortBy,
                                ], static fn ($value) => $value !== null && $value !== '' && $value !== 0 && $value !== '0')) }}" class="font-medium text-link-darker underline underline-offset-2">
                                    #{{ $numberData['number'] ?? '' }}
                                    @if(!empty($numberData['sciname']))
                                        - <i>{{ $numberData['sciname'] }}</i>
                                    @endif
                                    @if(!empty($numberData['occurstr']))
                                        , {{ $numberData['occurstr'] }}
                                    @endif
                                </a>
                            </div>

                            @if(!empty($numberData['notes']))
                                <div class="ml-4 text-sm text-base-content/50">{{ $numberData['notes'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        @else
            @php
            //Number Page
                $indExsUrl = null;
                if (preg_match('/^http.+IndExs.+={1}(\d+)$/', (string) ($number['sourceidentifier'] ?? ''), $matches))
                    $indExsUrl = ['url' => $number['sourceidentifier'] ?? '', 'label' => 'IndExs #' . $matches[1]];
            @endphp

            <div x-data="{ numEditOpen: false, occAddOpen: false }">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">
                            <a href="{{ route('exsiccata.index') . '?' . http_build_query(array_filter([
                                'ometid' => $currentOmetid,
                                'searchterm' => $searchTerm,
                                'specimenonly' => $specimenOnly,
                                'imagesonly' => $imagesOnly,
                                'collid' => $collId,
                                'sortby' => $sortBy,
                            ], static fn ($value) => $value !== null && $value !== '' && $value !== 0 && $value !== '0')) }}" class="text-link-darker underline underline-offset-2">
                                {{ Purify::clean($title['title']) ?? '' }}
                            </a>
                            #{{ $number['exsnumber'] ?? '' }}
                        </h1>
                        <div class="mt-2 space-y-1">
                            @if(!empty($title['abbreviation']))
                                <div>{{ $title['abbreviation'] }}</div>
                            @endif
                            @if(!empty($title['editor']))
                                <div>{{ $title['editor'] }}</div>
                            @endif
                            @if(!empty($title['exsrange']))
                                <div>[{{ $title['exsrange'] }}]</div>
                            @endif
                            @if(!empty($number['notes']))
                                <div>{{ $number['notes'] }}</div>
                            @endif
                            @if($indExsUrl)
                                <a href="{{ $indExsUrl['url'] }}" target="_blank" class="inline-block text-link-darker underline underline-offset-2">
                                    {{ $indExsUrl['label'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($isEditor)
                        <div class="flex gap-2">
                            <button type="button" @click="numEditOpen = !numEditOpen" class="cursor-pointer px-3 py-2">
                                <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                            </button>
                            <button type="button" @click="occAddOpen = !occAddOpen" class="cursor-pointer px-3 py-2 text-lg leading-none font-bold">
                                <i class="fas fa-add text-base-content hover:text-base-content/50"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @if($isEditor)
                <div x-cloak x-show="numEditOpen" id="numeditdiv" class="border space-y-4 p-4">
                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyNumAddForm(this)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.NUMBER') }}</label>
                            <input name="exsnumber" type="text" value="{{ $number['exsnumber'] ?? '' }}" class="mt-1 w-full max-w-[180px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.NOTES') }}</label>
                            <input name="notes" type="text" value="{{ $number['notes'] ?? '' }}" class="mt-1 w-[90%] rounded border px-3 py-2" />
                        </div>
                        <x-button name="formsubmit" type="submit" value="Save Edits" class="text-sm">
                            {{ __('exsiccati.SAVE_EDITS') }}
                        </x-button>
                    </form>

                    <form method="POST" action="{{ $postAction }}" onsubmit="return window.confirm('{{ __('exsiccati.SURE_DEL_EXS_NUM') }}');">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                        <x-button name="formsubmit" type="submit" value="Delete Number" variant="error" class="text-sm">
                            {{ __('exsiccati.DEL_NUM') }}
                        </x-button>
                    </form>

                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyExsMergeForm(this)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.TARGET_EXS') }}</label>
                            <x-select name="targetometid" class="mt-1 w-full max-w-[90%]" :items="$exsTargetItems" select_text="-----"/>
                        </div>
                        <x-button name="formsubmit" type="submit" value="Transfer Number" class="text-sm">
                            {{ __('exsiccati.TRANSFER_NUM') }}
                        </x-button>
                    </form>
                </div>

                <div x-cloak x-show="occAddOpen" id="occadddiv" class="rounded border p-4">
                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyOccAddForm(this)" class="space-y-3">
                        @csrf
                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.COLL') }}</label>
                            <x-select name="occaddcollid" class="mt-1 w-full max-w-[420px]" :items="$occAddCollectionItems" :select_text="__('exsiccati.SEL_COLL')"/>
                        </div>
                        <div class="grid gap-3 md:grid-cols-4">
                            <div>
                                <label class="block font-medium">{{ __('exsiccati.CATNUM') }}</label>
                                <input name="identifier" type="text" class="mt-1 w-full max-w-[180px] rounded border px-3 py-2" />
                            </div>
                            <div class="md:pt-8 md:text-center">{{ __('exsiccati.OR') }}</div>
                            <div>
                                <label class="block font-medium">{{ __('exsiccati.COLLECTOR_LAST') }}</label>
                                <input name="recordedby" type="text" class="mt-1 w-full max-w-[220px] rounded border px-3 py-2" />
                            </div>
                            <div>
                                <label class="block font-medium">{{ __('exsiccati.NUMBER') }}</label>
                                <input name="recordnumber" type="text" class="mt-1 w-full max-w-[180px] rounded border px-3 py-2" />
                            </div>
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.RANKING') }}</label>
                            <input name="ranking" type="text" class="mt-1 w-full max-w-[120px] rounded border px-3 py-2" />
                        </div>
                        <div>
                            <label class="block font-medium">{{ __('exsiccati.NOTES') }}</label>
                            <input name="notes" type="text" class="mt-1 w-[90%] rounded border px-3 py-2" />
                        </div>
                        <x-button name="formsubmit" type="submit" value="Add Specimen Link" class="text-sm">
                            {{ __('exsiccati.ADD_SPEC_LINK') }}
                        </x-button>
                    </form>
                </div>
            @endif
            </div>
            <div class="my-4">
                <hr class="border border-accent-lighter" >
            </div>
            @if(empty($occurrences))
                <div class="bg-base-100 px-4 py-6 font-semibold">
                    {{ __('exsiccati.NO_SPECS_WITH_EX_NUM') }}
                </div>
            @else
                <div class="mt-4">
                    @foreach($occurrences as $occid => $occ)
                        <div x-data="{ occEditOpen: false }">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div class="space-y-1">
                                    <div class="font-bold">{{ $occ['collname'] ?? '' }}</div>
                                    <div>{{ __('exsiccati.CATNUM') }}: {{ $occ['catalognumber'] ?? '' }}</div>
                                    @if(!empty($occ['occurrenceid']))
                                        <div>{{ $occ['occurrenceid'] }}</div>
                                    @endif
                                    <div>
                                        {{ $occ['recby'] ?? '' }}
                                        {{ !empty($occ['recnum']) ? '#' . $occ['recnum'] : 's.n.' }}
                                        <span class="ml-3">{{ $occ['eventdate'] ?? '' }}</span>
                                    </div>
                                    <div><i>{{ $occ['sciname'] ?? '' }}</i> {{ $occ['author'] ?? '' }}</div>
                                    <div>
                                        {{ $occ['country'] ?? '' }}
                                        @if(!empty($occ['state'])) , {{ $occ['state'] }} @endif
                                        @if(!empty($occ['county'])) , {{ $occ['county'] }} @endif
                                        @if(!empty($occ['locality'])) , {{ $occ['locality'] }} @endif
                                    </div>
                                    @if(!empty($occ['notes']))
                                        <div>{{ $occ['notes'] }}</div>
                                    @endif
                                    <div>
                                        <a href="{{ url('/occurrence/' . $occid) }}" class="text-link-darker underline underline-offset-2">
                                            {{ __('exsiccati.FULL_RECORD_DETAILS') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    @if(!empty($occ['img']))
                                        @php
                                            $img = reset($occ['img']);
                                        @endphp
                                        <a href="{{ $img['url'] }}" target="_blank">
                                            <img src="{{ $img['tnurl'] }}" alt="" class="w-20 rounded border" />
                                        </a>
                                    @endif

                                    @if($isEditor)
                                        <button type="button" @click="occEditOpen = !occEditOpen" class="px-3 py-2">
                                            <i class="cursor-pointer fas fa-edit text-base-content hover:text-base-content/50"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($isEditor)
                                {{-- Occurrence link editing stays inline so each linked specimen can be adjusted without leaving the number page. --}}
                                <div x-cloak x-show="occEditOpen" id="occeditdiv-{{ $occid }}" class="mt-4 space-y-4 rounded border p-4">
                                    <form method="POST" action="{{ $postAction }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                                        <input type="hidden" name="occid" value="{{ $occid }}" />
                                        <div>
                                            <label class="block font-medium">{{ __('exsiccati.RANKING') }}</label>
                                            <input name="ranking" type="text" value="{{ $occ['ranking'] ?? '' }}" class="mt-1 w-full max-w-[120px] rounded border px-3 py-2" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">{{ __('exsiccati.NOTES') }}</label>
                                            <input name="notes" type="text" value="{{ $occ['notes'] ?? '' }}" class="mt-1 w-[90%] rounded border px-3 py-2" />
                                        </div>
                                        <x-button name="formsubmit" type="submit" value="Save Specimen Link Edit" class="text-sm">
                                            {{ __('exsiccati.SAVE_SPEC_LINK_EDIT') }}
                                        </x-button>
                                    </form>

                                    <form method="POST" action="{{ $postAction }}" onsubmit="return window.confirm('{{ __('exsiccati.SURE_DEL_SPEC_LINK') }}');">
                                        @csrf
                                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                                        <input type="hidden" name="occid" value="{{ $occid }}" />
                                        <x-button name="formsubmit" type="submit" value="Delete Link to Specimen" variant="error" class="text-sm">
                                            {{ __('exsiccati.DEL_SPEC_LINK') }}
                                        </x-button>
                                    </form>

                                    <form method="POST" action="{{ $postAction }}" onsubmit="return verifyOccTransferForm(this)" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="ometid" value="{{ $currentOmetid }}" />
                                        <input type="hidden" name="omenid" value="{{ $omenid }}" />
                                        <input type="hidden" name="occid" value="{{ $occid }}" />
                                        <div>
                                            <label class="block font-medium">{{ __('exsiccati.TARGET_EXS') }}</label>
                                            <x-select
                                                name="targetometid"
                                                class="mt-1 w-full max-w-[90%]"
                                                :items="$exsTargetItems"
                                                select_text="------"
                                            />
                                        </div>
                                        <div>
                                            <label class="block font-medium">{{ __('exsiccati.TARGET_EXS_NUM') }}</label>
                                            <input name="targetexsnumber" type="text" class="mt-1 w-full max-w-[180px] rounded border px-3 py-2" />
                                        </div>
                                        <x-button name="formsubmit" type="submit" value="Transfer Specimen" class="text-sm">
                                            {{ __('exsiccati.TRANSFER_SPEC') }}
                                        </x-button>
                                    </form>
                                </div>
                            @endif

                            <div class="my-4">
                                <hr class="border border-accent-lighter" >
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-margin-layout>
