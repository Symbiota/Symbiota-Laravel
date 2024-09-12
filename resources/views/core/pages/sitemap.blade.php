<x-layout class="grid grid-cols-1 p-10 gap-4">

    <h1 class="text-5xl text-primary font-bold">Site Map</h1>
    {{-- COLLECTIONS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Collections</h2>
        <li><x-link target="_blank" href="/collections/search">Search Engine</x-link> - search collections</li>
        <li>
            <x-link target="_blank" href="/collections/misc/collprofiles.php">Collections</x-link> - list of
            collections
            participating in project
        </li>
        <li>
            <x-link target="_blank" href="collections/misc/collstats.php">Collection Statistics</x-link>
        </li>
        <li>
            <x-link target="_blank" href="collections/misc/protectedspecies.php">
                Protected Species
            </x-link>
            - list of taxa where
            locality and/or taxonomic information is protected due to rare/threatened/endangered status
        </li>
        <h3 class="text-lg font-bold text-primary">Data Publishing</h3>
        <li>
            <x-link target="_blank" href="collections/datasets/rsshandler.php">
                RSS Feed for Natural History Collections and Observation Projects
            </x-link>
        </li>

        <li>
            <x-link target="_blank" href="collections/datasets/datapublisher.php">
                Darwin Core Archives (DwC-A)
            </x-link> - published datasets of selected collections
        </li>
        <li>
            <x-link target="_blank" href="/content/dwca/rss.xml">
                DwC-A RSS Feed
            </x-link>
        </li>
    </div>

    {{-- IMAGE LIBRARY --}}
    <div class="grid grid-cols-1">
        <h2 class="text-2xl text-primary font-bold">Image Library</h2>
        <li><x-link href="imagelib/index.php">Image Library</x-link></li>
        <li><x-link href="imagelib/search.php">Interactive Search Tool</x-link></li>
        <li><x-link href="imagelib/contributors.php">Image Contributors</x-link></li>
        <li><x-link href="includes/usagepolicy.php">Usage Policy and Copyright Information</x-link></li>
    </div>

    {{-- ADDITIONAL RESOURCES --}}
    <div class="grid grid-cols-1">
        <h2 class="text-2xl text-primary font-bold">Additional Resources</h2>
        <li><x-link href="glossary/index.php">Glossary</x-link></li>
        <li><x-link href="taxa/taxonomy/taxonomydisplay.php">Taxonomic Tree Viewer</x-link></li>
        <li><x-link href="taxa/taxonomy/taxonomydynamicdisplay.php">Taxonomy Explorer</x-link></li>
    </div>

    {{-- BIOTIC INVENTORY PROJECTS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Biotic Inventory Projects</h2>
        This is dyanmic info
    </div>

    {{-- DATASETS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Datasets</h2>
        <li><x-link href="collections/datasets/publiclist.php">All Publicly Viewable Datasets</x-link></li>
    </div>

    {{-- DYNAMIC SPECIES LISTS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Dynamic Species Lists</h2>
        <li>
            <x-link href="checklists/dynamicmap.php?interface=checklist">
                Checklist </x-link> - dynamically build a checklist using georeferenced specimen records
        </li>
        <li>
            <x-link href="checklists/dynamicmap.php?interface=key">
                Dynamic Key </x-link> - dynamically build a key using georeferenced specimen records
        </li>
    </div>

    {{-- ADMIN DATA TOOLS --}}
    <h1 class="text-4xl text-primary font-bold mt-4">Data Management Tools</h1>
    Please login to access editing tools
    Contract a portal administrator for obtaining editing permisssons

    {{-- ADMIN FUNCTIONS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Administrative Functions (Super Admins only)</h2>
        <li>
            <x-link href="profile/usermanagement.php">User Permissions</x-link>
        </li>
        <li>
            <x-link href="/collections/misc/collmetadata.php">
                Create a New Collection or Observation Profile </x-link>
        </li>
        <li>
            <x-link href="/geothesaurus/index.php">
                Geographic Thesaurus </x-link>
        </li>
        <li>
            <x-link href="/imagelib/admin/thumbnailbuilder.php">
                Thumbnail Builder Tool </x-link>
        </li>
        <li>
            <x-link href="/collections/admin/guidmapper.php">
                Collection GUID Mapper </x-link>
        </li>
        <li>
            <x-link href="/collections/specprocessor/salix/salixhandler.php">
                SALIX WordStat Manager </x-link>
        </li>
        <li>
            <x-link href="/glossary/index.php">
                Glossary </x-link>
        </li>
        <li>
            <x-link href="collections/map/staticmaphandler.php">Manage Taxon Profile Map Thumbnails</x-link>
        </li>
    </div>

    {{-- IDENTIFICATION KEYS --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Identification Keys</h2>
        <div>
            <li>You are authorized to access the
                <x-link href="/ident/admin/index.php">Characters and Character StatesEditor</x-link>
            </li>
            <li>You are authorized to edit Identification Keys</li>
            <li>For coding characters in a table format, open the Matrix Editor for any of the following checklists
            </li>
            <div class="ml-4">
                {{-- TODO (Logan) dynamic content user checklists. Example below --}}

                <li><x-link href="/ident/tools/matrixeditor.php?clid=133">SDSU Adobe Falls Ecological Reserve Plant
                        Checklist</x-link></li>
            </div>
        </div>
    </div>

    {{-- IMAGES --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Images</h2>
        <p>See the Symbiota documentation on
            <x-link href="https://biokic.github.io/symbiota-docs/editor/images/">Image Submission</x-link>
            for an overview of how images are managed within a Symbiota data portal. Field images without
            detailed locality information can be uploaded using the Taxon Species Profile page.
            Specimen images are loaded through the Specimen Editing page or through a batch upload process
            established by a portal manager. Image Observations (Image Vouchers) with detailed locality information can
            be
            uploaded using the link below. Note that you will need the necessary permission assignments to use this
            feature.
        </p>
        <div class="pt-2">
            <li>
                <x-link href="taxa/profile/tpeditor.php?tabindex=1" target="_blank">
                    Basic Field Image Submission </x-link>
            </li>
            <li>
                <x-link href="collections/editor/observationsubmit.php">
                    Image Observation Submission Module </x-link>
            </li>

        </div>
    </div>

    {{-- Biotic Inventory Projects --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Biotic Inventory Projects</h2>
        <li>
            <x-link href="projects/index.php?newproj=1">Add a New Project</x-link>
        </li>
        {{-- TODO (Logan) load as dynamic content --}}
        <div class="text-bold text-lg text-primary font-bold">List of Current Projects (click to edit)</div>
        <li>
            <x-link href="/projects/index.php?pid=7&amp;emode=1">California State Parks</x-link>
        </li>
        <li>
            <x-link href="/projects/index.php?pid=3&amp;emode=1">California-Wide Taxon Lists</x-link>
        </li>
        <li>
            <x-link href="/projects/index.php?pid=4&amp;emode=1">County Floras</x-link>
        </li>
        <li>
            <x-link href="/projects/index.php?pid=6&amp;emode=1">CSU Natural Reserve Systems</x-link>
        </li>
        <li>
            <x-link href="/projects/index.php?pid=5&amp;emode=1">Local Floras</x-link>
        </li>
        <li>
            <x-link href="/projects/index.php?pid=1&amp;emode=1">National Park Service, California Parks</x-link>
        </li>
        <li>
            <x-link href="/projects/index.php?pid=2&amp;emode=1">Univ. of California, Natural Reserve System</x-link>
        </li>
    </div>

    {{-- Datasets --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Datasets</h2>
        <li>
            <x-link href="collections/datasets/index.php">Dataset Management Page</x-link> - datasets you are authorized
            to edit
        </li>
    </div>

    {{-- Taxon Profile Page --}}
    <div>
        <h2 class="text-2xl text-primary font-bold">Taxon Profile Page</h2>
        <div>
            <li><x-link href="taxa/profile/tpeditor.php?taxon=">Edit Synonyms / Common Names</x-link></li>
            <li><x-link href="taxa/profile/tpeditor.php?taxon=&amp;tabindex=4">Edit Text Descriptions</x-link></li>
            <li><x-link href="taxa/profile/tpeditor.php?taxon=&amp;tabindex=1">Edit Images</x-link></li>
            <div class="ml-4">
                <li class="nested-li"><x-link
                        href="taxa/profile/tpeditor.php?taxon=&amp;category=imagequicksort&amp;tabindex=2">Edit Image
                        Sorting Order</x-link></li>
                <li class="nested-li"><x-link
                        href="taxa/profile/tpeditor.php?taxon=&amp;category=imageadd&amp;tabindex=3">Add a new
                        image</x-link></li>
            </div>
        </div>
    </div>
    <div>
        <h2 class="text-2xl text-primary font-bold">Taxonomy</h2>
        <div>
            <li>Edit Taxonomic Placement (use <x-link href="taxa/taxonomy/taxonomydisplay.php">Taxonomic Tree
                    Viewer)</x-link>
            </li>
            <li><x-link href="taxa/taxonomy/taxonomyloader.php">Add New Taxonomic Name</x-link></li>
            <li><x-link href="taxa/taxonomy/batchloader.php">Batch Upload a Taxonomic Data File</x-link></li>
            <li><x-link href="taxa/profile/eolmapper.php">Encyclopedia of Life Linkage Manager</x-link></li>
        </div>
    </div>
    <div>
        <h2 class="text-2xl text-primary font-bold">Checklists</h2>
        <p>
            Tools for managing Checklists are available from each checklist display page.
            Editing symbols located in the upper right of the page will display
            editing options for that checklist.
            Below is a list of the checklists you are authorized to edit.
        </p>
        {{-- TODO (Logan) load checklists --}}
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
        {{-- TODO (Logan) load collections --}}
        <h3 class="text-xl pt-2 text-primary font-bold">Lists of collections you have permissions to edit</h3>
        dynamic content of collections
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
                href="https://biokic.github.io/symbiota-docs/col_obs/" target="_blank">Symbiota Documentation</x-link>
            for more information on specimen processing capabilities. Note that observation projects are not activated
            on all Symbiota data portals.
        </p>
        <h3 class="text-xl pt-2 text-primary font-bold">Observation Image Voucher submission</h3>
        {{-- TODO (Logan) load this data --}}
        <h3 class="text-xl pt-2 text-primary font-bold">Personal Specimen Management and Label Printing Features</h3>
        {{-- TODO (Logan) load this data --}}
        <h3 class="text-xl pt-2 text-primary font-bold">Observation Profject Management</h3>
        {{-- TODO (Logan) load this data --}}
    </div>

    {{-- VERSIONING --}}
    <img class="h-8" src="https://img.shields.io/badge/Symbiota-v3.0.34-blue.svg"
        alt="a blue badge depicting Symbiota software version">
    <img class="h-8" src="https://img.shields.io/badge/Schema-v3.1-blue.svg"
        alt="a blue badge depicting Symbiota database schema version">
</x-layout>
