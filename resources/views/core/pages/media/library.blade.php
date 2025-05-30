<x-layout class="sm:w-[90%] lg:w-[70%] m-auto flex flex-col gap-4">
    <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Media Library' ]
        ]"
    />

    <h1 class="text-4xl font-bold">Taxa with Media</h1>

    <p>
        This page provides a complete list of taxa that have media. Use the controls below to browse and search for media by family, genus, or species.
    </p>

    <div class="flex items-center gap-2">
        <x-button hx-boost="true" href="{{ url('usagepolicy/#media') }}">Media Copyright Policy</x-button>
        <x-button hx-boost="true" href="{{ url('media/contributors') }}">Media Contributors</x-button>
        <x-button hx-boost="true" href="{{ url('media/search')}}">Media Search</x-button>
    </div>

    <x-taxa-search />

    <hr>

    <h2 class="text-2xl font-bold">Select a family to see species list</h2>

    <x-slide-tab-container :tabs="['Family', 'Genus']">
        <x-slide-tab class="py-4">
            TODO family Rendering
        </x-slide-tab>

        <x-slide-tab class="py-4">
            TODO Genus Rendering
        </x-slide-tab>
    </x-slide-tab-container>
</x-layout>
