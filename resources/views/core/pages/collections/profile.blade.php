@props(['collection', 'stats'])
@php
function colUrl($url, $extra_query = '') {
    return legacy_url('/collections/' . $url) . '?collid=' . request('collid') . $extra_query;
}
@endphp
<x-layout class="flex flex-col gap-4">
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
            <x-button>Search Collection</x-button>
        </x-nav-link>

        <x-nav-link hx-boost="true" href="{{ url('media/search?collId=' . $collection->collID) }}">
            <x-button>Search Media</x-button>
        </x-nav-link>

        <x-modal>
            <x-slot name='button' variant="clear-primary">
                Quick Search
            </x-slot>

            <x-slot name="title" class="text-2xl">
                Quick Search
            </x-slot>

            <x-slot name="body">
                <form hx-get="{{ url('collections/table') }}" hx-target="body" hx-indicator="#quick-search-loader" class="flex flex-col gap-2">
                    <input type="hidden" name="collid" value="{{ $collection->collID }}">
                    <x-input name="catalogNumber" label="Catalog Number" required />
                    <div class="flex items-center">
                        <x-button type="submit">
                            Search
                        </x-button>

                        <div id="quick-search-loader" class="stroke-accent w-6 h-6 flex justify-center htmx-indicator">
                            <x-icons.loading />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-modal>

        @can('COLL_EDIT', $collection->collID)
        <x-nav-link hx-boost="true" href="{{ url('collections/table?collid=' . $collection->collID) }}">
            <x-button>Edit</x-button>
        </x-nav-link>
        @endcan
    </div>
    <p>{!! Purify::clean($collection->fullDescription) !!}</p>

    @can('COLL_EDIT', $collection->collid)
    <x-accordion label="Manager Control Panel" open="true">
        <div class="flex gap-2">
            @php
            $data_links = [
                colUrl('editor/occurrenceeditor.php', '&gotomode=1') => 'Add New Occurrence Record',
                colUrl('editor/imageoccursubmit.php') => 'Add Skeletal Records',
                colUrl('editor/skeletalsubmit.php') => 'Add New Occurrence Record',
                colUrl('editor/occurrencetabledisplay.php', '&displayquery=1') => 'Edit Existing Occurrence Records',
                colUrl('editor/batchdeterminations.php') => 'Add Batch Determinations/Nomenclatural Adjustments',
                colUrl('reports/labelmanager.php') => 'Print specimen Labels',
                colUrl('reports/annotationmanager.php') => 'Print Annotation Labels',
                colUrl('georef/batchgeoreftool.php') => 'Batch Georeference Specimens',
                colUrl('loans/index.php') => 'Loan Management',
            ];

            $trait_links = [
                colUrl('traitattr/occurattributes.php') => 'Trait Coding from Images',
                colUrl('traitattr/attributemining.php') => 'Trait Mining from Verbatim Text',
            ];
            $admin_links = [
                colUrl('misc/commentlist.php') => 'View Posted Comments',
                colUrl('misc/collmetadata.php') => 'Edit Meta Data',
                colUrl('misc/collpermissions.php') => 'Import/Update Specimen Records',
                colUrl('specprocessor/index.php') => 'Processing Toolbox',
                colUrl('datasets/datapublisher.php') => 'Darwin Core Archive Publishing',
                colUrl('editor/editreviewer.php') => 'Review/Verify Occurrence Edits',
                colUrl('datasets/duplicatemanager.php') => 'Duplicate Clustering',
            ];

            $upload_links = [
                colUrl('admin/specupload.php', '&uploadtype=7') => 'Skeletal Text File Import',
                colUrl('admin/specupload.php', '&uploadtype=3') => 'Full Text File Import',
                colUrl('admin/specupload.php', '&uploadtype=6') => 'DwC-Archive Import',
                colUrl('admin/specupload.php', '&uploadtype=8') => 'IPT Import',
                colUrl('admin/importextended.php') => 'Extended Data Import',
                colUrl('admin/specupload.php', '&uploadtype=9') => 'Notes from Nature Import',
                colUrl('admin/specuploadmanagement.php') => 'Saved Import Profiles',
                colUrl('admin/specuploadmanagement.php', '&action=addprofile') => 'Create a new Import Profile',
            ];

            @endphp

            {{-- Data Editor Control Panel --}}
            <div class="flex-grow">
                <div class="font-bold text-xl">Data Editor</div>
                <ul class="pl-4">
                    @foreach ($data_links as $link => $title)
                        <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>
                <div class="font-bold text-lg">Occurrence Trait Coding Tools</div>
                <ul class="pl-4">
                    @foreach ($trait_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>
            </div>

            @can('COLL_ADMIN', $collection->collid)
            {{-- Administration Conrol Panel--}}
            <div class="flex-grow">
                <div class="font-bold text-xl">Administration</div>
                <ul class="pl-4">
                    @foreach ($admin_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach

                </ul>
                <div class="font-bold text-lg">Import/Update Specimen Records</div>
                <ul class="pl-4">
                    @foreach ($upload_links as $link => $title)
                    <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </ul>
            </div>
            @endcan
        </div>
    </x-accordion>
    @endcan

    <div>
        @php
            $contacts = json_decode($collection->contactJson, true);
        @endphp
        @if(is_array($contacts) && count($contacts))
            <div class="text-2xl font-bold">Contacts</div>
            @foreach($contacts as $contact)
                @if(isset($contact['firstName']) && isset($contact['lastName']) && isset($contact['email']))
                    <div>
                        <span class="font-bold">{{ $contact['role'] ?? 'Contact' }}:</span>
                        {{ $contact['firstName'] . ' ' . $contact['lastName'] . ' ' . $contact['email'] }}
                    </div>
                @endif
            @endforeach
        @endif
    </div>


    @php
        $dynamic_stats = $stats->dynamicProperties? json_decode($stats->dynamicProperties, true): [];
    @endphp
    <div>
        <div class="text-2xl font-bold">Collection Statistics</div>
        <ul class="pl-4">
            <li class="list-disc">{{ $stats->recordcnt ?? 0 }} specimen records</li>
            <li class="list-disc">{{ $stats->georefcnt ?? 0 }} ({{ $stats->georefcnt? floor( ($stats->georefcnt / $stats->recordcnt) * 100): 0 }}%) georeferenced</li>
            @isset($dynamic_stats['SpecimensCountID'])
                <li class="list-disc">{{ $dynamic_stats['SpecimensCountID'] }} ({{ $dynamic_stats['SpecimensCountID']? floor( ($dynamic_stats['SpecimensCountID'] / $stats->recordcnt) * 100) : 0}}%) identified to species</li>
            @endisset
            <li class="list-disc">{{ $stats->familycnt ?? 0 }} families</li>
            <li class="list-disc">{{ $stats->genuscnt ?? 0 }} genera</li>
            <li class="list-disc">{{ $stats->speciescnt ?? 0 }} species</li>
            @isset($dynamic_stats['TotalTaxaCount'])
                <li class="list-disc">{{ $dynamic_stats['TotalTaxaCount'] }} total taxa (including subsp. and var.)</li>
            @endisset
        </ul>
    </div>

    <x-accordion label="More Information">
        <div><span class="font-bold">Collection Type:</span> {{ $collection->collType }}</div>
        <div><span class="font-bold">Management:</span> {{ $collection->managementType }}</div>
        @if($collection->managementType != 'Live Data')
        <div><span class="font-bold">Last Update:</span> {{ $stats->uploaddate }}</div>
        @endif
        <div><span class="font-bold">Digital Metadata:</span> <x-link href="{{colUrl('datasets/emlhandler.php')}}">EML File</x-link></div>
        <div><span class="font-bold">IPT / DwC-A Source:</span> <x-link href="{{ $collection->path }} ">{{ $collection->title }}</x-link></div>
        <div><span class="font-bold">Usage Rights:</span>
            <a href="{{ $collection->rights }}">
                <img class="w-32" src="https://mirrors.creativecommons.org/presskit/buttons/88x31/png/by-nd.png"/>
            </a>
        </div>
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
            $stats_tabs[] = 'Taxonomic Distribution';
        }

        if(isset($dynamic_stats['countries'])) {
            $stats_tabs[] = 'Country Distribution';
        }
    @endphp

    <div>
        <div class="text-2xl font-bold">Extra Statistics</div>

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
</x-layout>
