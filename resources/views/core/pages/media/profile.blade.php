@props(['mediaID', 'imgArr', 'isEditor', 'status' => ''])
<?php global $SERVER_ROOT, $LANG_TAG, $LANG, $IS_ADMIN, $DEFAULT_TITLE, $SYMB_UID;

include_once($SERVER_ROOT . '/classes/Media.php');
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('imagelib/imgdetails');

$eMode = request('emode') ? 'true': 'false';

$serverPath = GeneralUtil::getDomain();
if ($imgArr) {
	$imgUrl = $imgArr['url'];
	$origUrl = $imgArr['originalUrl'];
	$metaUrl = $imgArr['url'];
	if (array_key_exists('MEDIA_DOMAIN', $GLOBALS)) {
		if (substr($imgUrl, 0, 1) == '/') {
			$imgUrl = $GLOBALS['MEDIA_DOMAIN'] . $imgUrl;
			$metaUrl = $GLOBALS['MEDIA_DOMAIN'] . $metaUrl;
		}
		if ($origUrl && substr($origUrl, 0, 1) == '/') {
			$origUrl = $GLOBALS['MEDIA_DOMAIN'] . $origUrl;
		}
	}
	if (substr($metaUrl, 0, 1) == '/') {
		$metaUrl = $serverPath . $metaUrl;
	}
}

