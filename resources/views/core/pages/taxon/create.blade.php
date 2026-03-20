@props([
    'kingdoms' => [],
    'allTaxonRanks' => [],
    'indContent' => [],
    'securityOptions' => [],
])
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

    <div class="flex flex-col items-center justify-center"
        x-init ="$nextTick(() => { 
            console.log('Alpine component initialized');
            if (window.taxonomyCreateInit) taxonomyCreateInit.call(this);
            $watch('rankid', (newValue, oldValue) => {
                console.log('Watcher triggered! Old:', oldValue, 'New:', newValue);
                updateLabels();
            });
        })"
        x-data="{
            unit1Label: 'Genus',
            unit2Label: 'Species',
            rankid: null,
            allTaxonRanks: @js($allTaxonRanks),
            updateLabels() {
                console.log('updateLabels called from Alpine component');
                if (window.updateLabels) {
                    window.updateLabels(this);
                }
            }
        }"
        x-effect="console.log('Current rankid value:', rankid)">
        <h1 class="text-4xl font-bold">Add New Taxon
        </h1>
        <div id="sciname-preview" class="mt-4">
            <h1 class="text-2xl font-bold">Sciname will be saved as:
                <span class="text-primary"
                    x-text="unit1Label + ' ' + this.$refs?.unitname1?.value + (this.$refs?.unitname2?.value ? ' ' + this.$refs?.unitname2.value : '') + (this.$refs?.unitname3?.value ? ' ' + this.$refs?.unitind3?.value + ' ' + this.$refs?.unitname3.value : '')"></span>
            </h1>
        </div>
        <form class="mt-4 flex flex-col items-center gap-4 w-full max-w-4xl"
            method="POST" action="{{ route('taxon.store') }}">
            @csrf
            <div class="w-3/4">
                <fieldset class="border border-base-300 rounded-md p-4 mb-4">
                    <legend class="text-2xl font-semibold">Optional Quick Parser
                    </legend>
                    <x-input label="Quick Parser" name="quickparser"
                        id="quickparser" value="" />
                    <x-button class="mt-2">Parse</x-button>
                </fieldset>
            </div>
            <fieldset class="w-full border border-base-300 rounded-md p-4 mb-4">

                <legend class="text-2xl font-semibold">Add New Taxon</legend>

                <div class="w-3/4">
                    <x-select label="Taxon Rank" name="rankid" id="rankid"
                        @select-changed="rankid = $event.detail.value; console.log('Select changed to:', $event.detail.value)"
                        :items="$allTaxonRanks
                            ->map(
                                fn($r) => [
                                    'title' => $r->rankname,
                                    'value' => $r->rankid,
                                    'disabled' => false,
                                ],
                            )
                            ->toArray()" />
                </div>
                <div id="unit1" class="flex items-center gap-2 mb-4">
                    <div class="flex flex-col">
                        <label class="text mb-1" for="unitind1-toggle"
                            x-text="unit1Label + ' Decorator'"></label>
                        <x-select name="unitind1" id="unitind1"
                            :items="$indContent" :default="0" />
                    </div>
                    <div class="flex flex-col">
                        <label
                            class="text-base-content text-base text-bold mb-1"
                            for="unitname1">
                            <span x-text="unit1Label + ' Name'"></span>
                            <span
                                class="vertical-align text-error italic pr-1">*</span>
                        </label>
                        <x-input required name="unitname1" id="unitname1"
                            value="" x-ref="unitname1" />
                    </div>
                </div>

                <div id="unit2" class="flex items-center gap-2 mb-4"
                    x-show="!rankid || parseInt(rankid) >= 220">
                    <div class="flex flex-col">
                        <label class="text mb-1" for="unitind2-toggle"
                            x-text="unit2Label + ' Decorator'"></label>
                        <x-select name="unitind2" id="unitind2"
                            :items="$indContent" :default="0" />
                    </div>
                    <div class="flex flex-col">
                        <label
                            class="text-base-content text-base text-bold mb-1"
                            for="unitname2"><span
                                x-text="unit2Label + ' Name'"></span>
                        </label>
                        <x-input name="unitname2" id="unitname2" value=""
                            x-ref="unitname2" />
                    </div>
                </div>
                <div id="unit3" class="inline-flex items-center gap-2"
                    x-show="rankid && parseInt(rankid) >= 230">
                    <x-input label="Infraspecific designation" name="unitind3"
                        id="unitind3" placeholder="spp., var., forma, etc."
                        x-ref="unitind3" />
                    <x-input label="Infraspecific Epithet" name="unitname3"
                        id="unitname3" value="" x-ref="unitname3" />
                </div>
                <div class="w-1/2">
                    <x-input label="Author" name="author" id="author"
                        value="" />
                </div>
                <div class="w-1/2">
                    <x-taxa-search :label="'Parent Taxon'" required id="parentname"
                        name="parentname" :hide_selector="true" :label_classes="''"
                        :hide_synonyms_checkbox="true" />
                </div>
                <div class="w-1/2 mt-2">
                    <x-input label="Notes" name="notes" id="notes"
                        value="" />
                </div>
                <div class="w-1/2">
                    <x-input label="Source" name="source" id="source"
                        value="" />
                </div>
                <div class="w-1/2">
                    <x-select label="Locality Security" name="securitystatus"
                        id="securitystatus" :items="$securityOptions" />
                </div>
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
    </div>
</x-layout>
