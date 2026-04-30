@php global $SERVER_ROOT;
include_once(legacy_path('/classes/OccurrenceSupport.php'));

$collid = request('collid')? filter_var(request('collid'), FILTER_SANITIZE_NUMBER_INT) : 0;
$page = is_numeric(request('page'))? intval(request('page')): 1;
$limit = is_numeric(request('limit'))? intval(request('limit')): 5;
$start = (($page - 1) * $limit);

$tsStart = request('tsstart') ?? '';
$tsEnd = request('tsend') ?? '';
$uid = request('uid') ? filter_var(request('uid'), FILTER_SANITIZE_NUMBER_INT) : 0;
$rs = is_numeric(request('rs')) ? filter_var(request('rs'), FILTER_SANITIZE_NUMBER_INT) : 1;
$showAllGeneralObservations = (request('showallgenobs') && request('showallgenobs') === 1) ? true : false;

//Sanition
if(!preg_match('/^[\d-]+$/', $tsStart)) $tsStart = '';
if(!preg_match('/^[\d-]+$/', $tsEnd)) $tsEnd = '';

$commentManager = new OccurrenceSupport();
$commentManager->setCollid($collid);
$collMeta = $commentManager->getCollectionMetadata();

$statusStr = '';
$commentArr = null;

//Editor is check at route level
$formSubmit = array_key_exists('formsubmit',$_REQUEST) ? $_REQUEST['formsubmit'] : '';
if($formSubmit){
    $comId = filter_var(request('comid'), FILTER_SANITIZE_NUMBER_INT) ?? '';
    if($comId){
        if($formSubmit == 'Delete Comment'){
            if(!$commentManager->deleteComment($comId)){
                $statusStr = $commentManager->getErrorStr();
            }
        }
        elseif($formSubmit == 'Make Comment Public'){
            if(!$commentManager->setReviewStatus($comId,1)){
                $statusStr = $commentManager->getErrorStr();
            }
        }
        elseif($formSubmit == 'Hide Comment from Public'){
            if(!$commentManager->setReviewStatus($comId,2)){
                $statusStr = $commentManager->getErrorStr();
            }
        }
        elseif($formSubmit == 'Mark as Reviewed'){
            if(!$commentManager->setReviewStatus($comId,3)){
                $statusStr = $commentManager->getErrorStr();
            }
        }
        elseif($formSubmit == 'Mark as Unreviewed'){
            if(!$commentManager->setReviewStatus($comId,1)){
                $statusStr = $commentManager->getErrorStr();
            }
        }
    }
}

$userArr = $commentManager->getCommentUsers($showAllGeneralObservations);
$commentArr = $commentManager->getComments($start, $limit, $tsStart, $tsEnd, $uid, $rs, $showAllGeneralObservations);

$userItems = [ item(0, __('misc_commentlist.ALL_COMMENTERS')) ];
foreach($userArr as $id => $name) {
    $userItems[] = item($id, $name);
}
@endphp

