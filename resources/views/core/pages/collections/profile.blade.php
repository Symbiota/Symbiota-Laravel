@props(['collection', 'stats'])

@php
global $DEFAULT_TITLE, $SERVER_HOST, $SERVER_ROOT, $CLIENT_ROOT;
include_once(legacy_path('/classes/utilities/GeneralUtil.php'));

function colUrl(string $url, string $extra_query = '') {
    return legacy_url('/collections/' . $url) . '?collid=' . request('collid') . $extra_query;
}

function tryColUrl(string $url, bool $predicate = true, string $extra_query = '') {
    return $predicate? colUrl($url, $extra_query): false;
}

$isLive = $collection->managementType == 'Live Data';
$isAggregate = $collection->managementType == 'Aggregate';
$isSpecimens = $collection->isSpecimens();
$isObservations = $collection->isObservations();

@endphp
<x-margin-layout x-data="{ showStatsMessages: false}">
    <x-breadcrumbs :items="[
        ['title' => __('header.H_HOME'), 'href' => url('')],
        ['title' => __('misc_collprofiles.COLLECTION_SEARCH'), 'href' => url('collections/search')],
        ['title' => __('misc_sharedterms.COLL_PROFILE')]
        ]" />
    <div class="flex items-center gap-4">
        @isset($collection->icon)
            <img class="h-20"src="{{ $collection->icon }}"/>
        @endisset
        <span class="md:text-4xl text-xl font-bold">{{ $collection->collectionName}}</span>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @if($datasetKey = $collection->aggKeysStr['datasetKey'] ?? false)
        <div class="hover:ring-4 focus:ring-4 rounded-md ring-accent">
            <iframe
                title="GBIF citation"
                src="https://www.gbif.org/api/widgets/literature/button?gbifDatasetKey={{ $datasetKey }}"
                frameborder="0"
                allowtransparency="true"
                class="h-7 w-40 rounded-md"
                >
            </iframe>
        </div>
        <a href="https://bionomia.net/dataset/{{ $datasetKey }}" class="hover:ring-4 focus:ring-4 rounded-md ring-accent">
            <img src="https://api.bionomia.net/dataset/{{ $datasetKey }}/badge.svg" onerror="this.style.display=\'none\'"
                alt="Bionomia dataset badge"
                class="h-7 rounded-md"
            >
        </a>
        @endif

        <div class="flex-grow md:justify-end flex items-center gap-2">
        <x-modal>
            <x-slot name='button' class="flex-grow-1" variant="clear-primary">
                {{ __('profile_usermanagement.QUICK_SEARCH') }}
            </x-slot>

            <x-slot name="title" class="text-2xl">
                {{ __('profile_usermanagement.QUICK_SEARCH') }}
            </x-slot>

            <x-slot name="body">
                <form method="get" action="{{ url('collections/table') }}"
                    hx-get="{{ url('collections/table') }}"
                    hx-push-url="true"
                    hx-target="body"
                    class="flex flex-col gap-2"
                >
                    <input type="hidden" name="collid" value="{{ $collection->collID }}">
                    <x-input name="catalogNumber" x-effect="if(modalOpen) $focus.focus($el)" label="Catalog Number" />
                    <x-taxa-search />

                    <div class="flex items-center">
                        <x-button type="submit">{{ __('collections_sharedterms.SEARCH') }}</x-button>
                        <div id="quick-search-loader" class="stroke-accent w-6 h-6 flex justify-center htmx-indicator">
                            <x-icons.loading />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-modal>

        @can('COLL_EDIT', $collection->collID)
        <x-nav-link hx-boost="true" href="{{ url('collections/table?collid=' . $collection->collID) }}">
            <x-button>{{ __('individual.OCCURRENCE_EDITOR') }}</x-button>
        </x-nav-link>
        @endcan
        </div>
    </div>

    @can('COLL_EDIT', $collection->collID)
    <x-accordion :label="__('misc_collprofiles.TOGGLE_MAN')" :open="true" variant="clear-primary">
        <div class="flex flex-wrap gap-2">
            @php
            $data_links = [
                __('misc_collprofiles.SUBMIT_IMAGE_V') => tryColUrl('editor/observationsubmit.php', $isObservations),
                __('misc_collprofiles.ADD_NEW_OCCUR') => colUrl('editor/occurrenceeditor.php', '&gotomode=1'),
                __('misc_collprofiles.CREATE_NEW_REC') => tryColUrl('editor/imageoccursubmit.php', $isSpecimens),
                __('misc_collprofiles.SKELETAL') => tryColUrl('editor/skeletalsubmit.php', $isSpecimens),
                __('misc_collprofiles.EDIT_EXISTING') => colUrl('editor/occurrencetabledisplay.php', '&displayquery=1'),
                __('misc_collprofiles.ADD_BATCH_DETER') => tryColUrl('editor/batchdeterminations.php', $isSpecimens),

                __('profile_occurrencemenu.PRINT_LABELS') => colUrl('reports/labelmanager.php'),
                __('profile_occurrencemenu.PRINT_ANNOTATIONS') => colUrl('reports/annotationmanager.php'),
                __('misc_collprofiles.BATCH_GEOREF') => tryColUrl('georef/batchgeoreftool.php', $isSpecimens),
                __('misc_collprofiles.LOAN_MANAGEMENT') => tryColUrl('loans/index.php', $isSpecimens),
            ];

            $trait_links = [
                __('misc_collprofiles.TRAIT_CODING') => colUrl('traitattr/occurattributes.php'),
                __('misc_collprofiles.TRAIT_MINING') => colUrl('traitattr/attributemining.php'),
            ];

            $admin_links = [
                __('profile_occurrencemenu.VIEW_COMMENTS') => colUrl('misc/commentlist.php'),
                __('misc_collmetadata.EDIT_METADATA') => route('collections.collmetadata.edit', ['collid' => $collection->collID]),
                __('misc_collprofiles.MANAGE_PERMISSIONS') => colUrl('misc/collpermissions.php'),
                __('misc_collprofiles.PROCESSING_TOOLBOX') => tryColUrl('specprocessor/index.php', $isSpecimens && !$isAggregate),
                __('misc_collprofiles.DARWIN_CORE_PUB') => tryColUrl('datasets/datapublisher.php', $isSpecimens && !$isAggregate),
                __('misc_collprofiles.REVIEW_SPEC_EDITS') => tryColUrl('editor/editreviewer.php', $isSpecimens),
                // TODO (Logan) Note is currently disabled in Symbiota repo keeping here for completeness
                // __('misc_collprofiles.ACCESS_REPORT') => tryColUrl('reports/accessreport.php', false),
                __('profile_occurrencemenu.DUP_CLUSTER') => tryColUrl('datasets/duplicatemanager.php', config('portal.activate_duplicates')),
            ];

            $upload_links = [
                __('misc_collprofiles.SKELETAL_FILE_IMPORT') => colUrl('admin/specupload.php', '&uploadtype=7'),
                __('misc_collprofiles.TEXT_FILE_IMPORT') => colUrl('admin/specupload.php', '&uploadtype=3'),
                __('misc_collprofiles.DWCA_IMPORT') => colUrl('admin/specupload.php', '&uploadtype=6'),
                __('misc_collprofiles.IPT_IMPORT') => colUrl('admin/specupload.php', '&uploadtype=8'),
                __('misc_collprofiles.EXTENDED_IMPORT') => colUrl('admin/importextended.php'),
                __('admin_specupload.NFN_IMPORT') => tryColUrl('admin/specupload.php', '&uploadtype=9', $isLive),
                __('misc_collprofiles.IMPORT_PROFILES') => tryColUrl('admin/specuploadmanagement.php', $isLive),
                __('misc_collprofiles.CREATE_PROFILE') => tryColUrl('admin/specuploadmanagement.php', '&action=addprofile', $isLive),
            ];

            $general_maintenance = [
                __('profile_occurrencemenu.DATA_CLEANING') => tryColUrl('cleaning/index.php', $isSpecimens, '&obsuid=0'),
                __('misc_collprofiles.BACKUP_DATA_FILE') => colUrl('collbackup.php'),
                __('misc_collprofiles.RESTORE_BACKUP') => tryColUrl('admin/restorebackup.php', $isLive),
                // TODO (Logan) Commented out in Symbiota repo should this be brought over? I can put it under a config flag
                //__('misc_collprofiles.GUID_MANAGEMENT') => legacy_url('imagelib/admin/igsnmapper.php'),
                __('misc_collprofiles.THUMBNAIL_MAINTENANCE') => legacy_url('imagelib/admin/thumbnailbuilder.php?collid=' . request('collid')),
            ];

            @endphp

            {{-- Data Editor Control Panel --}}
            <div class="flex-grow">
                <div class="font-bold text-xl">{{ __('misc_collprofiles.DAT_EDIT') }}</div>
                <x-list-of-links :links="$data_links" />

                @if($collection->isTraitCodingActivated() && $isSpecimens)
                <div class="font-bold text-lg">{{ __('misc_collprofiles.TRAIT_CODING_TOOLS') }}</div>
                <x-list-of-links :links="$trait_links" />
                @endif
            </div>

            @can('COLL_ADMIN', $collection->collID)
            {{-- Administration Conrol Panel--}}
            <div class="flex-grow">
                <div class="font-bold text-xl">{{ __('misc_collprofiles.ADMIN_CONTROL') }} </div>
                <x-list-of-links :links="$admin_links" />

                <div class="flex items-center gap-2">
                    <div class="font-bold text-lg">{{ __('misc_collprofiles.IMPORT_SPECIMEN') }}</div>
                        <x-question-mark-button
                            target="_blank"
                            href="{{ docs_url('Collection_Manager_Guide/Importing_Uploading/') }}"
                            title="{{__('header.H_MORE_INFO')}}"
                        />
                    </div>
                <x-list-of-links :links="$upload_links" />

                <div class="font-bold text-lg">{{ __('misc_collprofiles.MAINTENANCE_TASKS') }}</div>
                <x-list-of-links :links="$general_maintenance">
                    @csrf
                    <li>
                        <x-link
                            class="cursor-pointer"
                            hx-ext="hx-stream"
                            hx-include="input[name=_token]"
                            hx-patch="{{ url('collections/' . $collection->collID . '/stats') }}"
                            hx-trigger="click"
                            hx-target="#stats-output"
                            x-on:htmx:after-request="setTimeout(() => location.reload(), 1000)"
                            hx-swap="stream"
                            @click="showStatsMessages = true"
                            hx-indicator="#stats-loader"
                        >
                        {{ __('misc_collstats.UPDATE_STATS') }}
                        </x-link>
                    </li>
                </x-list-of-links>
            </div>
            @endcan
        </div>
    </x-accordion>
    @endcan

    <div x-show="showStatsMessages" class="flex flex-col gap-4" >
        <div>
            <div class="font-bold text-xl flex items-center gap-2">
                {{ __('misc_collprofiles.UPDATE_STATISTICS') }}
                <div  id="stats-loader" class="htmx-indicator stroke-accent w-7 h-7">
                    <x-icons.loading/>
                </div>
            </div>
            <hr class="mt-2"/>
        </div>

        <div id="stats-output" class="p-4 bg-base-200"></div>
        <hr/>
    </div>

    <div class="flex flex-col gap-2">
        <p>{!! Purify::clean($collection->fullDescription) !!}</p>

        @isset($collection->resourceJson)
            @if($resourceArr = json_decode($collection->resourceJson, true))
            <div>
                @foreach($resourceArr as $rArr)
                <div>
                    <x-link href="{{ $rArr['url'] }}" target="_blank">{{ $rArr['title'][App::currentLocale()] ?? __('misc_collprofiles.HOMEPAGE') }}</x-link>
                </div>
                @endforeach
            </div>
            @endif
        @endisset

        @php
            $contacts = json_decode($collection->contactJson, true);
        @endphp
        @if(is_array($contacts) && count($contacts))
        <div>
            <div class="text-2xl font-bold">{{ __('header.H_CONTACTS') }}</div>
            @foreach($contacts as $contact)
                @if(isset($contact['firstName']) && isset($contact['lastName']) && isset($contact['email']))
                    <div>
                        <span class="font-bold">{{ $contact['role'] ?? 'Contact' }}:</span>
                        {{ $contact['firstName'] . ' ' . $contact['lastName'] . ' ' . $contact['email'] }}
                    </div>
                @endif
            @endforeach
        </div>
        @endif

        @if(($collection->publishToGbif ?? false) && ($collection->aggKeysStr['datasetKey'] ?? false))
        @php $gbifUrl = 'http://www.gbif.org/dataset/' .  $collection->aggKeysStr['datasetKey'] @endphp
        <x-text-label :label="__('misc_collprofiles.GBIF_DATASET')">
            <x-link :href="$gbifUrl" target="_blank" rel="noopener noreferrer">
                {{ $gbifUrl }}
            </x-link>
        </x-text-label>
        @endif

        {{-- TODO (Logan) double check this link is stable and being used --}}
        @if(($collection->publishToIdigbio ?? false) && ($collection->aggKeysStr['idigbioKey'] ?? false))
        @php $idigBioUrl = 'https://www.idigbio.org/portal/recordsets/' .  $collection->aggKeysStr['datasetKey'] @endphp
        <x-text-label :label="__('misc_collprofiles.IDIGBIO_DATASET')">
            <x-link :href="$idigBioUrl" target="_blank" rel="noopener noreferrer">
                {{ $idigBioUrl }}
            </x-link>
        </x-text-label>
        @endif

        @if(file_exists(legacy_path('/includes/citationcollection.php')))
            <x-text-label label="Cite this collection">
                <blockquote>
				{{-- If GBIF dataset key is available, fetch GBIF format from API --}}
                @if($collection->publishToGbif && ($collection->aggKeysStr['datasetKey'] ?? false) && file_exists(legacy_path('/includes/citationgbif.php')) && false)
                @php
					$gbifUrl = 'http://api.gbif.org/v1/dataset/' . $collection->aggKeysStr['datasetKey'];
					$responseData = json_decode(file_get_contents($gbifUrl));
