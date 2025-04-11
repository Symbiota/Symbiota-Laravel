@props(['collection', 'stats'])
@php
function colUrl($url, $extra_query = '') {
    return url(config('portal.name') . '/collections/' . $url) . '?collid=' . request('collid') . $extra_query;
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
    </div>
    <p>{!! Purify::clean($collection->fullDescription) !!}</p>

    <x-accordion label="Manager Control Panel" open="true">
        <div class="flex gap-2">
            {{-- Data Editor Control Panel --}}
            <div class="flex-grow">
                <div class="font-bold text-xl">Data Editor</div>
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
                    colUrl('misc/commentlist.php') => 'Processing Toolbox',
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
                <div>
                    @foreach ($data_links as $link => $title)
                    <li><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach

                    <div class="font-bold text-lg">Occurrence Trait Coding Tools</div>
                    @foreach ($trait_links as $link => $title)
                    <li><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </div>
            </div>

            {{-- Administration Conrol Panel--}}
            <div class="flex-grow">
                <div class="font-bold text-xl">Administration</div>
                <div>
                    @foreach ($admin_links as $link => $title)
                    <li><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach

                    <div class="font-bold text-lg">Import/Update Specimen Records</div>
                    @foreach ($upload_links as $link => $title)
                    <li><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </div>
            </div>
        </div>
    </x-accordion>

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
        <li>{{ $stats->recordcnt ?? 0 }} specimen records</li>
        <li>{{ $stats->georefcnt ?? 0 }} ({{ floor( ($stats->georefcnt / $stats->recordcnt) * 100) }}%) georeferenced</li>
        @isset($dynamic_stats['SpecimensCountID'])
            <li>{{ $dynamic_stats['SpecimensCountID'] }} ({{ floor( ($dynamic_stats['SpecimensCountID'] / $stats->recordcnt) * 100) }}%) identified to species</li>
        @endisset
        <li>{{ $stats->familycnt ?? 0 }} families</li>
        <li>{{ $stats->genuscnt ?? 0 }} genera</li>
        <li>{{ $stats->speciescnt ?? 0 }} identified to species</li>

        @isset($dynamic_stats['TotalTaxaCount'])
            <li>{{ $dynamic_stats['TotalTaxaCount'] }} total taxa (including subsp. and var.)</li>
        @endisset
    </div>

    @php
        $fam_georef_stats = [];
        foreach($dynamic_stats['families'] as $key => $item) {
            if(is_numeric($item['SpecimensPerFamily'])) {
                $fam_georef_stats[$key] = intval($item['SpecimensPerFamily']);
            }
        }

        $country_georef_stats = [];
        foreach($dynamic_stats['countries'] as $key => $item) {
            if(is_numeric($item['CountryCount'])) {
                $country_georef_stats[$key] = intval($item['CountryCount']);
            }
        }
    @endphp

    <div>
        <div class="text-2xl font-bold">Extra Statistics</div>
        <div class="flex items-center gap-4">
            @isset($dynamic_stats['families'])
                <x-pie-chart class="w-64" :values="$fam_georef_stats" />
            @endisset

            @isset($dynamic_stats['countries'])
                <x-pie-chart class="w-64" :values="$country_georef_stats"/>
            @endisset
        </div>
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
</x-layout>