<x-margin-layout>
    <x-breadcrumbs
        :items="[
        ['title' => __('header.H_HOME'), 'href' => url('/') ],
        ['title' => __('datasets_datapublisher.COL_MANAGEMENT'), 'href' => url('collections/' . $collid) ],
        ['title' => __('misc_commentlist.OCC_COMMENTS_LISTING') ]
    ]"
    />
    <h1 class="text-4xl font-bold">
        <div class="sr-only">{{ __('misc_commentlist.OCCUR_COMMENTS') }}</div>
        {{ $collMeta['name'] }}
    </h1>

    <x-accordion :label="__('misc_commentlist.FILTER_OPT')" :open="true">
        <form class="flex flex-col gap-4">
            <x-select
                name="uid"
                :defaultValue="$uid"
                :label="__('misc_commentlist.COMMENTER')"
                class="w-full"
                :items="$userItems"
            />
            <div class="flex gap-2">
                <x-input :label="__('individual.DATE') . ' 1'" name="tsstart" type="date" :value="$tsStart" />
                <x-input :label="__('individual.DATE') . ' 2'" name="tsend" type="date" :value="$tsEnd" />
            </div>

            <x-radio
                :default_value="$rs"
                :label="__('misc_commentlist.COMMENT_TYPE')"
                name="rs"
                :options="[
                ['label' => __('projects.PUBLIC'), 'value' => 1, 'id' => 'public' ],
                ['label' => __('misc_commentlist.NON-PUBLIC'), 'value' => 2, 'id' => 'non-public' ],
                ['label' => __('misc_commentlist.REVIEWED'), 'value' => 3, 'id' => 'reviewed' ],
                ['label' => __('misc_commentlist.ALL'), 'value' => 0, 'id' => 'all' ]
            ]"
            />

            <x-button>{{ __('loans_loan_langs.REFRESH_LIST') }}</x-button>
        </form>
    </x-accordion>
    @if($statusStr)
        <x-errors :errors="message_bag([$statusStr])" />
    @endif

    @if(!empty($commentArr))
        @php
        $recCnt = $commentArr['cnt'];
		unset($commentArr['cnt']);
        @endphp
    @endif

    @if($recCnt > 0)
        @php
            $pages =  ceil($recCnt / $limit);
        @endphp
        <div class="inline-flex gap-2">
            @for($i = 1; $i <= $pages; $i++)
                @if($page === $i)
                    <span>{{ $i }}</span>
                @else
                    @php
                    $params = [
                        ...request()->except(['_token', 'page']),
                        'page' => $i,
                    ];
                @endphp
                    <x-link :href="url()->current() . '?' . http_build_query($params)">{{ $i }}</x-link>
                @endif
            @endfor
            <span
                >{{ $start + 1 }}-{{ $limit }} {{ __('imagelib_search.OF') }} {{ $recCnt }} {{ __('checklists_checklist.COMMENTS') }}</span
            >
        </div>
    @endif

    @foreach($commentArr as $commentId => $comment)
        <form method="post" class="border-base-300 flex flex-col gap-2 border p-4">
            @csrf
            <x-link :href="url('occurrence/' . $comment['occid'])" target="_blank" rel="noopener">
                {{-- TODO (Logan) look at pulling tags out of this entirely --}}
                {!! Purify::clean($comment['occurstr']) !!}
            </x-link>
            <div>
                <span class="font-bold">{{ $userArr[$comment['uid']] }}</span>
                <span class="text-base-content/75">{{ __('misc_commentlist.POSTED_ON') }} {{ $comment['ts'] }}</span>
            </div>

            @if($comment['rs'] === 1 || $comment['rs'] === 2)
                <div>{{ __('taxonomy_batchloader.STATUS') }}</div>
            @elseif($comment['rs'] === 3 )
                <div>{{ __('projects.REVIEWED') }}</div>
            @endif
            <p>{{ $comment['str'] }}</p>

            <input name="comid" type="hidden" value="{{ $commentId }}" />
            <input name="collid" type="hidden" value="{{ $collid }}" />
            <input name="page" type="hidden" value="{{ $page }}" />
            <input name="limit" type="hidden" value="{{ $limit }}" />
            <input name="tsstart" type="hidden" value="{{ $tsStart }}" />
            <input name="tsend" type="hidden" value="{{ $tsEnd }}" />
            <input name="uid" type="hidden" value="{{ $uid }}" />
            <input name="rs" type="hidden" value="{{ $rs }}" />

            @if($comment['rs'] == 2)
                <x-button name="formsubmit" value="Make Comment Public">
                    {{ __('profile_userprofile.MAKE_PUBLIC') }}
                </x-button>
            @else
                <x-button name="formsubmit" value="Hide Comment from Public">
                    {{ __('misc_commentlist.HIDE_PUBLIC') }}
                </x-button>
            @endif

            @if($comment['rs'] == 3)
                <x-button name="formsubmit" value="Mark as Unreviewed">
                    {{ __('misc_commentlist.MARK_UNREVIEWED') }}
                </x-button>
            @else
                <x-button name="formsubmit" value="Mark as Reviewed">
                    {{ __('misc_commentlist.MARK_REVIEWED') }}
                </x-button>
            @endif

            <x-button
                variant="error"
                name="formsubmit"
                type="submit"
                value="Delete Comment"
                onclick="return confirm('{{ __('misc_commentlist.SURE_DELETE_COMMENT') }}')"
            >
                {{ __('misc_commentlist.DEL_COMMENT') }}
            </x-button>
        </form>
    @endforeach
</x-margin-layout>
