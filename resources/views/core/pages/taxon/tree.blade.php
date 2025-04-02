@props(['taxa' => []])
<x-layout class="flex flex-col gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Taxonomy'],
    ]" />

    <h1 class="font-bold text-4xl">Taxonomy Explorer: Central Thesaurus</h1>

    <form class="flex flex-col gap-4">
        <x-taxa-search include="#editor_mode, #display_author, #only_linked_to_occurrences" />
        <x-checkbox id="editor_mode" label="Editor Mode" :checked="true" />
        <x-checkbox id="display_author" label="Display Author" :checked="false" />
        <x-checkbox id="only_linked_to_occurrences" label="Display only taxa linked to occurrences" :checked="false" />

        <div class="flex gap-4">
            <x-button type="submit">Display Taxon Tree</x-button>
            <x-button type="button" variant="neutral">Export Taxonomy</x-button>
        </div>
    </form>

    <div id="taxonomy-tree" class="flex flex-col gap-2">
        @foreach ($taxa as $taxon)
        <div class="gap-4 flex items-center">
            <x-button class="p-1 w-fit"><i class="text-primary-content fa fa-plus"></i></x-button> {{$taxon->rankname ?? 'Unknown' }}: {{ $taxon->sciName }}
        </div>
        @endforeach
    </div>
</x-layout>
