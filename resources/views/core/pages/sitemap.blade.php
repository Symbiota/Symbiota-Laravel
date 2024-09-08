<x-layout class="grid grid-cols-1 p-10">

    <h1 class="text-5xl text-primary font-bold">Site Map</h1>

    <h2 class="text-2xl text-primary font-bold">Collections</h2>
    <div>
        <x-link target="_blank" href="/collections/search">Search Engine</x-link> - search collections
    </div>
    <div>
        <x-link target="_blank" href="/collections/misc/collprofiles.php">Collections</x-link> - list of collections
        participating in project
    </div>
    <div>
        <x-link target="_blank" href="collections/misc/collstats.php">Collection Statistics</x-link>
    </div>
    <h3 class="text-lg font-bold text-primary">Data Publishing</h3>
    <div>
        <x-link target="_blank" href="collections/datasets/rsshandler.php">
            RSS Feed for Natural History Collections and Observation Projects
        </x-link>
    </div>

    <div>
        <x-link target="_blank" href="collections/datasets/datapublisher.php">
            Darwin Core Archives (DwC-A)
        </x-link> - published datasets of selected collections
    </div>
    <x-link target="_blank" href="/content/dwca/rss.xml">
        DwC-A RSS Feed
    </x-link>

    <div>

        <x-link target="_blank" href="collections/misc/protectedspecies.php">
            Protected Species
        </x-link>
        - list of taxa where
        locality and/or taxonomic information is protected due to rare/threatened/endangered status
    </div>

    <h2 class="text-2xl text-primary font-bold">Image Library</h2>
    <x-link href="imagelib/index.php">Image Library</x-link>
    <x-link href="imagelib/search.php">Interactive Search Tool</x-link>
    <x-link href="imagelib/contributors.php">Image Contributors</x-link>
    <x-link href="includes/usagepolicy.php">Usage Policy and Copyright Information</x-link>

    <h2 class="text-2xl text-primary font-bold">Additional Resources</h2>
    <x-link href="glossary/index.php">Glossary</x-link>
    <x-link href="taxa/taxonomy/taxonomydisplay.php">Taxonomic Tree Viewer</x-link>
    <x-link href="taxa/taxonomy/taxonomydynamicdisplay.php">Taxonomy Explorer</x-link>

    <h2 class="text-2xl text-primary font-bold">Biotic Inventory Projects</h2>
    This is dyanmic info

    <h2 class="text-2xl text-primary font-bold">Datasets</h2>
    <x-link href="collections/datasets/publiclist.php">All Publicly Viewable Datasets</x-link>

    <h2 class="text-2xl text-primary font-bold">Dynamic Species Lists</h2>
    <div>
        <x-link href="checklists/dynamicmap.php?interface=checklist">
            Checklist </x-link> - dynamically build a checklist using georeferenced specimen records
    </div>
    <div>
        <x-link href="checklists/dynamicmap.php?interface=key">
            Dynamic Key </x-link> - dynamically build a key using georeferenced specimen records
    </div>

    <h1 class="text-4xl text-primary font-bold">Data Management Tools</h1>
    Please login to access editing tools
    Contract a portal administrator for obtaining editing permisssons

    <img class="h-8" src="https://img.shields.io/badge/Symbiota-v3.0.34-blue.svg"
        alt="a blue badge depicting Symbiota software version">
    <img class="h-8" src="https://img.shields.io/badge/Schema-v3.1-blue.svg"
        alt="a blue badge depicting Symbiota database schema version">
</x-layout>
