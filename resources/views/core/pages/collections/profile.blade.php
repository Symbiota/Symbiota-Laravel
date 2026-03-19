@props(['collection', 'stats'])
@php
global $LANG, $LANG_TAG;
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('collections/misc/collprofiles');

function colUrl($url, $extra_query = '') {
    return legacy_url('/collections/' . $url) . '?collid=' . request('collid') . $extra_query;
}
@endphp
<x-margin-layout>
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('')],
        ['title' => 'Collection Search Page', 'href' => url('collections/search'), ],
        ['title' => 'Collection Profile']
        ]" />

    <div class="flex items-center gap-4">
        @isset($collection->icon)
            <img class="h-20"src="{{ $collection->icon }}"/>
        @endisset
        <span class="text-4xl font-bold">{{ $collection->collectionName}}</span>
    </div>

    <div class="flex items-center gap-2">
        <x-nav-link hx-boost="true" href="{{ url('collections/search?collId=' . $collection->collID) }}">
                {{-- TODO (Logan) Translations --}}
            <x-button>Search Collection</x-button>
        </x-nav-link>

        <x-nav-link hx-boost="true" href="{{ url('media/search?collId=' . $collection->collID) }}">
            {{-- TODO (Logan) Translations --}}
            <x-button>Search Media</x-button>
        </x-nav-link>

        <x-modal>
            <x-slot name='button' variant="clear-primary">
                {{ $LANG['QUICK_SEARCH'] }}
            </x-slot>

            <x-slot name="title" class="text-2xl">
                {{ $LANG['QUICK_SEARCH'] }}
            </x-slot>

            <x-slot name="body">
                <form hx-get="{{ url('collections/table') }}" hx-target="body" hx-push-url="true" hx-indicator="#quick-search-loader" class="flex flex-col gap-2">
                    <input type="hidden" name="collid" value="{{ $collection->collID }}">
                    <x-input name="catalogNumber" x-effect="if(modalOpen) $focus.focus($el)" label="Catalog Number" required />

                    <div class="flex items-center">
                        <x-button type="submit">{{ $LANG['SEARCH'] }}</x-button>
                        <div id="quick-search-loader" class="stroke-accent w-6 h-6 flex justify-center htmx-indicator">
                            <x-icons.loading />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-modal>

        @can('COLL_EDIT', $collection->collID)
        <x-nav-link hx-boost="true" href="{{ url('collections/table?collid=' . $collection->collID) }}">
            <x-button>{{ $LANG['OCCURRENCE_EDITOR'] }}</x-button>
        </x-nav-link>
        @endcan
    </div>

    @can('COLL_EDIT', $collection->collid)
    <x-accordion :label="$LANG['TOGGLE_MAN']" open="true">
        <div class="flex flex-wrap gap-2">
            @php
            $data_links = [
                colUrl('editor/occurrenceeditor.php', '&gotomode=1') => $LANG['ADD_NEW_OCCUR'],

                // TODO (Logan) exlcude if colltype doesn't have "Specimens" in it
                colUrl('editor/imageoccursubmit.php') => $LANG['CREATE_NEW_REC'],
                colUrl('editor/skeletalsubmit.php') => $LANG['SKELETAL'],

                colUrl('editor/occurrencetabledisplay.php', '&displayquery=1') => $LANG['EDIT_EXISTING'],
                // TODO (Logan) exclude if colltype general observations
                colUrl('editor/batchdeterminations.php') => $LANG['ADD_BATCH_DETER'],

                colUrl('reports/labelmanager.php') => $LANG['PRINT_LABELS'],
                colUrl('reports/annotationmanager.php') => $LANG['PRINT_ANNOTATIONS'],

                // TODO (Logan) exclude if colltype general observations
                colUrl('georef/batchgeoreftool.php') => $LANG['BATCH_GEOREF'],
                // TODO (Logan) only "Preserved Specimens"
                colUrl('loans/index.php') => $LANG['LOAN_MANAGEMENT'],
            ];

            // TODO (Logan) exclude if colltype general observations. Also traits activated
            $trait_links = [
                colUrl('traitattr/occurattributes.php') => $LANG['TRAIT_CODING'],
                colUrl('traitattr/attributemining.php') => $LANG['TRAIT_MINING'],
            ];

            $admin_links = [
                colUrl('misc/commentlist.php') => $LANG['VIEW_COMMENTS'],
                colUrl('misc/collmetadata.php') => $LANG['EDIT_META'],
                colUrl('misc/collpermissions.php') => $LANG['MANAGE_PERMISSIONS'],
                colUrl('specprocessor/index.php') => $LANG['PROCESSING_TOOLBOX'],
                colUrl('datasets/datapublisher.php') => $LANG['DARWIN_CORE_PUB'],
                colUrl('editor/editreviewer.php') => $LANG['REVIEW_SPEC_EDITS'],
                // TODO (Logan) figure out why commented out in old code
                // colUrl('reports/accessreport.php') => $LANG['ACCESS_REPORT'],
                // TODO (Logan) !empty($ACTIVATE_DUPLICATES)
                colUrl('datasets/duplicatemanager.php') => $LANG['DUP_CLUSTER'],
            ];

            $upload_links = [
                colUrl('admin/specupload.php', '&uploadtype=7') => $LANG['SKELETAL_FILE_IMPORT'],
                colUrl('admin/specupload.php', '&uploadtype=3') => $LANG['TEXT_FILE_IMPORT'],
                colUrl('admin/specupload.php', '&uploadtype=6') => $LANG['DWCA_IMPORT'],
                colUrl('admin/specupload.php', '&uploadtype=8') => $LANG['IPT_IMPORT'],
                colUrl('admin/importextended.php') => $LANG['EXTENDED_IMPORT'],
                // TODO (Logan) live data only
                colUrl('admin/specupload.php', '&uploadtype=9') => $LANG['NFN_IMPORT'],
                colUrl('admin/specuploadmanagement.php') => $LANG['IMPORT_PROFILES'],
                colUrl('admin/specuploadmanagement.php', '&action=addprofile') => $LANG['CREATE_PROFILE'],
            ];

            $general_maintenance = [
                colUrl('cleaning/index.php', '&obsuid=0') => $LANG['DATA_CLEANING'],
                colUrl('collbackup.php') => $LANG['BACKUP_DATA_FILE'],
                // TODO (Logan) only live data
                colUrl('admin/restorebackup.php') => $LANG['RESTORE_BACKUP'],
                // TODO (Logan) figure out why commented out in old code?
                // legacy_url('imagelib/admin/igsnmapper.php') => $LANG['GUID_MANAGEMENT'],
                legacy_url('imagelib/admin/thumbnailbuilder.php?collid=' . request('collid')) => $LANG['THUMBNAIL_MAINTENANCE'],
                colUrl('misc/collprofiles.php', '&action=UpdateStatistics') => $LANG['UPDATE_STATS'],
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
                <div class="font-bold text-lg">{{ $LANG['TRAIT_CODING_TOOLS'] }}</div>
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

                <div class="font-bold text-lg">{{ $LANG['IMPORT_SPECIMEN'] }}</div>
                {{-- TODO (Logan) ? mark button <x-link
                    target="_blank"
                    href="{{ docs_url('Collection_Manager_Guide/Importing_Uploading/') }}">
                    {{ $LANG['MORE_INFO'] }}
                </x-link>
                --}}
                <ul class="pl-4">
                    @foreach ($upload_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>

                <div class="font-bold text-lg">{{ $LANG['MAINTENANCE_TASKS'] }}</div>
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
                    <x-link href="{{ $rArr['url'] }}" target="_blank">{{ $rArr['title'][$LANG_TAG] ?? $LANG['HOMEPAGE'] }}</x-link>
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
            <div class="text-2xl font-bold">{{ $LANG['CONTACT'] }}</div>
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
    </div>


    @php
        $dynamic_stats = $stats->dynamicProperties? json_decode($stats->dynamicProperties, true): [];
    @endphp
    <div>
        <div class="text-2xl font-bold">{{ $LANG['COLL_STATISTICS'] }}</div>
        <ul class="pl-4">
            <li class="list-disc">{{ $stats->recordcnt ?? 0 }} {{ $LANG['SPECIMEN_RECORDS'] }}</li>
            <li class="list-disc">{{ $stats->georefcnt ?? 0 }} ({{ $stats->georefcnt? floor( ($stats->georefcnt / $stats->recordcnt) * 100): 0 }}%) {{ $LANG['GEOREFERENCED'] }}</li>
            @isset($dynamic_stats['SpecimensCountID'])
                <li class="list-disc">{{ $dynamic_stats['SpecimensCountID'] }} ({{ $dynamic_stats['SpecimensCountID']? floor( ($dynamic_stats['SpecimensCountID'] / $stats->recordcnt) * 100) : 0}}%) {{ $LANG['IDED_TO_SPECIES'] }}</li>
            @endisset
            <li class="list-disc">{{ $stats->familycnt ?? 0 }} {{ $LANG['FAMILIES'] }}</li>
            <li class="list-disc">{{ $stats->genuscnt ?? 0 }} {{ $LANG['GENERA'] }}</li>
            <li class="list-disc">{{ $stats->speciescnt ?? 0 }} {{ $LANG['SPECIES'] }}</li>
            @isset($dynamic_stats['TotalTaxaCount'])
                <li class="list-disc">{{ $dynamic_stats['TotalTaxaCount'] }} {{ $LANG['TOTAL_TAXA'] }}</li>
            @endisset
        </ul>
    </div>

    <x-accordion label="More Information">
        <x-text-label :label="$LANG['COLLECTION_TYPE']">
            {{ $collection->collType }}
        </x-text-label>

        <x-text-label :label="$LANG['MANAGEMENT']">
            {{ $collection->managementType }}
        </x-text-label>

        @if($collection->managementType != 'Live Data')
        <x-text-label :label="$LANG['LAST_UPDATE']">
            {{ $stats->datelastmodified }}
        </x-text-label>
        @endif

        @if($collection->dwcaUrl)
        <x-link :href="$collection->dwcaUrl">{{ $LANG['DWCA_PUB'] }}</x-link>
        @endif

        <x-text-label :label="$LANG['DIGITAL_METADATA']">
            <x-link :href="colUrl('datasets/emlhandler.php')" target="_blank">
                EML File
            </x-link>
        </x-text-label>

        <x-text-label :label="$LANG['IPT_SOURCE']">
            <x-link href="#todo">{{ $collection->title }}</x-link>
        </x-text-label>

        @if($collection->rights)
        <x-text-label :label="$LANG['LICENSE']">
            {{-- TODO (Logan) GeneralUtil::getRightsHtml --}}
            <img class="w-32" src="https://mirrors.creativecommons.org/presskit/buttons/88x31/png/by-nd.png"/>
        </x-text-label>
        @endif
    </x-accordion>

    @if(isset($dynamic_stats['families']) || isset($dynamic_stats['countries']))
    @php
        $fam_georef_stats = [];

        if(isset($dynamic_stats['families'])) {
            foreach($dynamic_stats['families'] as $key => $item) {
                if(is_numeric($item['SpecimensPerFamily'])) {
                    $fam_georef_stats[] = [
                        'label' => $key,
                        'value' => intval($item['SpecimensPerFamily'])
                    ];
                }
            }
        }

        $country_georef_stats = [];
        if(isset($dynamic_stats['countries'])) {
            foreach($dynamic_stats['countries'] as $key => $item) {
                if(is_numeric($item['CountryCount'])) {
                    $country_georef_stats[] = [
                        'label' => $key,
                        'value' => intval($item['CountryCount'])
                    ];
                }
            }
        }

        function valueCmp($a, $b) {
            return $b['value'] - $a['value'];
        }

        function calc_chart_width($item_count, $per_item = 16) {
            $width = $item_count * $per_item;

            return $width < 600? '': $width;
        }

        usort($country_georef_stats, 'valueCmp');
        usort($fam_georef_stats, 'valueCmp');

        $stats_tabs = [];
        if(isset($dynamic_stats['families'])) {
            $stats_tabs[] = $LANG['TAXON_DIST'];

        }

        if(isset($dynamic_stats['countries'])) {
            $stats_tabs[] = $LANG['GEO_DIST'];
        }
    @endphp

    <div>
        <div class="text-2xl font-bold">{{ $LANG['EXTRA_STATS'] }}</div>

        <x-tabs :tabs="$stats_tabs" class:body="border-x-0 border-b-0">
            @isset($dynamic_stats['families'])
                <x-chart name="Taxon Distribution" type="bar" width="{{ calc_chart_width(count($fam_georef_stats)) }}" height="600" class="w-full pb-4" :values="$fam_georef_stats" />
            @endisset

            @isset($dynamic_stats['countries'])
                <x-chart name="Geographic Distribution" type="bar" width="{{calc_chart_width(count($dynamic_stats['countries'])) }}" height="600" class="w-full pb-4" :values="$country_georef_stats"/>
            @endisset
        </x-tabs>
    </div>
    @endif
</x-margin-layout>
