@props(['collection', 'stats'])
@php
global $DEFAULT_TITLE, $SERVER_HOST, $CLIENT_ROOT;
include_once(legacy_path('/classes/utilities/GeneralUtil.php'));

function colUrl($url, $extra_query = '') {
    return legacy_url('/collections/' . $url) . '?collid=' . request('collid') . $extra_query;
}
@endphp
<x-margin-layout>
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

    @can('COLL_EDIT', $collection->collid)
    <x-accordion :label="__('misc_collprofiles.TOGGLE_MAN')" :open="true" variant="clear-primary">
        <div class="flex flex-wrap gap-2">
            @php
            $data_links = [
                colUrl('editor/occurrenceeditor.php', '&gotomode=1') => __('misc_collprofiles.ADD_NEW_OCCUR'),

                // TODO (Logan) exlcude if colltype doesn't have "Specimens" in it
                colUrl('editor/imageoccursubmit.php') => __('misc_collprofiles.CREATE_NEW_REC'),
                colUrl('editor/skeletalsubmit.php') => __('misc_collprofiles.SKELETAL'),

                colUrl('editor/occurrencetabledisplay.php', '&displayquery=1') => __('misc_collprofiles.EDIT_EXISTING'),
                // TODO (Logan) exclude if colltype general observations
                colUrl('editor/batchdeterminations.php') => __('misc_collprofiles.ADD_BATCH_DETER'),

                colUrl('reports/labelmanager.php') => __('profile_occurrencemenu.PRINT_LABELS'),
                colUrl('reports/annotationmanager.php') => __('profile_occurrencemenu.PRINT_ANNOTATIONS'),

                // TODO (Logan) exclude if colltype general observations
                colUrl('georef/batchgeoreftool.php') => __('misc_collprofiles.BATCH_GEOREF'),
                // TODO (Logan) only "Preserved Specimens"
                colUrl('loans/index.php') => __('misc_collprofiles.LOAN_MANAGEMENT'),
            ];

            // TODO (Logan) exclude if colltype general observations. Also traits activated
            $trait_links = [
                colUrl('traitattr/occurattributes.php') => __('misc_collprofiles.TRAIT_CODING'),
                colUrl('traitattr/attributemining.php') => __('misc_collprofiles.TRAIT_MINING'),
            ];

            $admin_links = [
                colUrl('misc/commentlist.php') => __('profile_occurrencemenu.VIEW_COMMENTS'),
                colUrl('misc/collmetadata.php') => __('misc_collmetadata.EDIT_METADATA'),
                colUrl('misc/collpermissions.php') => __('misc_collprofiles.MANAGE_PERMISSIONS'),
                colUrl('specprocessor/index.php') => __('misc_collprofiles.PROCESSING_TOOLBOX'),
                colUrl('datasets/datapublisher.php') => __('misc_collprofiles.DARWIN_CORE_PUB'),
                colUrl('editor/editreviewer.php') => __('misc_collprofiles.REVIEW_SPEC_EDITS'),
                // TODO (Logan) figure out why commented out in old code
                // colUrl('reports/accessreport.php') => __('misc_collprofiles.ACCESS_REPORT'),
                // TODO (Logan) !empty($ACTIVATE_DUPLICATES)
                colUrl('datasets/duplicatemanager.php') => __('profile_occurrencemenu.DUP_CLUSTER'),
            ];

            $upload_links = [
                colUrl('admin/specupload.php', '&uploadtype=7') => __('misc_collprofiles.SKELETAL_FILE_IMPORT'),
                colUrl('admin/specupload.php', '&uploadtype=3') => __('misc_collprofiles.TEXT_FILE_IMPORT'),
                colUrl('admin/specupload.php', '&uploadtype=6') => __('misc_collprofiles.DWCA_IMPORT'),
                colUrl('admin/specupload.php', '&uploadtype=8') => __('misc_collprofiles.IPT_IMPORT'),
                colUrl('admin/importextended.php') => __('misc_collprofiles.EXTENDED_IMPORT'),
                // TODO (Logan) live data only
                colUrl('admin/specupload.php', '&uploadtype=9') => __('admin_specupload.NFN_IMPORT'),
                colUrl('admin/specuploadmanagement.php') => __('misc_collprofiles.IMPORT_PROFILES'),
                colUrl('admin/specuploadmanagement.php', '&action=addprofile') => __('misc_collprofiles.CREATE_PROFILE'),
            ];

            $general_maintenance = [
                colUrl('cleaning/index.php', '&obsuid=0') => __('profile_occurrencemenu.DATA_CLEANING'),
                colUrl('collbackup.php') => __('misc_collprofiles.BACKUP_DATA_FILE'),
                // TODO (Logan) only live data
                colUrl('admin/restorebackup.php') => __('misc_collprofiles.RESTORE_BACKUP'),
                // TODO (Logan) figure out why commented out in old code?
                // legacy_url('imagelib/admin/igsnmapper.php') => __('misc_collprofiles.GUID_MANAGEMENT'),
                legacy_url('imagelib/admin/thumbnailbuilder.php?collid=' . request('collid')) => __('misc_collprofiles.THUMBNAIL_MAINTENANCE'),
                colUrl('misc/collprofiles.php', '&action=UpdateStatistics') => __('misc_collstats.UPDATE_STATS'),
            ];

            @endphp

            {{-- Data Editor Control Panel --}}
            <div class="flex-grow">
                {{-- TODO (Logan) Translations --}}
                <div class="font-bold text-xl">Data Editor</div>
                <ul class="pl-4">
                    @foreach ($data_links as $link => $title)
                        <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>
                <div class="font-bold text-lg">{{ __('misc_collprofiles.TRAIT_CODING_TOOLS') }}</div>
                <ul class="pl-4">
                    @foreach ($trait_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>
            </div>

            @can('COLL_ADMIN', $collection->collid)
            {{-- Administration Conrol Panel--}}
            <div class="flex-grow">
                {{-- TODO (Logan) Translations --}}
                <div class="font-bold text-xl">Administration</div>
                <ul class="pl-4">
                    @foreach ($admin_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach

                </ul>

                <div class="font-bold text-lg">{{ __('misc_collprofiles.IMPORT_SPECIMEN') }}</div>
                {{-- TODO (Logan) ? mark button <x-link
                    target="_blank"
                    href="{{ docs_url('Collection_Manager_Guide/Importing_Uploading/') }}">
                    {{ __('misc_collprofiles.MORE_INFO') }}
                </x-link>
                --}}
                <ul class="pl-4">
                    @foreach ($upload_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>

                <div class="font-bold text-lg">{{ __('misc_collprofiles.MAINTENANCE_TASKS') }}</div>
                <ul class="pl-4">
                    @foreach ($general_maintenance as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>
            </div>
            @endcan
        </div>
    </x-accordion>
    @endcan

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
            <div class="text-2xl font-bold">{{ __('misc_collmetadata.CONTACT') }}</div>
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
                    // TODO (Logan) create laravel template
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
            <li class="list-disc">{{ $stats->recordcnt ?? 0 }} {{ __('misc_collprofiles.SPECIMEN_RECORDS') }}</li>
            <li class="list-disc">{{ $stats->georefcnt ?? 0 }} ({{ $stats->georefcnt? floor( ($stats->georefcnt / $stats->recordcnt) * 100): 0 }}%) {{ __('misc_collstats.GEOREFERENCED') }}</li>
            @if($specimensCount = $stats->dynamicProperties->SpecimensCountID ?? false)
                <li class="list-disc">{{ $specimensCount }} ({{ $specimensCount? floor( ($specimensCount / $stats->recordcnt) * 100) : 0}}%) {{ __('misc_collprofiles.IDED_TO_SPECIES') }}</li>
            @endif
            {{-- TODO (Logan) media image counts --}}
            <li class="list-disc lowercase">{{ $stats->familycnt ?? 0 }} {{ __('checklists_checklist.FAMILIES') }}</li>
            <li class="list-disc lowercase">{{ $stats->genuscnt ?? 0 }} {{ __('checklists_checklist.GENERA') }}</li>
            <li class="list-disc lowercase">{{ $stats->speciescnt ?? 0 }} {{ __('checklists_checklist.SPECIES') }}</li>
            @if($totalTaxaCount = $stats->dynamicProperties->TotalTaxaCount ?? false)
                <li class="list-disc lowercase">{{ $totalTaxaCount }} {{ __('checklists_checklist.TOTAL_TAXA') }}</li>
            @endif
        </ul>
    </div>

    <div class="flex items-center gap-2">
        <x-nav-link hx-boost="true" href="{{ url('collections/search?collId=' . $collection->collID) }}">
            {{-- TODO (Logan) Translations --}}
            <x-button>{{ __('misc_collprofiles.ADVANCED_SEARCH_THIS_COLLECTION') }}</x-button>
        </x-nav-link>

        <x-nav-link hx-boost="true" href="{{ url('media/search?collId=' . $collection->collID) }}">
            {{-- TODO (Logan) Translations --}}
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
            <x-link href="#todo">{{ $collection->title }}</x-link>
        </x-text-label>
		@endif

        <x-text-label :label="__('misc_collprofiles.DIGITAL_METADATA')">
            <x-link :href="colUrl('datasets/emlhandler.php')" target="_blank">
                {{-- TODO (Logan) translation (note) there is not a transferable one --}}
                EML File
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