($collection->aggKeysStr['datasetKey'] ?? false);
					$collData['gbiftitle'] = $responseData->title;
					$collData['doi'] = $responseData->doi;
                    include(legacy_path('/includes/citationgbif.php'));
                @endphp
                @else
                    @php
					    $collData['collectionname'] = $collection->collectionName;
					    $collData['recordid'] = $collection->recordId;
					    $collData['dwcaurl'] = $collection->dwcaUrl;

                        include(legacy_path('/includes/citationcollection.php'))
                    @endphp
                @endif
                </blockquote>
            </x-text-label>
        @endif
    </div>

    <div>
        <div class="text-2xl font-bold">{{ __('misc_collprofiles.COLL_STATISTICS') }}</div>
        <ul class="pl-4">
            <li class="list-disc">
                {{ number_format($stats->georefcnt ?? 0) }} ({{ $stats->recordcnt_percent($stats->georefcnt ?? 0) }}%) {{ __('misc_collstats.GEOREFERENCED') }}
            </li>

            @if($media_stats = $stats->media())
                @if(is_numeric($media_stats['media_count']))
                <li class="list-disc">
                    {{ implode(' ', [
                        number_format($media_stats['media_count']),
                        '(' . $stats->recordcnt_percent($media_stats['media_count']) . '%)',
                        __('misc_collprofiles.WITH_IMAGES'),
                        '(' . number_format($media_stats['total_media_count']) . ' ' . strtolower(__('taxa.TOTAL_IMAGES')) . ')',
                    ]) }}
                </li>
                @endif
            @endif

            @php
                $genbank = $stats->genbank();
                $bold = $stats->bold();
                $genetic = $stats->other_genetic();
            @endphp
            @if($genbank || $bold || $genetic)
            <li class="list-disc">
                @if($genbank)
                {{ number_format($genbank) }} {{ __('misc_collprofiles.GENBANK_REF') }}
                @endif

                @if($bold)
                {{ number_format($bold) }} {{ __('misc_collprofiles.BOLD_REF') }}
                @endif

                @if($genetic)
                {{ number_format($genetic) }} {{ __('misc_collprofiles.OTHER_GENETIC_REF') }}
                @endif
                {{ __('misc_collprofiles.GENETIC_REF') }}
            </li>
            @endif

            @if($ref_cnt = $stats->references())
            <li class="list-disc">
                {{ number_format($ref_cnt) }} {{ __('misc_collprofiles.PUB_REFS') }}
            </li>
            @endif

            @if($spec_cnt = $stats->specimen())
            <li class="list-disc">
                {{ number_format($spec_cnt) }} ({{ $stats->recordcnt_percent($spec_cnt) }}%) {{ __('misc_collprofiles.SPECIMEN_RECORDS') }}
            </li>
            @endif

            <li class="list-disc lowercase">{{ number_format($stats->familycnt ?? 0) }} {{ __('checklists_checklist.FAMILIES') }}</li>
            <li class="list-disc lowercase">{{ number_format($stats->genuscnt ?? 0) }} {{ __('checklists_checklist.GENERA') }}</li>
            <li class="list-disc lowercase">{{ number_format($stats->speciescnt ?? 0) }} {{ __('checklists_checklist.SPECIES') }}</li>
            @if($total_taxa_cnt = $stats->total_taxa())
                <li class="list-disc">{{ number_format($total_taxa_cnt) }} {{ __('misc_collprofiles.TOTAL_TAXA_INCLUDING') }}</li>
            @endif
        </ul>
    </div>

    <div class="flex items-center gap-2">
        <x-nav-link hx-boost="true" href="{{ url('collections/search?collId=' . $collection->collID) }}">
            <x-button>{{ __('misc_collprofiles.ADVANCED_SEARCH_THIS_COLLECTION') }}</x-button>
        </x-nav-link>

        <x-nav-link hx-boost="true" href="{{ url('media/search?collId=' . $collection->collID) }}">
            <x-button>{{ __('misc_collprofiles.MEDIA_SEARCH_THIS_COLLECTION') }}</x-button>
        </x-nav-link>
    </div>

    <x-accordion :label="__('checklists_dynamicmap.MORE_DETAILS')" :open="true" variant="clear-primary">
        <x-text-label :label="__('misc_collprofiles.COLLECTION_TYPE')">
            {{ $collection->collType }}
        </x-text-label>

        <x-text-label :label="__('misc_collmetadata.MANAGEMENT')">
        @switch($collection->managementType)
            @case('Live Data')
                {{ __('misc_collmetadata.LIVE_DATA') }}
                @break
            @case('Aggregate')
                {{ __('misc_collprofiles.DATA_AGGREGATE') }}
                @break
            @default
                {{ __('misc_collprofiles.DATA_SNAPSHOT') }}
        @endswitch
        </x-text-label>

        @if($collection->managementType != 'Live Data')
        <x-text-label :label="__('misc_collprofiles.LAST_UPDATE')">
            {{ $stats->datelastmodified }}
        </x-text-label>
        @endif

        @if($collection->dwcaUrl)
        <x-link :href="$collection->dwcaUrl">{{ __('misc_collprofiles.DWCA_PUB') }}</x-link>
        @endif

		@if($collection->managementType == 'Live Data')
        <x-text-label :label="__('misc_collprofiles.GLOBAL_UNIQUE_ID')">
            {{ $collection->collectionGuid }}
        </x-text-label>
		@endif

		@if($collection->managementType == 'Snapshot')
        <x-text-label :label="__('misc_collprofiles.IPT_SOURCE')">
            @foreach($collection->dwcaPaths() as $path)
            <x-link href="{{ $path->path }}">{{ $path->title }}</x-link>@if(!$loop->last){{'|'}}@endif
            @endforeach
        </x-text-label>
		@endif

        <x-text-label :label="__('misc_collprofiles.DIGITAL_METADATA')">
            <x-link :href="colUrl('datasets/emlhandler.php')" target="_blank">
                {{__('misc_collprofiles.EML_FILE') }}
            </x-link>
        </x-text-label>

        @if($collection->rights)
        <x-text-label :label="__('misc_collmetadata.LICENSE')">
            <div class="w-32">
            {!! Purify::clean(GeneralUtil::getRightsHtml($collection->rights)) !!}
            </div>
        </x-text-label>
        @else
        <div>
			<x-link href="{{ url('usagepolicy') }}" target="_blank">{{ __('misc_collprofiles.USAGE_POLICY') }}</x-link>
		</div>
        @endif

        <x-text-label :label="__('individual.RIGHTS_HOLDER')">
            {{ $collection->rightsHolder }}
        </x-text-label>

        <x-text-label :label="__('individual.ACCESS_RIGHTS')">
            {{ $collection->accessRights }}
        </x-text-label>
    </x-accordion>

    @if(isset($stats->dynamicProperties->families) || isset($stats->dynamicProperties->countries))
    @php
        $fam_georef_stats = array();

        if(isset($stats->dynamicProperties->families)) {
            foreach($stats->dynamicProperties->families as $key => $item) {
                if(is_numeric($item->SpecimensPerFamily)) {
                    $fam_georef_stats[] = [
                        'label' => $key,
                        'value' => intval($item->SpecimensPerFamily)
                    ];
                }
            }
        }

        $country_georef_stats = array();
        if(isset($stats->dynamicProperties->countries)) {
            foreach($stats->dynamicProperties->countries as $key => $item) {
                if(is_numeric($item->CountryCount)) {
                    $country_georef_stats[] = [
                        'label' => $key,
                        'value' => intval($item->CountryCount)
                    ];
                }
            }
        }

        function calc_chart_width($item_count, $per_item = 16) {
            $width = $item_count * $per_item;

            return $width < 600? '': $width;
        }

        function valueCmp(array $a, array $b): int {
            if($b['value'] > $a['value']) return 1;
            else if($b['value'] < $a['value']) return -1;
            else return 0;
        }

        usort($country_georef_stats, 'valueCmp');
        usort($fam_georef_stats, 'valueCmp');

        $stats_tabs = [];
        if(isset($stats->dynamicProperties->families)) {
            $stats_tabs[] = __('misc_collprofiles.TAXON_DIST');

        }

        if(isset($stats->dynamicProperties->countries)) {
            $stats_tabs[] = __('misc_collstats.GEO_DIST');
        }
    @endphp

    <div>
        <div class="text-2xl font-bold">{{ __('misc_collstats.EXTRA_STATS') }}</div>

        <x-tabs :tabs="$stats_tabs" class:body="border-x-0 border-b-0">
            @isset($stats->dynamicProperties->families)
                <x-chart name="Taxon Distribution" type="bar" width="{{ calc_chart_width(count($fam_georef_stats)) }}" height="600" class="w-full pb-4" :values="$fam_georef_stats" />
            @endisset

            @isset($stats->dynamicProperties->countries)
                <x-chart name="Geographic Distribution" type="bar" width="{{calc_chart_width(count($country_georef_stats)) }}" height="600" class="w-full pb-4" :values="$country_georef_stats"/>
            @endisset
        </x-tabs>
    </div>
    @endif
</x-margin-layout>
