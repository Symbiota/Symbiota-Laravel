@props(['projects' => [], 'schema_version'])
<x-layout class="grid grid-cols-1 p-10 gap-4">
    <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Site Map' ]
        ]"
    />

    <h1 class="text-5xl text-primary font-bold">Site Map</h1>

    {{-- COLLECTIONS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Collections</h2>

        <ul class="list-disc pl-4">
            <li><x-link target="_blank" href="{{ url('/collections/search') }}">Search Engine</x-link> - search collections</li>
            <li>
                <x-link target="_blank" href="{{ url('/collections') }}">Collections</x-link> - list of
                collections
                participating in project
            </li>
            <li>
                <x-link target="_blank" href="{{ legacy_url('/collections/misc/collstats.php') }}">Collection Statistics</x-link>
            </li>
            <li>
                <x-link target="_blank" href="{{ legacy_url('/collections/misc/protectedspecies.php') }}">
                    Protected Species
                </x-link>
                - list of taxa where
                locality and/or taxonomic information is protected due to rare/threatened/endangered status
            </li>
        </ul>

        <h3 class="text-lg font-bold text-primary">Data Publishing</h3>
        <ul class="list-disc pl-4">
            <li>
                <x-link target="_blank" href="{{ legacy_url('/collections/datasets/rsshandler.php') }}">
                    RSS Feed for Natural History Collections and Observation Projects
                </x-link>
            </li>

            <li>
                <x-link target="_blank" href="{{ legacy_url('/collections/datasets/datapublisher.php') }}">
                    Darwin Core Archives (DwC-A)
                </x-link> - published datasets of selected collections
            </li>
            <li>
                <x-link target="_blank" href="{{ legacy_url('/content/dwca/rss.xml') }}">
                    DwC-A RSS Feed
                </x-link>
            </li>
        </ul>
    </div>

    {{-- IMAGE LIBRARY --}}
    <div class="grid grid-cols-1">
        <h2 class="text-2xl text-primary font-bold">Media Library</h2>
        <ul class="list-disc pl-4">
            <li><x-link href="{{ url('media/library') }}">Media Library</x-link></li>
            <li><x-link href="{{ url('media/search') }}">Interactive Search Tool</x-link></li>
            <li><x-link href="{{ url('media/contributors') }}">Media Contributors</x-link></li>
            <li><x-link href="{{ url('usagepolicy') }}">Usage Policy and Copyright Information</x-link></li>
        <ul>
    </div>

    {{-- ADDITIONAL RESOURCES --}}
    <div class="grid grid-cols-1">
        <h2 class="text-2xl text-primary font-bold">Additional Resources</h2>
        <ul class="list-disc pl-4">
            <li><x-link href="{{ legacy_url('/glossary/index.php') }}">Glossary</x-link></li>
            <li><x-link href="{{ legacy_url('/taxa/taxonomy/taxonomydisplay.php') }}">Taxonomic Tree Viewer</x-link></li>
            <li><x-link href="{{ legacy_url('/taxa/taxonomy/taxonomydynamicdisplay.php') }}">Taxonomy Explorer</x-link></li>
        </ul>
    </div>

    {{-- BIOTIC INVENTORY PROJECTS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Biotic Inventory Projects</h2>
            <ul class="list-disc pl-4">
                @foreach ($projects as $project)
                    <li>
                        <x-link href="{{ url('projects/' . $project->pid) }}">{{ $project->projname }}</x-link>
                        @if($project->managers)
                        <ul class="list-disc pl-4"><li>Manager: {{ $project->managers }}</li></ul>
                        @endif
                    </li>
                @endforeach
                <li>
                    <x-link href="{{ url('checklists') }}">All Public Checklists</x-link>
                </li>
            </ul>
    </div>

    {{-- DATASETS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Datasets</h2>
        <ul class="list-disc pl-4">
            <li><x-link href="{{ legacy_url('/collections/datasets/publiclist.php') }}">All Publicly Viewable Datasets</x-link></li>
        </ul>
    </div>

    {{-- DYNAMIC SPECIES LISTS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Dynamic Species Lists</h2>
        <ul class="list-disc pl-4">
            <li>
                <x-link href="{{ legacy_url('/checklists/dynamicmap.php?interface=checklist') }}">
                    Checklist </x-link> - dynamically build a checklist using georeferenced specimen records
            </li>
            <li>
                <x-link href="{{ legacy_url('/checklists/dynamicmap.php?interface=key') }}">
                    Dynamic Key </x-link> - dynamically build a key using georeferenced specimen records
            </li>
        </ul>
    </div>

    {{-- ADMIN DATA TOOLS --}}
    <h1 class="text-4xl text-primary font-bold mt-4">Data Management Tools</h1>

    Please login to access editing tools
    Contract a portal administrator for obtaining editing permissions

    {{-- ADMIN FUNCTIONS --}}
    @can('SUPER_ADMIN')
    <div>
        <h2 class="text-2xl text-primary font-bold">Administrative Functions (Super Admins only)</h2>
        <ul class="list-disc pl-4">
            <li>
                <x-link href="{{ legacy_url('/profile/usermanagement.php') }}">User Permissions</x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/collections/misc/collmetadata.php') }}">
                    Create a New Collection or Observation Profile </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/geothesaurus/index.php') }}">
                    Geographic Thesaurus </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/imagelib/admin/thumbnailbuilder.php') }}">
                    Thumbnail Builder Tool </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/collections/admin/guidmapper.php') }}">
                    Collection GUID Mapper </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/collections/specprocessor/salix/salixhandler.php') }}">
                    SALIX WordStat Manager </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/glossary/index.php') }}">
                    Glossary </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/collections/map/staticmaphandler.php') }}">Manage Taxon Profile Map Thumbnails</x-link>
            </li>
        </ul>
    </div>
    @endcan

    {{-- IDENTIFICATION KEYS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Identification Keys</h2>
        <ul class="list-disc pl-4">
            <li>You are authorized to access the
                <x-link href="{{ legacy_url('/ident/admin/index.php') }}">Characters and Character States Editor</x-link>
            </li>
            <li>You are authorized to edit Identification Keys</li>
            <li>For coding characters in a table format, open the Matrix Editor for any of the following checklists
            </li>
            <div class="ml-4">
                {{-- TODO (Logan) dynamic content user checklists. Example below --}}

                <li><x-link href="{{ legacy_url('/ident/tools/matrixeditor.php?clid=133') }}">SDSU Adobe Falls Ecological Reserve Plant
                        Checklist</x-link></li>
            </div>
        </ul>
    </div>

    {{-- IMAGES --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Media</h2>
        <p>See the Symbiota documentation on
            <x-link href="{{ docs_url('Collection_Manager_Guide/Images') }}">Image Submission</x-link>
            for an overview of how images are managed within a Symbiota data portal. Field images without
            detailed locality information can be uploaded using the Taxon Species Profile page.
            Specimen images are loaded through the Specimen Editing page or through a batch upload process
            established by a portal manager. Image Observations (Image Vouchers) with detailed locality information can
            be
            uploaded using the link below. Note that you will need the necessary permission assignments to use this
            feature.
        </p>
        <ul class="list-disc pt-2 pl-4">
            <li>
                <x-link href="{{ legacy_url('/taxa/profile/tpeditor.php?tabindex=1') }}" target="_blank">
                    Basic Field Image Submission </x-link>
            </li>
            <li>
                <x-link href="{{ legacy_url('/collections/editor/observationsubmit.php') }}">
                    Image Observation Submission Module </x-link>
            </li>

        </ul>
    </div>

    {{-- Biotic Inventory Projects --}}
    <div>
        <div class="flex flex-items items-center gap-4">
            <h2 class="text-2xl text-primary font-bold">Biotic Inventory Projects</h2>
            <x-button href="{{ legacy_url('/projects/index.php?newproj=1') }}">Add a New Project</x-button>
        </div>
        <ul class="list-disc pl-4">
            @foreach ($projects as $project)
                <li>
                    <x-link href="{{ url('projects/' . $project->pid . '/edit') }}">{{ $project->projname}}</x-link>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Datasets --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Datasets</h2>
        <ul class="list-disc pl-4">
            <li>
                <x-link href="{{ legacy_url('/collections/datasets/index.php') }}">Dataset Management Page</x-link> - datasets you are authorized
                to edit
            </li>
        </ul>
    </div>

    {{-- Taxon Profile Page --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Taxon Profile Page</h2>
        <ul class="list-disc pl-4">
            <li><x-link href="{{ legacy_url('/taxa/profile/tpeditor.php?taxon=') }}">Edit Synonyms / Common Names</x-link></li>
            <li><x-link href="{{ legacy_url('/taxa/profile/tpeditor.php?taxon=&tabindex=4') }}">Edit Text Descriptions</x-link></li>
            <li><x-link href="{{ legacy_url('/taxa/profile/tpeditor.php?taxon=&tabindex=1') }}">Edit Images</x-link></li>
            <div class="ml-4">
                <li class="nested-li"><x-link
                        href="{{ legacy_url('/taxa/profile/tpeditor.php?taxon=&category=imagequicksort&tabindex=2') }}">Edit Image
                        Sorting Order</x-link></li>
                <li class="nested-li"><x-link
                        href="{{ legacy_url('/taxa/profile/tpeditor.php?taxon=&category=imageadd&tabindex=3') }}">Add a new
                        image</x-link></li>
            </div>
        </ul>
    </div>
    <div>
        <h2 class="text-2xl text-primary font-bold">Taxonomy</h2>
        <ul class="list-disc pl-4">
            <li>Edit Taxonomic Placement (use <x-link href="{{ legacy_url('/taxa/taxonomy/taxonomydisplay.php') }}">Taxonomic Tree
                    Viewer)</x-link>
            </li>
            <li><x-link href="{{ legacy_url('/taxa/taxonomy/taxonomyloader.php') }}">Add New Taxonomic Name</x-link></li>
            <li><x-link href="{{ legacy_url('/taxa/taxonomy/batchloader.php') }}">Batch Upload a Taxonomic Data File</x-link></li>
            <li><x-link href="{{ legacy_url('/taxa/profile/eolmapper.php') }}">Encyclopedia of Life Linkage Manager</x-link></li>
        </ul>
    </div>
    <div>
        <h2 class="text-2xl text-primary font-bold">Checklists</h2>
        <p>
            Tools for managing Checklists are available from each checklist display page.
            Editing symbols located in the upper right of the page will display
            editing options for that checklist.
            Below is a list of the checklists you are authorized to edit.
        </p>
        <ul class="list-disc pl-4">
            @foreach ($checklists as $checklist)
            <li>
                <x-link href="{{ url('checklist/' . $checklist->clid) }}">{{ $checklist->name }}</x-link>
            </li>
            @endforeach
        <ul>
    </div>

    {{-- Collections --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Collections</h2>
        <p>
            Tools for managing data specific to a particular collection are available through the collection's profile
            page.
            Clicking on a collection name in the list below will take you to this page for that given collection.
            An additional method to reach this page is by clicking on the collection name within the specimen search
            engine.
            The editing symbol located in the upper right of Collection Profile page will open
            the editing pane and display a list of editing options. </p>
        <h3 class="text-xl pt-2 text-primary font-bold">Lists of collections you have permissions to edit</h3>
        <ul class="list-disc pl-4">
        @foreach($collections as $collection)
            @if($collection->collType == App\Models\Collection::Specimens)
            <li>
                <x-link href="{{ url('collections/' .$collection->collID) }}">{{ $collection->collectionName }} </x-link>
            </li>
            @endif
        @endforeach
        </ul>
    </div>

    {{-- Observations --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Observations</h2>
        <p class="description">
            Data management for observation projects is handled in a similar manner to what is described in the
            Collections paragraph above.
            One difference is the General Observation project. This project serves two central purposes:
            1) Allows registered users to submit a image voucherd field observation.
            2) Allows collectors to enter their own collection data for label printing and to make the data available
            to the collections obtaining the physical specimens through donations or exchange. Visit the <x-link
                href="{{ docs_url('Collector_Observer_Guide') }}" target="_blank">Symbiota Documentation</x-link>
            for more information on specimen processing capabilities. Note that observation projects are not activated
            on all Symbiota data portals.
        </p>
        <h3 class="text-xl pt-2 text-primary font-bold">Observation Image Voucher submission</h3>
        <ul class="list-disc pl-4">
        @foreach($collections as $collection)
            @if($collection->collType == App\Models\Collection::Observations || $collection->collType == App\Models\Collection::GeneralObservations)
            <li>
                <x-link href="{{ legacy_url('/collections/editor/observationsubmit.php') }}?collid={{$collection->collID}}">{{ $collection->collectionName }} </x-link>
            </li>
            @endif
        @endforeach
        </ul>

        <h3 class="text-xl pt-2 text-primary font-bold">Personal Specimen Management and Label Printing Features</h3>
        <ul class="list-disc pl-4">
        @foreach($collections as $collection)
            @if($collection->collType == App\Models\Collection::GeneralObservations)
            <li>
                <x-link href="{{ url('collections/' .$collection->collID) }}">{{ $collection->collectionName }} </x-link>
            </li>
            @endif
        @endforeach
        </ul>

        <h3 class="text-xl pt-2 text-primary font-bold">Observation Profject Management</h3>
        <ul class="list-disc pl-4">
        @foreach($collections as $collection)
            @if($collection->collType == App\Models\Collection::Observations || $collection->collType == App\Models\Collection::GeneralObservations)
            <li>
                <x-link href="{{ url('collections/' .$collection->collID) }}">{{ $collection->collectionName }} </x-link>
            </li>
            @endif
        @endforeach
        </ul>
    </div>

    {{-- VERSIONING --}}
    <img class="h-8" src="https://img.shields.io/badge/Symbiota-v{{ config('portal.version') }}-blue.svg"
        alt="a blue badge depicting Symbiota software version">
    <img class="h-8" src="https://img.shields.io/badge/Schema-v{{ $schema_version }}-blue.svg"
        alt="a blue badge depicting Symbiota database schema version">
    @if(config('portal.schema_version') != $schema_version)
        <div class="bg-warning text-warning-content rounded-md p-4">
            Recommended Symbiota schema version differs from the database schema
        </div>
    @endif
</x-layout>
