@props([
    'kingdoms' => [],
    'allTaxonRanks' => [],
    'indContent' => [],
    'securityOptions' => [],
    'errors' => [],
    'canCreate' => false,
])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('')],
            [
                'title' => 'Taxononmic Tree View',
                'href' => legacy_url('/taxa/taxonomy/taxonomydisplay.php'),
            ],
            ['title' => __('taxonomy_taxonomyloader.CREATE_TAXON')],
        ]" />
    </div>
    @if (!$canCreate)
        <div class="flex flex-col items-center justify-center mb-4">
            <p>{{ __('taxonomy_taxonomyloader.NO_PERMISSION_CREATE') }}</p>
        </div>
    @endif
    <div x-data="{ tid: @js(request()->query('tid')), mode: @js(request()->query('mode')) }">
        @if ($canCreate)
            <div class="flex flex-col items-center justify-center alert alert-success mb-4"
                id="successful-creation"
                x-show="tid !== null && !isNaN(Number(tid))">
                <p>Taxon created successfully with ID (TODO this should go to
                    taxon
                    editor page): <span x-text="tid"></span>
                </p>
            </div>
            <div x-show="mode === 'edit'">Edit mode!</div>
            <div x-show="tid === null || isNaN(Number(tid))"
                class="flex flex-col items-center justify-center"
                x-init=" validate();
                 $watch('rankid', (newValue, oldValue) => {
                     console.log('Watcher triggered! Old:', oldValue, 'New:', newValue);
                     updateLabels();
                     validate();
                 });" x-data="{
                    unit1Label: 'Genus',
                    unit2Label: 'Species',
                    rankid: 220,
                    isValid: false,
                    validationMessage: '',
                    allTaxonRanks: @js($allTaxonRanks),
                    updateLabels() {
                        if (window.updateLabels) {
                            window.updateLabels(this);
                        }
                    },
                    async validate() {
                        if (window.validateTaxonForm) {
                            const validationResult = await window.validateTaxonForm(this);
                            this.isValid = validationResult.isValid;
                            this.validationMessage = validationResult.message;
                        }
                    },
                    updateScinameDisplay() {
                        if (window.updateScinameDisplay) {
                            window.updateScinameDisplay();
                        }
                    },
                    async parseName() {
                        if (window.parseName) {
                            await window.parseName();
                        }
                    },
                }">
                <h1 class="text-4xl font-bold">
                    {{ __('taxonomy_taxonomyloader.TAXON_LOADER') }}
                </h1>
                <div class="mt-4">
                    <h1 class="text-2xl font-bold">
                        {{ __('taxonomy_taxonomyloader.SCINAME_SAVED_AS') }}:
                        <span id="sciname-preview" class="text-primary"></span>
                    </h1>
                </div>
                <form id="taxon-form"
                    class="mt-4 flex flex-col items-center gap-4 w-full max-w-4xl"
                    method="POST" action="{{ route('taxon.store') }}"
                    @change="validate(); updateScinameDisplay();">
                    @csrf
                    <div class="w-3/4">
                        <fieldset
                            class="border border-base-300 rounded-md p-4 mb-4">
                            <legend class="text-2xl font-semibold">
                                {{ __('taxonomy_taxonomyloader.OPTIONAL_QUICK_PARSER') }}
                            </legend>
                            <x-input :label="__(
                                'taxonomy_taxonomyloader.QUICK_PARSER',
                            )" name="quickparser"
                                id="quickparser" value=""
                                @keydown.enter="await parseName(); await validate();" />
                            <x-button
                                @click="await parseName(); await validate();"
                                type="button"
                                class="mt-2">{{ __('taxonomy_taxonomyloader.RUN_QUICK_PARSE') }}</x-button>
                        </fieldset>
                    </div>
                    <fieldset
                        class="w-full border border-base-300 rounded-md p-4 mb-4">

                        <legend class="text-2xl font-semibold">
                            {{ __('taxonomy_taxonomyloader.TAXON_LOADER') }}
                        </legend>

                        <div class="w-3/4">
                            <x-select
                                label="{{ __('taxonomy_taxonomyloader.TAXON_RANK') }}"
                                name="rankid" id="rankid" :defaultValue="220"
                                onChange="rankid = $event.target.value"
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
                        <div id="unit1"
                            class="flex items-center gap-2 mb-4">
                            <div class="flex flex-col">
                                <label class="text mb-1" for="unitind1-toggle"
                                    x-text="unit1Label + ' {{ __('taxonomy_taxonomyloader.DECORATOR') }}'"></label>
                                <x-select name="unitind1" id="unitind1"
                                    :items="$indContent" :default="0" />
                            </div>
                            <div class="flex flex-col">
                                <label
                                    class="text-base-content text-base text-bold mb-1"
                                    for="unitname1">
                                    <span
                                        x-text="unit1Label + ' {{ __('taxonomy_taxonomyloader.NAME') }}'"></span>
                                    <span
                                        class="vertical-align text-error italic pr-1">*</span>
                                </label>
                                <x-input required name="unitname1"
                                    id="unitname1" value=""
                                    x-ref="unitname1" />
                            </div>
                        </div>

                        <div id="unit2" class="flex items-center gap-2 mb-4"
                            x-show="!rankid || parseInt(rankid) >= 220">
                            <div class="flex flex-col">
                                <label class="text mb-1" for="unitind2-toggle"
                                    x-text="unit2Label + ' {{ __('taxonomy_taxonomyloader.DECORATOR') }}'"></label>
                                <x-select name="unitind2" id="unitind2"
                                    :items="$indContent" :default="0" />
                            </div>
                            <div class="flex flex-col">
                                <label
                                    class="text-base-content text-base text-bold mb-1"
                                    for="unitname2">
                                    <div class="flex items-center gap-1">
                                        <span
                                            x-text="unit2Label + ' {{ __('taxonomy_taxonomyloader.NAME') }}'"></span>
                                        <span
                                            class="vertical-align text-error italic pr-1">*</span>
                                    </div>
                                </label>
                                <x-input name="unitname2" id="unitname2"
                                    value="" x-ref="unitname2"
                                    x-bind:required="!rankid || parseInt(rankid) >= 220" />
                            </div>
                        </div>
                        <div id="unit3"
                            class="inline-flex items-center gap-2"
                            x-show="rankid && parseInt(rankid) >= 230">
                            <x-input label="Infraspecific designation"
                                name="unitind3" id="unitind3"
                                placeholder="spp., var., forma, etc."
                                x-ref="unitind3" />
                            <div class="flex flex-col w-full">
                                <label
                                    class="text-base-content text-base text-bold mb-1"
                                    for="unitname3"><span
                                        x-text="'{{ __('taxonomy_taxonomyloader.INFRASPECIFIC_EPITHET') }}'"></span>
                                    <span
                                        class="vertical-align text-error italic pr-1">*</span>
                                </label>
                                <x-input name="unitname3" id="unitname3"
                                    value="" x-ref="unitname3"
                                    x-bind:required="rankid && parseInt(rankid) >= 230" />
                            </div>
                        </div>
                        <div id="cultivarEpithet-div"
                            class="inline-flex items-center gap-2"
                            x-show="rankid && parseInt(rankid) >= 300">
                            <x-input
                                label="{{ __('taxonomy_taxonomyloader.CULTIVAR_EPITHET') }}"
                                name="cultivarEpithet" id="cultivarEpithet"
                                value="" x-ref="cultivarEpithet" />
                        </div>
                        <div id="tradeName-div"
                            class="inline-flex items-center gap-2"
                            x-show="rankid && parseInt(rankid) >= 300">
                            <x-input
                                label="{{ __('fieldterms_occurrenceterms.TRADE_NAME') }}"
                                name="tradeName" id="tradeName"
                                value="" x-ref="tradeName" />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                label="{{ __('glossary_addterm.AUTHOR') }}"
                                name="author" id="author"
                                value="" />
                        </div>
                        <div class="w-1/2">
                            <x-taxa-search
                                label="{{ __('taxonomy_taxoneditor.PARENT_TAXON') }}"
                                required id="parentname" name="parentname"
                                :tidName="'parenttid'" :hide_selector="true"
                                :hide_synonyms_checkbox="true" />
                        </div>
                        <div class="w-1/2 mt-2">
                            <x-input label="{{ __('projects.NOTES') }}"
                                name="notes" id="notes"
                                value="" />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                label="{{ __('glossary_addterm.SOURCE') }}"
                                name="source" id="source"
                                value="" />
                        </div>
                        <div class="w-1/2">
                            <x-select
                                label="{{ __('taxonomy_taxoneditor.LOC_SECURITY') }}"
                                name="securitystatus" id="securitystatus"
                                :items="$securityOptions" />
                        </div>
                        <fieldset
                            class="border border-base-300 rounded-md p-4 mb-4">
                            <legend class="text-2xl font-semibold">
                                {{ __('taxonomy_taxonomyloader.ACCEPT_STATUS') }}
                            </legend>
                            {{-- blade-formatter-disable --}}
                        <x-radio name="acceptstatus" :options="[
                            [
                                'label' => __('taxonomy_taxoneditor.ACCEPTED'),
                                'value' => 1,
                            ],
                            [
                                'label' => __(
                                    'taxonomy_taxoneditor.NOT_ACCEPTED'
                                ),
                                'value' => 0,
                            ],
                        ]"
                            default_value="1" />
                        {{-- blade-formatter-enable --}}
                        </fieldset>
                        <div id="accdiv" class="hidden">
                            <div>
                                <div class="left-column">
                                    <label for="acceptedstr">
                                        {{ __('taxonomy_taxoneditor.ACCEPTED_TAXON') }}:
                                    </label>
                                </div>
                                <input id="acceptedstr" name="acceptedstr"
                                    type="text" class="search-bar-long" />
                                <input id="tidaccepted" name="tidaccepted"
                                    type="hidden" />
                            </div>
                            <div>
                                <div class="left-column">
                                    <label for="unacceptabilityreason">
                                        {{ __('taxonomy_taxoneditor.UNACCEPT_REASON') }}:
                                    </label>
                                </div>
                                <input type='text'
                                    id='unacceptabilityreason'
                                    name='unacceptabilityreason'
                                    class='search-bar-long' />
                            </div>
                        </div>
                        <div>
                            <span class="text-sm italic text-base-content">* =
                                {{ __('taxonomy_taxonomyloader.REQUIRED') }}
                                Field</span>
                        </div>
                        <x-button class="mt-2" x-bind:disabled="!isValid"
                            x-text=" isValid ? '{{ __('taxonomy_taxonomyloader.SUBMIT_NEW_NAME') }}' : '{{ __('taxonomy_taxonomyloader.SUBMISSION_DISABLED') }}'"></x-button>
                        <p><span id="validationMessage"
                                class="text-sm italic text-red-700"
                                x-text="validationMessage"></span>
                        </p>
                    </fieldset>
                </form>
            </div>
        @endif
    </div>

</x-layout>
