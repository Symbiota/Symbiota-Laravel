@props(['collection'])
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
