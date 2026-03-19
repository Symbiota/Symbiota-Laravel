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
    <form x-data="{ unit1Label: 'Genus Name', unit2Label: 'Specific Epithet' }" class="mt-4 flex flex-col gap-4">
        <div class="w-1/2">
            <fieldset class="border border-base-300 rounded-md p-4 mb-4">
                <legend class="text-2xl font-semibold">Optional Quick Parser
                </legend>
                <x-input label="Quick Parser" name="quickparser" id="quickparser"
                    value="" />
                <x-button class="mt-2">Parse</x-button>
            </fieldset>
        </div>
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
            <div class="flex items-center gap-2 mb-4">
                <div class="flex flex-col">
                    <label class="text mb-1" for="unitind1-toggle"
                        x-text="unit1Label + ' Decorator'"></label>
                    <x-select name="unitind1" id="unitind1" :items="$indContent"
                        :default="0" />
                </div>
                <div class="flex flex-col">
                    <label class="text-base-content text-base text-bold mb-1"
                        for="unitname1">
                        <span x-text="unit1Label"></span>
                        <span
                            class="vertical-align text-error italic pr-1">*</span>
                    </label>
                    <x-input required name="unitname1" id="unitname1"
                        value="" />
                </div>
            </div>

            <div class="flex items-center gap-2 mb-4">
                <div class="flex flex-col">
                    <label class="text mb-1" for="unitind2-toggle"
                        x-text="unit2Label + ' Decorator'"></label>
                    <x-select name="unitind2" id="unitind2" :items="$indContent"
                        :default="0" />
                </div>
                <div class="flex flex-col">
                    <label class="text-base-content text-base text-bold mb-1"
                        for="unitname2" x-text="unit2Label"></label>
                    <x-input name="unitname2" id="unitname2" value="" />
                </div>
            </div>
            <div class="inline-flex items-center gap-2">
                <x-input label="Infraspecific designation" name="unitind3"
                    id="unitind3" placeholder="spp., var., forma, etc." />
                <x-input label="Infraspecific Epithet" name="unitname3"
                    id="unitname3" value="" />
            </div>
            <div class="w-3/4">
                <x-input label="Author" name="author" id="author"
                    value="" />
            </div>
            <div class="w-1/2">
                <x-input required label="Parent Taxon" name="parentname"
                    id="parentname" value="" />
            </div>
            <div class="w-1/2">
                <x-input label="Notes" name="notes" id="notes"
                    value="" />
            </div>
            <div class="w-1/2">
                <x-input label="Source" name="source" id="source"
                    value="" />
            </div>
            <x-select class="m-2" label="Locality Security"
                name="securitystatus" id="securitystatus" :items="$securityOptions" />
            <fieldset class="border border-base-300 rounded-md p-4 mb-4">
                <legend class="text-2xl font-semibold">Acceptance Status
                </legend>
                <x-radio name="acceptance_status" :options="[
                    ['label' => 'Accepted', 'value' => 1],
                    ['label' => 'Not Accepted', 'value' => 0],
                ]"
                    default_value="1" />
            </fieldset>
            <div>
                <span class="text-sm italic text-base-content">* = Required
                    Field</span>
            </div>
            <x-button class="mt-2">Submit New Name</x-button>
    </form>
</x-layout>
