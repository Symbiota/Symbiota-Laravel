@props(['taxon'])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('') ],
            ['title' => 'Taxon Profile Public Display', 'href' => url('/taxon/' . $taxon->tid . '/edit') ],
            'Taxon Profile Editor'
        ]" />
    </div>

    <h1 class="text-4xl font-bold"><i>{{ $taxon->sciName }}</i> {{ $taxon->author }}</h1>
    <div class="mb-4 flex">
    @if($taxon->family)
        <h2 class="text-2xl font-bold">Family: {{ $taxon->family }}</h2>
    @endif
    </div>

    <x-tabs :tabs="['Synonyms/Vernaculars', 'Media', 'Media Sort', 'Add Media', 'Descriptions']">
        {{-- Synonyms/Vernaculars --}}
        <div>
            TODO (Logan) Synonyms/Vernaculars
        </div>

        {{-- Media --}}
        <div>
            TODO (Logan) Media
        </div>

        {{-- Media Sort --}}
        <div>
            TODO (Logan) Media sort
        </div>

        {{-- Add Media --}}
        <div>
            TODO (Logan) Add Media
        </div>

        {{-- Descriptions --}}
        <div>
            TODO (Logan) Descriptions
        </div>
    </x-tabs>
</x-layout>
