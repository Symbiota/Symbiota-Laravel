@props(['collection'])
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

    <div class="text-4xl font-bold">{{ $collection->collectionName}}</div>

    <div class="flex items-center gap-2">
        <x-button>Toggle Manager Control Panel</x-button>
        <x-button>Search Collection</x-button>
        <x-button>Search Media</x-button>
    </div>
    <p>{{ $collection->fullDescription }}</p>

    <x-accordion label="Manager Control Panel" open="true">
        <div>
            {{-- Data Editor Control Panel --}}
            <div>
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
                    colUrl('georef/georeftool.php') => 'Batch Georeference Specimens',
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
            <div>
                <div class="font-bold text-xl">Administration</div>
                <div>
                    @foreach ($admin_links as $link => $title)
                    <li><x-link href="{{ $link }}">{{ $title }}</x-link></li>
                    @endforeach
                </div>
            </div>
        </div>
    </x-accordion>

    <div>
        <div class="text-2xl font-bold">Contacts</div>
        TODO contacts
    </div>

    <div>
        <div class="text-2xl font-bold">Collection Statistics</div>
        TODO collections stats
    </div>

    <div>
        <div class="text-2xl font-bold">Extra Statistics</div>
        TODO collections extra stats
    </div>
    <x-accordion label="More Information">
        <div><span class="font-bold">Collection Type:</span> TODO</div>
        <div><span class="font-bold">Management:</span> TODO</div>

        <div><span class="font-bold">Last Update:</span> TODO</div>
        <div><span class="font-bold">Digital Metadata:</span> TODO</div>
        <div><span class="font-bold">IPT / DwC-A Source:</span> TODO</div>
        <div><span class="font-bold">Usage Rights:</span> TODO</div>
    </x-accordion>
</x-layout>
