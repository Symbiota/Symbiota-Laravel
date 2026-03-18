@props(['kingdoms' => []])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[['title' => 'Home', 'href' => url('')], 'Create Taxon']" />
    </div>

    <h1 class="text-4xl font-bold">Create Taxon</h1>
    <x-input label="Quick Parser" name="quickparser" id="quickparser"
        value="" />
    <x-input label="Scientific Name" name="sciName" id="sciName" value="" />
    <x-input label="Author" name="author" id="author" value="" />
    <x-select label="Kingdom" name="kingdom" id="kingdom" :items="$kingdoms
        ->map(
            fn($k) => [
                'title' => $k->sciName,
                'value' => $k->tid,
                'disabled' => false,
            ],
        )
        ->toArray()"
        :default="0" />
    <x-input label="Family" name="family" id="family" value="" />
    <x-input label="Notes" name="notes" id="notes" value="" />
    <x-input label="Source" name="source" id="source" value="" />
    <x-input label="Sort Sequence" name="sortSequence" id="sortSequence"
        value="" />
    <x-button>Save</x-button>
</x-layout>
