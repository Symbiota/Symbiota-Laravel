@props([
    'parents' => [],
    'rankMap' => [],
    'targetTid' => null,
    'taxonName' => null,
])
<x-layout class="grid-col-1 grid gap-4">
    <div class="w-fit">
        <x-breadcrumbs
            :items="[
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Taxonomic Tree Viewer', 'href' => '#_'],
        ]"
        />
    </div>
    <x-button href="{{ url('taxon/create') }}" class="w-fit">Add a New Taxon</x-button>
    <x-button href="{{ url('taxon/export') }}" class="w-fit">Export Taxonomy</x-button>
    <form>
        <x-fieldset legend="Taxon Search">
            <x-taxa-search hide_selector="true" tidName="parenttid" :tid_value="$targetTid" :taxa_value="$taxonName" />
            <x-checkbox id="displayauthor" label="Display authors" name="displayauthor" />
            <x-checkbox id="match-on-whole-words" label="Match on whole words" name="match-on-whole-words" />
            <x-checkbox
                id="display-full-tree-below-family"
                label="Display full tree below family"
                name="display-full-tree-below-family"
            />
            <x-checkbox id="display-subgenera" label="Display species with subgenera" name="display-subgenera" />
            <x-checkbox
                id="display-only-occurrence-linked"
                label="Display only taxa linked to occurrences"
                name="display-only-occurrence-linked"
            />
            <x-button type="submit" class="w-fit">Display Taxon Tree</x-button>
        </x-fieldset>
    </form>
    <ul>
        <div id="taxon-tree">
            <x-tree-node :nodes="$parents" :rankMap="$rankMap" :standardizingFraction="5" :parentRankId="null" />
        </div>
    </ul>
</x-layout>