?>
<x-margin-layout :hasHeader="false" :hasFooter="false" :hasNavbar="false" x-data="{editOpen: {{ $eMode }}}" class="px-0">
    @push('head')
        @if($imgArr)
		<meta property="og:title" content="{{ $imgArr["sciname"] }}" />
		<meta property="og:site_name" content="{{ $DEFAULT_TITLE }}" />
		<meta property="og:image" content="{{ $metaUrl }}" />
        @endif
	    <title>{{ $DEFAULT_TITLE . " Image Details: #" . $mediaID }}</title>
    @endpush

    <div class="flex items-center gap-4">
        <script>
		function openOccurrenceSearch(target) {
			let occWindow = open("{{legacy_url('collections/misc/occurrencesearch.php') }}?targetid=" + target, "occsearch", "resizable=1,scrollbars=1,toolbar=0,width=750,height=750,left=400,top=40");
			if (occWindow.opener == null) occWindow.opener = self;
		}
        </script>
	    <h1 class="text-4xl text-primary font-bold">{{ $LANG['IMG_DETAILS'] }}</h1>
        @if($imgArr)
        <div class="flex grow items-center justify-end gap-2">
            @if($imgArr['occid'])
				<div title="{{ $LANG['EDITING_PRIVILEGES'] }}">
					<x-link class="flex items-center gap-1" href="{{ legacy_url('collections/editor/occurrenceeditor.php?&tabtarget=2&occid=' . $imgArr['occid']) }}" target="_blank">
						<img src="{{ legacy_url('images/edit.png') }}" class="w-5"/><span class="text-sm">{{ $LANG['SPEC'] }}</span>
					</x-link>
				</div>
            @elseif($isEditor)
                <div>
                    <x-link class="flex items-center gap-1" x-bind:href="'#emode=' + (editOpen? 'true':'false')" @click="editOpen=!editOpen" title="{{ $LANG['EDIT_IMAGE'] }}">
                        <img src="{{ legacy_url('images/edit.png') }}" class="w-5"/><span class="text-sm">{{ $LANG['IMG'] }}</span>
                    </x-link>
                </div>
            @endif

            @if($SYMB_UID && ($IS_ADMIN || array_key_exists("TaxonProfile", $USER_RIGHTS)))
                <div title="{{ $LANG['TAXON_PROFILE_EDITING'] }}">
                    <x-link class="flex items-center gap-1" href="{{ legacy_url('taxa/profile/tpeditor.php?&tabindex=1&tid=' . $imgArr['tid'])}}" target="_blank">
                        <img src="{{ legacy_url('images/edit.png') }}" class="w-5"/><span class="text-sm">{{ $LANG['TP'] }}</span>
                    </x-link>
                </div>
            @endif
        </div>
        @endif
    </div>

    @if($status )
        <hr />
        <div class="text-error">
            {!! $status !!}
        </div>
        <hr />
    @endif

    @if(!$imgArr)
        <h2>{{ $LANG['UNABLE_TO_LOCATE'] }}</h2>
    @else
        @if($isEditor && !$imgArr['occid'])
            <div x-show="editOpen" x-cloak id="imageedit" class="flex flex-col gap-4 ">
                <form name="editform"  method="post" target="_self">
                    @csrf
                    <x-formgroup :label="$LANG['EDIT_IMAGE_DETAILS']">
                        <x-input :label="$LANG['CAPTION']" class="w-65" :inline="true"
                            name="caption" value="{{ $imgArr['caption'] }}"
                        />

                        <div class="flex flex-wrap items-center gap-1">
                            <label for="creatorUid" class="font-bold">{{ $LANG['CREATOR_USER_ID'] }}:</label>
                            <select class="px-1 py-1 text-base border border-base-300 rounded-md" id="creatorUid" name="creatorUid">
                                <option value="">{{ $LANG['SELECT_CREATOR'] }}</option>
                                <option value="">---------------------------------------</option>
                                {!! Media::renderCreatorOptions($imgArr['creatorUid']) !!}
                            </select>
                            <span>
                                * {{ $LANG['USER_REGISTERED_SYSTEM'] }}
                            </span>
                        </div>

                        <x-input :label="$LANG['CREATOR_OVERRIDE']" class="max-w-65" :inline="true"
                            name="creator" value="{{ $imgArr['creator'] }}"
                            :assistive_text="'* ' . $LANG['OVERRIDE_SELECTION']"
                        />

                        <x-input :label="$LANG['MANAGER']" class="max-w-100" :inline="true"
                            name="owner" value="{{ $imgArr['owner'] }}"
                        />

                        <x-input :label="$LANG['SOURCE_URL']" class="max-w-115" :inline="true"
                            name="sourceUrl" value="{{ $imgArr['sourceUrl'] }}"
                        />

                        <x-input :label="$LANG['COPYRIGHT']" class="max-w-115" :inline="true"
                            name="copyright" value="{{ $imgArr['copyright'] }}"
                        />

                        <x-input :label="$LANG['RIGHTS']" class="max-w-115" :inline="true"
                            name="rights" value="{{ $imgArr['rights'] }}"
                        />

                        <x-input :label="$LANG['LOCALITY']" class="max-w-138" :inline="true"
                            name="locality" value="{{ $imgArr['locality'] }}"
                        />

                        <div class="flex items-center gap-2">
                            <x-input :label="$LANG['OCCURRENCE_RECORD'] . ' #'" :inline="true"
                                id="imgdisplay-{{ $mediaID; }}" name="displayoccid" value="" disabled
                                class="max-w-10"
                            />
                            <input id="imgoccid-{{ $mediaID; }}" name="occid" type="hidden" value="" />
                            <x-button onclick="openOccurrenceSearch('{{ $mediaID; }}');return false">
                                {{ $LANG['LINK_OCCUR_RECORD'] }}
                            </x-button>
                        </div>

                        <x-input :label="$LANG['NOTES']" class="max-w-138" :inline="true"
                            name="notes" value="{{ $imgArr['notes'] }}"
                        />

                        <x-input :label="$LANG['SORT_SEQUENCE']" size="5" :inline="true"
                            name="sortSequence" value="{{ $imgArr['sortSequence'] ?? '' }}"
                        />

                        <div>
                            <x-input :label="$LANG['WEB_IMAGE']" :inline="true" required
                                name="url" value="{{ $imgArr['url'] }}"
                            />
                            @if($imgArr["url"] && stripos($imgArr["url"], $GLOBALS['MEDIA_ROOT_URL']) === 0)
                                <div class="m-[70px]">
                                    <input type="checkbox" name="renameweburl" value="1" />
                                    {{ $LANG['RENAME_WEB_IMAGE_FILE'] }}
                                </div>
                                <input name="old_url" type="hidden" value="{{ $imgArr['url']; }}" />
                            @endif
                        </div>
                        <div>
                            <x-input :label="$LANG['THUMBNAIL']" :inline="true"
                                name="thumbnailUrl" value="{{ $imgArr['thumbnailUrl'] }}"
                            />
                            @if($imgArr["thumbnailUrl"] && stripos($imgArr["thumbnailUrl"], $GLOBALS['MEDIA_ROOT_URL']) === 0)
                                <div class="m-[70px]">
                                    <input type="checkbox" name="renametnurl" value="1" />
                                    {{ $LANG['RENAME_THUMBNAIL_IMAGE_FILE'] }}
                                </div>
                                <input name="old_thumbnailurl" type="hidden" value="{{ $imgArr['thumbnailUrl']; }}" />
                            @endif
                        </div>
                        <div style="margin-top:2px;">

                            <x-input :label="$LANG['LARGE_IMAGE']" :inline="true"
                                name="originalUrl" value="{{ $imgArr['originalUrl'] }}"
                            />
                            @if($imgArr["originalUrl"] && stripos($imgArr["originalUrl"], $GLOBALS['MEDIA_ROOT_URL']) === 0)
                                <div class="m-[70px]">
                                    <input type="checkbox" name="renameorigurl" value="1" />
                                    {{ $LANG['RENAME_LARGE_IMAGE_FILE'] }}
                                </div>
                                <input name="old_originalurl" type="hidden" value="{{ $imgArr['originalUrl']; }}" />
                            @endif
                        </div>
                        <input name="mediaid" type="hidden" value="{{ $mediaID; }}" />
                        <div style="margin-top:2px;">
                            <x-button type="submit" name="submitaction" id="editsubmit" value="Submit Image Edits">{{ $LANG['SUBMIT_IMAGE_EDITS'] }}</x-button>
                        </div>
                    </x-formgroup>
                </form>
                <form name="changetaxonform" action="{{ url('media/' . $mediaID . '/transfer/taxa') }}" method="post" target="_self">
                    @csrf
                    <x-formgroup :label="$LANG['TRANSFER_IMAGE_TO_DIFF_NAME']">
                        <x-taxa-search id="taxa" :label="$LANG['TRANSFER_TO_TAXON']" :hide_selector="true" :hide_synonyms_checkbox="true" />
                        <input type="hidden" id="tid" name="targettid" value="" />
                        <input type="hidden" name="sourcetid" value="{{ $imgArr['tid']; }}" />
                        <input type="hidden" name="mediaid" value="{{ $mediaID; }}" />
                        <input type="hidden" name="submitaction" value="Transfer Image" />
                        <x-button type="submit" name="submitaction" value="Transfer Image">{{ $LANG['TRANSFER_IMAGE'] }}</x-button>
                    </x-formgroup>
                </form>
                <form name="deleteform" method="post" target="_self" onsubmit="return window.confirm('{{ $LANG['DELETE_IMAGE_FROM_SERVER'] }}');">
                    @csrf
                    <x-formgroup :label="$LANG['AUTHORIZED_REMOVE_IMAGE']">
                        <input type="hidden" name="_method" value="DELETE">
                        <input name="mediaid" type="hidden" value="{{ $mediaID; }}" />
                        <div style="margin-top:2px;">
                            <x-button variant="error" type="submit" name="submitaction" id="submit" value="Delete Image">{{ $LANG['DELETE_IMAGE'] }}</x-button>
                        </div>
                        <input type="hidden" name="tid" value="{{ $imgArr["tid"]; }}" />
                        <x-checkbox id="removeimg" name="removeimg" :checked="false" :label="$LANG['REMOVE_IMG_FROM_SERVER']"/>
                        <div class="text-error">
                            {{ $LANG['BOX_CHECKED_IMG_DELETED'] }}
                        </div>
                    </x-formgroup>
                </form>
            </div>
        @endif
        <div class="flex">
            <div class="w-fit">
                @php
                $imgDisplay = (!$imgUrl || $imgUrl == 'empty') && $origUrl? $origUrl: $imgUrl;
                $mediaType = MediaType::tryFrom($imgArr['mediaType']);
                @endphp

                @if($mediaType === MediaType::Image)
                    <a href="{{ $imgDisplay }}">
                        <img src="{{ $imgDisplay }}" class="w-75" />
                    </a>
                    @if($origUrl)
                        <div><x-link href="{{ $origUrl }}">{{$LANG['CLICK_IMAGE'] }}</x-link></div>
                    @endif
                @elseif($mediaType === MediaType::Audio)
                    <audio controls class="my-4">
                        <source src="{{ $origUrl }}" type="{{ $imgArr['format'] }}">
                        Your browser does not support the audio element.
                    </audio>
                @endif
            </div>
            <div class="px-4">
                <x-text-label :label="$LANG['SCIENTIFIC_NAME']">
                    <x-link href="{{ legacy_url('taxa/index.php?taxon='. $imgArr['tid']) }}"><i>{{ $imgArr["sciname"] }}</i> {{ $imgArr["author"] }}</x-link>
                </x-text-label>

                @if($imgArr['caption'])
                    <x-text-label :label="$LANG['CAPTION']">{{ $imgArr['caption'] }}</x-text-label>
                @endif

                @if($imgArr['creatorDisplay'])
                    <x-text-label :label="$LANG['CREATOR']">
                    @if(!$imgArr['creator'])
                        <x-link href="{{ url('media/search') . '?imagetype=all&submitaction=search&phuid=' . $imgArr['creatorUid'] }}">
                            {{ $imgArr['creatorDisplay'] }}
                        </x-link>
                    @else
                        {{ $imgArr['creatorDisplay'] }}
                    @endif
                    </x-text-label>
                @endif

                @if($imgArr['owner'])
                <x-text-label :label="$LANG['MANAGER']">{{ $imgArr['owner'] }}</x-text-label>
                @endif

                @if($imgArr['sourceUrl'])
                <x-text-label :label="$LANG['IMAGE_SOURCE']">
                    <x-link href="{{ $imgArr['sourceUrl'] }}">{{ $imgArr['sourceUrl'] }}</x-link>
                </x-text-label>
                @endif

                @if($imgArr['locality'])
                <x-text-label :label="$LANG['LOCALITY']">{{ $imgArr['locality'] }}</x-text-label>
                @endif

                @if($imgArr['notes'])
                <x-text-label :label="$LANG['NOTES']">{{ $imgArr['notes'] }}</x-text-label>
                @endif

                @if($imgArr['rights'])
                <x-text-label :label="$LANG['RIGHTS']">{{ $imgArr['rights'] }}</x-text-label>
                @endif

                @if($imgArr['copyright'])
                <x-text-label :label="$LANG['COPYRIGHT']">
                    @if(stripos($imgArr['copyright'], 'http'))
                    <x-link href="{{ $imgArr['copyright'] }}">{{ $imgArr['copyright'] }}</x-link>
                    @else
                    {{ $imgArr['copyright'] }}
                    @endif
                </x-text-label>
                @else
                <div>
                    <x-link href="{{ url('usagepolicy#images') }}">{{ $LANG['COPYRIGHT_DETAILS'] }}</x-link>
                </div>
                @endif

                @if($imgArr['occid'])
                <div>
                    <x-link href="{{ url('occurrence/' . $imgArr['occid'])}}">
                        {{ $LANG['DISPLAY_SPECIMEN_DETAILS'] }}
                    </x-link>
                </div>
                @endif

                @if($imgUrl)
                <div>
                    <x-link href="{{ $imgUrl }}">
                        {{ $LANG['OPEN_MEDIUM_SIZED_IMAGE'] }}
                    </x-link>
                </div>
                @endif

                @if($origUrl)
                <div>
                    <x-link href="{{ $origUrl }}">
                        {{ $LANG['OPEN_LARGE_IMAGE'] }}
                    </x-link>
                </div>
                @endif


                @if($GLOBALS['ADMIN_EMAIL'])
                <div style="margin-top:20px;">
                    {{ $LANG['ERROR_COMMENT_ABOUT_IMAGE'] }}<br/> {{ $LANG['SEND_EMAIL'] }}

                    @php
                    $emailSubject = $DEFAULT_TITLE . ' ' . $LANG['IMG_NO'] . ' ' . $mediaID;
                    $emailBody = 'Image being referenced: ' . url('media/' . $mediaID);
                    $emailRef = 'subject=' . $emailSubject . '&cc=' . $GLOBALS['ADMIN_EMAIL'] . '&body=' . $emailBody;
                    @endphp

                    <x-link href="mailto:{{ $GLOBALS['ADMIN_EMAIL'] . '?' . $emailRef }}">{{ $GLOBALS['ADMIN_EMAIL'] }}</x-link>
                </div>
                @endif
            </div>
        </div>
    @endif
</x-margin-layout>
