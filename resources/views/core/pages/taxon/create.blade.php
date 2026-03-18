@props(['kingdoms' => [], 'allTaxonRanks' => []])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('')],
            [
                'title' => 'Taxononmic Tree View',
                'href' => legacy_url('/taxa/taxonomy/taxonomydisplay.php'),
            ],
            'Create Taxon',
        ]" />
    </div>

    <h1 class="text-4xl font-bold">Create Taxon</h1>
    <fieldset class="border border-base-300 rounded-md p-4 mb-4">
        <legend class="text-2xl font-semibold">Optional Quick Parser</legend>
        <x-input label="Quick Parser" name="quickparser" id="quickparser"
            value="" />
        <x-button class="mt-2">Parse</x-button>
    </fieldset>
    <fieldset class="border border-base-300 rounded-md p-4 mb-4">
        <legend class="text-2xl font-semibold">Add New Taxon</legend>
        <x-select label="Taxon Rank" name="rankid" id="rankid"
            :items="$allTaxonRanks
                ->map(
                    fn($r) => [
                        'title' => $r->rankname,
                        'value' => $r->rankid,
                        'disabled' => false,
                    ],
                )
                ->toArray()" />
        <div class="inline-flex items-center gap-2">
            <x-select label="Decorator" name="unitind1" id="unitind1"
                :items="$indContent" :default="0" />
            <x-input label="Unitname1-changeme" name="unitname1" id="unitname1"
                value="" />
        </div>
        <div class="inline-flex items-center gap-2">
            <x-select label="Decorator" name="unitind2" id="unitind2"
                :items="$indContent" :default="0" />
            <x-input label="Unitname2-changeme" name="unitname2" id="unitname2"
                value="" />
        </div>
        <x-input label="Unitname3-changeme" name="unitname3" id="unitname3"
            value="" />
        <x-input label="Author" name="author" id="author" value="" />
        <x-select label="Kingdom" name="kingdom" id="kingdom"
            :items="$kingdoms
                ->map(
                    fn($k) => [
                        'title' => $k->sciName,
                        'value' => $k->tid,
                        'disabled' => false,
                    ],
                )
                ->toArray()" :default="0" />
        <x-input label="Family" name="family" id="family" value="" />
        <x-input label="Notes" name="notes" id="notes" value="" />
        <x-input label="Source" name="source" id="source" value="" />
        <x-input label="Sort Sequence" name="sortSequence" id="sortSequence"
            value="" />
        <x-button>Save</x-button>
    </fieldset>
</x-layout>
