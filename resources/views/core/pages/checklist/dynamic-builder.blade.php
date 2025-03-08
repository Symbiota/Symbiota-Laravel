<x-layout class="flex flex-col gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('')],
        ['title' => 'Dynamic Map'],
        ]" />
    <div>
        Description
    </div>

    <div>
        Point (Lat, Long) (click on map)
    </div>

    <x-taxa-search label="Taxon Filter" />
    <div>
        <x-button>Build Checklist</x-button>
    </div>

    <div class="flex items-center gap-2">
        <x-input name="radius" />
        <x-select name="units" :items="[
        ['title' => 'Kilometers', 'value' => 'kilometers' ],
        ['title' => 'Miles', 'value' => 'miles' ]
    ]" />
    </div>

    <x-map />
</x-layout>
