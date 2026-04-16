@props([
    'mode' => 'create',
    'kingdoms' => [],
    'allTaxonRanks' => [],
    'indContent' => [],
    'securityOptions' => [],
    'errors' => [],
    'canCreateOrEdit' => false,
    'taxonInfo' => null,
    'parentName' => '',
    'acceptedName' => '',
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
    @if (!$canCreateOrEdit)
        <div class="flex flex-col items-center justify-center mb-4">
            <p>{{ __('taxonomy_taxonomyloader.NO_PERMISSION_CREATE') }}</p>
        </div>
    @endif
    <div x-data="{ tid: @js(request()->query('tid')), mode: @js($mode) }">
        @if ($canCreateOrEdit)
            <div
                class="flex flex-col items-center justify-center"
                x-data="{
                    unit1Label: 'Genus',
                    unit2Label: 'Species',
                    rankid: @js($mode === 'edit' && $taxonInfo ? (int)$taxonInfo->rankID : 220),
                    isValid: false,
                    validationMessage: '',
                    allTaxonRanks: @js($allTaxonRanks),
                    init() {
                        this.validate();
                        this.$watch('rankid', (newValue, oldValue) => {
                            this.updateLabels();
                            this.validate();
                        });
                    },
                    updateLabels() {
                        if (window.updateLabels) {
                            window.updateLabels(this);
                        }
                    },
                    async validate() {
                        if (window.verifyLoadForm && window.validateTaxonEditForm) {
                            const targetValidationFunction = this.mode === 'create' ? window.verifyLoadForm : window.validateTaxonEditForm;
                            const validationResult = await targetValidationFunction(this, false, @js($taxonInfo), @js(__('taxonomy_taxonomyloader.SCI_NAME_RANK_REQUIRED')), @js(__('taxonomy_taxonomyloader.ALREADY_EXISTS')), @js(__('taxonomy_taxonomyloader.PARENT_TAXON_REQUIRED')), @js(__('taxonomy_taxonomyloader.PARENT_ID_NOT_SET')), @js(__('taxonomy_taxonomyloader.ACC_NAME_NEEDS_VALUE')));
                            console.log('deleteMe a1 validationResult is: ');
                            console.log(validationResult);
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
                    {{ $mode==='create' ? __('taxonomy_taxonomyloader.TAXON_LOADER') : __('profile_tpeditor.EDIT_TAXON') }}
                </h1>
                @if($mode === 'create')
                    <div class="mt-4">
                        <h1 class="text-2xl font-bold">
                            {{ __('taxonomy_taxonomyloader.SCINAME_SAVED_AS') }}:
                            <span id="sciname-preview" class="text-primary"></span>
                        </h1>
                    </div>
                @endif
                <form id="taxon-form"
                    class="mt-4 flex flex-col items-center gap-4 w-full max-w-4xl"
                    method="POST" action="{{ $mode === 'create' ? route('taxon.store') : route('taxon.update') }}"
                    @change="validate(); '{{ $mode }}' === 'create' ? updateScinameDisplay() : null;">
                    @csrf
                    <x-input type="hidden" name="mode" id="mode" :value="$mode" />
                    <div class="w-3/4">
                        @if($mode === 'create')
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
                        @endif
                    </div>
                    <fieldset
                        class="w-full border border-base-300 rounded-md p-4 mb-4">

                        <legend class="text-2xl font-semibold">
                            {{ $mode === 'create' ? __('taxonomy_taxonomyloader.TAXON_LOADER') : __('profile_tpeditor.EDIT_TAXON') }}
                        </legend>

                        <div class="w-3/4">
                            <x-select
                                label="{{ __('taxonomy_taxonomyloader.TAXON_RANK') }}"
                                name="rankid" id="rankid" :defaultValue="$mode === 'edit' && $taxonInfo ? $taxonInfo->rankID : 220"
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
                                    :items="$indContent" :default="0"
                                    :defaultValue="$mode === 'edit' && $taxonInfo ? ($taxonInfo->unitInd1 ?? '') : ''" />
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
                                <x-input  required name="unitname1"
                                    id="unitname1" value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitName1 ?? '') : '' }}"
                                    x-ref="unitname1" />
                            </div>
                        </div>

                        <div id="unit2" class="flex items-center gap-2 mb-4"
                            x-show="(!rankid || parseInt(rankid) >= 220)">
                            <div class="flex flex-col">
                                <label class="text mb-1" for="unitind2-toggle"
                                    x-text="unit2Label + ' {{ __('taxonomy_taxonomyloader.DECORATOR') }}'"></label>
                                <x-select name="unitind2" id="unitind2"
                                    :items="$indContent" :default="0"
                                    :defaultValue="$mode === 'edit' && $taxonInfo ? ($taxonInfo->unitInd2 ?? '') : ''" />
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
                                    value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitName2 ?? '') : '' }}" x-ref="unitname2"
                                    x-bind:required="!rankid || parseInt(rankid) >= 220" />
                            </div>
                        </div>
                        <div id="unit3"
                            class="inline-flex items-center gap-2"
                            x-show="rankid && parseInt(rankid) >= 230">
                            <x-input label="Infraspecific designation"
                                name="unitind3" id="unitind3"
                                placeholder="spp., var., forma, etc."
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitInd3 ?? '') : '' }}"
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
                                    value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitName3 ?? '') : '' }}" x-ref="unitname3"
                                    x-bind:required="rankid && parseInt(rankid) >= 230" />
                            </div>
                        </div>
                        <div id="cultivarEpithet-div"
                            class="inline-flex items-center gap-2"
                            x-show="rankid && parseInt(rankid) >= 300">
                            <x-input
                                label="{{ __('taxonomy_taxonomyloader.CULTIVAR_EPITHET') }}"
                                name="cultivarEpithet" id="cultivarEpithet"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->cultivarEpithet ?? '') : '' }}" x-ref="cultivarEpithet" />
                        </div>
                        <div id="tradeName-div"
                            class="inline-flex items-center gap-2"
                            x-show="rankid && parseInt(rankid) >= 300">
                            <x-input
                                label="{{ __('fieldterms_occurrenceterms.TRADE_NAME') }}"
                                name="tradeName" id="tradeName"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->tradeName ?? '') : '' }}" x-ref="tradeName" />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                label="{{ __('glossary_addterm.AUTHOR') }}"
                                name="author" id="author"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->author ?? '') : '' }}" />
                        </div>
                        <div class="w-1/2">
                            <x-taxa-search
                                label="{{ __('taxonomy_taxoneditor.PARENT_TAXON') }}"
                                required id="parentname" name="parentname"
                                :tidName="'parenttid'" :hide_selector="true"
                                :hide_synonyms_checkbox="true"
                                :taxa_value="$mode === 'edit' && $taxonInfo ? $parentName : ''"
                                :tid_value="$mode === 'edit' && $taxonInfo ? ($taxonInfo->parenttid ?? '') : ''" />
                        </div>
                        <div class="w-1/2 mt-2">
                            <x-input label="{{ __('projects.NOTES') }}"
                                name="notes" id="notes"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->notes ?? '') : '' }}" />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                label="{{ __('glossary_addterm.SOURCE') }}"
                                name="source" id="source"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->source ?? '') : '' }}" />
                        </div>
                        <div class="w-1/2">
                            <x-select
                                label="{{ __('taxonomy_taxoneditor.LOC_SECURITY') }}"
                                name="securitystatus" id="securitystatus"
                                :defaultValue="$mode === 'edit' && $taxonInfo ? $taxonInfo->securityStatus : 0"
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
                            :default_value="$mode === 'edit' && $taxonInfo ? ($taxonInfo->tid == $taxonInfo->tidaccepted ? 1 : 0) : 1" />
                        {{-- blade-formatter-enable --}}
                        </fieldset>
                        <div id="accdiv" class="{{ $mode === 'edit' && $taxonInfo && $taxonInfo->tid != $taxonInfo->tidaccepted ? '' : 'hidden' }}">
                            <div>
                                <div class="left-column">
                                    <label for="acceptedstr">
                                        {{ __('taxonomy_taxoneditor.ACCEPTED_TAXON') }}:
                                    </label>
                                </div>
                                <input id="acceptedstr" name="acceptedstr"
                                    type="text" class="search-bar-long"
                                    value="{{ $mode === 'edit' && $taxonInfo && $taxonInfo->tid != $taxonInfo->tidaccepted ? $acceptedName : '' }}" />
                                <input id="tidaccepted" name="tidaccepted"
                                    type="hidden" value="{{ $mode === 'edit' && $taxonInfo ? $taxonInfo->tidaccepted : '' }}" />
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
                                    class='search-bar-long'
                                    value='{{ $mode === "edit" && $taxonInfo ? ($taxonInfo->UnacceptabilityReason ?? "") : "" }}' />
                            </div>
                        </div>
                        <div>
                            <span class="text-sm italic text-base-content">* =
                                {{ __('taxonomy_taxonomyloader.REQUIRED') }}
                                Field</span>
                        </div>  
                        <x-button name="submitButton" id="submitButton" class="mt-2" x-bind:disabled="!isValid"
                            x-text=" isValid ? '{{ $mode === 'create' ? __('taxonomy_taxonomyloader.SUBMIT_NEW_NAME') : __('profile_userprofile.SUBMIT_EDITS') }}' : '{{ __('taxonomy_taxonomyloader.SUBMISSION_DISABLED') }}'"></x-button>
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
