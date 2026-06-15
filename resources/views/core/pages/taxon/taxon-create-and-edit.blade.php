@props([
    'mode' => 'create',
    'canCreateOrEdit' => false,
    'allTaxonRanks' => collect(),
    'indContent' => [],
    'securityOptions' => [],
    'securitystatusstart' => 0,
    'taxonInfo' => null,
    'parentName' => '',
    'acceptedName' => '',
    'includeTitle' => false,
    'editorTitle' => null,
])
@if(!$canCreateOrEdit)
    <div class="mb-4 flex flex-col items-center justify-center">
        <p>{{ __('taxonomy_taxonomyloader.NO_PERMISSION_CREATE') }}</p>
    </div>
@endif
<div x-data="{ tid: @js(request()->query('tid')), mode: @js($mode) }">
    @if($canCreateOrEdit)
        <div
            class="flex flex-col items-center justify-center"
            x-data="{
                unit1Label: 'Genus',
                unit2Label: 'Species',
                rankid: @js($mode === 'edit' && $taxonInfo ? (int)$taxonInfo->rankID : 220),
                acceptstatus: @js($mode === 'edit' && $taxonInfo ? ($taxonInfo->tid == $taxonInfo->tidaccepted ? 1 : 0) : 1),
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
                async validate(mode = @js($mode)) {
                    if (window.verifyLoadForm && window.validateTaxonEditForm) {
                        const targetValidationFunction = this.mode === 'create' ? window.verifyLoadForm : window.validateTaxonEditForm;
                        const validationResult = await targetValidationFunction(this, true, @js($taxonInfo), @js(__('taxonomy_taxonomyloader.SCI_NAME_RANK_REQUIRED')), @js(__('taxonomy_taxonomyloader.ALREADY_EXISTS')), @js(__('taxonomy_taxonomyloader.PARENT_TAXON_REQUIRED')), @js(__('taxonomy_taxonomyloader.PARENT_ID_NOT_SET')), @js(__('taxonomy_taxonomyloader.ACC_NAME_NEEDS_VALUE')), @js(__('taxonomy_taxonomyloader.MISSING_REQUIRED_TAXON_FIELD')));
                        this.isValid = validationResult.isValid;
                        console.log('Validation result:', validationResult);
                        this.validationMessage = validationResult.message;
                        if (mode === 'create') {
                            this.updateScinameDisplay();
                        }
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
            }"
        >
            @if($includeTitle)
                <h1 class="text-4xl font-bold">
                    {{ $mode==='create' ? __('taxonomy_taxonomyloader.TAXON_LOADER') : __('profile_tpeditor.EDIT_TAXON') }}
                </h1>
            @endif
            @if($mode === 'create')
                <div class="mt-4">
                    <h1 class="text-2xl font-bold">
                        {{ __('taxonomy_taxonomyloader.SCINAME_SAVED_AS') }}:
                        <span name="sciname-preview" id="sciname-preview" class="text-primary"></span>
                    </h1>
                </div>
            @endif
            <form
                id="taxon-form"
                class="mx-auto mt-4 flex w-full flex-col items-stretch"
                method="POST"
                action="{{ $mode === 'create' ? route('taxon.store') : route('taxon.update') }}"
                @change="await validate()"
            >
                @csrf
                <x-input type="hidden" name="mode" id="mode" :value="$mode" />
                @if($mode === 'edit')
                    <x-input type="hidden" name="edit-type" id="edit-type" value="taxonedits" />

                @endif
                <x-input
                    type="hidden"
                    name="securitystatusstart"
                    id="securitystatusstart"
                    :value="$securitystatusstart"
                />
                <div class="w-full">
                    @if($mode === 'create')
                        <x-fieldset :legend="__('taxonomy_taxonomyloader.OPTIONAL_QUICK_PARSER')">
                            <x-input
                                :label="__(
                                'taxonomy_taxonomyloader.QUICK_PARSER',
                            )"
                                name="quickparser"
                                id="quickparser"
                                value=""
                                @keydown.enter="
                                    await parseName();
                                    await validate();
                                "
                            />
                            <x-button
                                @click="
                                    await parseName();
                                    await validate();
                                "
                                type="button"
                                class="mt-2"
                                >{{ __('taxonomy_taxonomyloader.RUN_QUICK_PARSE') }}</x-button
                            >
                        </x-fieldset>
                    @endif
                </div>
                <x-fieldset class="w-full" :legend="($editorTitle ?? __('taxonomy_taxoneditor.TAXONOMY_EDITOR'))">
                    <div id="taxon-rank-container" name="taxon-rank-container" class="w-1/2">
                        <x-select
                            class="font-bold"
                            label="{{ __('taxonomy_taxonomyloader.TAXON_RANK') }}"
                            name="rankid"
                            id="rankid"
                            :defaultValue="$mode === 'edit' && $taxonInfo ? $taxonInfo->rankID : 220"
                            onChange="rankid = $event.target.value"
                            :items="$allTaxonRanks
                                ->map(
                                    fn($r) => [
                                        'title' => $r->rankname,
                                        'value' => $r->rankid,
                                        'disabled' => false,
                                    ],
                                )
                                ->toArray()"
                        />
                    </div>
                    <div id="unit1" class="mb-4 flex w-1/2 items-center gap-2">
                        <div class="flex flex-col">
                            <label
                                class="text mb-1 font-bold"
                                for="unitind1-toggle"
                                x-text="unit1Label + ' {{ __('taxonomy_taxonomyloader.DECORATOR') }}'"
                            ></label>
                            <x-select
                                name="unitind1"
                                id="unitind1"
                                :items="$indContent"
                                :default="0"
                                :defaultValue="$mode === 'edit' && $taxonInfo ? ($taxonInfo->unitInd1 ?? '') : ''"
                            />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-base-content mb-1 text-base font-bold" for="unitname1">
                                <span x-text="unit1Label + ' {{ __('taxonomy_taxonomyloader.NAME') }}'"></span>
                                <span class="vertical-align text-error pr-1 italic">*</span>
                            </label>
                            <x-input
                                required
                                name="unitname1"
                                id="unitname1"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitName1 ?? '') : '' }}"
                                x-ref="unitname1"
                            />
                        </div>
                    </div>

                    <div
                        id="unit2"
                        class="mb-4 flex w-1/2 items-center gap-2"
                        x-show="!rankid || parseInt(rankid) >= 220"
                    >
                        <div class="flex flex-col">
                            <label
                                class="text mb-1 font-bold"
                                for="unitind2-toggle"
                                x-text="unit2Label + ' {{ __('taxonomy_taxonomyloader.DECORATOR') }}'"
                            ></label>
                            <x-select
                                name="unitind2"
                                id="unitind2"
                                :items="$indContent"
                                :default="0"
                                :defaultValue="$mode === 'edit' && $taxonInfo ? ($taxonInfo->unitInd2 ?? '') : ''"
                            />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-base-content mb-1 text-base font-bold" for="unitname2">
                                <div class="flex items-center gap-1">
                                    <span x-text="unit2Label + ' {{ __('taxonomy_taxonomyloader.NAME') }}'"></span>
                                    <span class="vertical-align text-error pr-1 italic">*</span>
                                </div>
                            </label>
                            <x-input
                                name="unitname2"
                                id="unitname2"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitName2 ?? '') : '' }}"
                                x-ref="unitname2"
                                x-bind:required="!rankid || parseInt(rankid) >= 220"
                            />
                        </div>
                    </div>
                    <div id="unit3" class="flex items-center gap-2" x-show="rankid && parseInt(rankid) >= 230">
                        <x-input
                            label="Infraspecific designation"
                            name="unitind3"
                            id="unitind3"
                            placeholder="spp., var., forma, etc."
                            value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitInd3 ?? '') : '' }}"
                            x-ref="unitind3"
                        />
                        <div class="flex w-full flex-col">
                            <label class="text-base-content mb-1 text-base font-bold" for="unitname3"
                                ><span x-text="'{{ __('taxonomy_taxonomyloader.INFRASPECIFIC_EPITHET') }}'"></span>
                                <span class="vertical-align text-error pr-1 italic">*</span>
                            </label>
                            <x-input
                                name="unitname3"
                                id="unitname3"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->unitName3 ?? '') : '' }}"
                                x-ref="unitname3"
                                x-bind:required="rankid && parseInt(rankid) >= 230"
                            />
                        </div>
                    </div>
                    <div class="flex items-end gap-4" x-show="rankid && parseInt(rankid) >= 300">
                        <div id="cultivarEpithet-div">
                            <div class="flex w-full flex-col">
                                <label class="text-base-content mb-1 text-base font-bold" for="cultivarEpithet"
                                    ><span x-text="'{{ __('taxonomy_taxonomyloader.CULTIVAR_EPITHET') }}'"></span>
                                    <span class="vertical-align text-error pr-1 italic">*</span>
                                </label>
                                <x-input
                                    name="cultivarEpithet"
                                    id="cultivarEpithet"
                                    value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->cultivarEpithet ?? '') : '' }}"
                                    x-ref="cultivarEpithet"
                                />
                            </div>
                        </div>
                        <div id="tradeName-div">
                            <x-input
                                label="{{ __('fieldterms_occurrenceterms.TRADE_NAME') }}"
                                name="tradeName"
                                id="tradeName"
                                value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->tradeName ?? '') : '' }}"
                                x-ref="tradeName"
                            />
                        </div>
                    </div>
                    <div class="w-1/2">
                        <x-input
                            label="{{ __('glossary_addterm.AUTHOR') }}"
                            name="author"
                            id="author"
                            value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->author ?? '') : '' }}"
                        />
                    </div>
                    <div class="w-1/2 {{ $mode === 'edit' ? 'hidden' : '' }}">
                        <x-taxa-search
                            class="font-bold"
                            label="{{ __('taxonomy_taxoneditor.PARENT_TAXON') }}"
                            required
                            id="parentname"
                            name="parentname"
                            tidName="parenttid"
                            hide_selector="true"
                            hide_synonyms_checkbox="true"
                            :taxa_value="$mode === 'edit' && $taxonInfo ? $parentName : ''"
                            :tid_value="$mode === 'edit' && $taxonInfo ? ($taxonInfo->parenttid ?? '') : ''"
                        />
                    </div>
                    <div class="mt-2 w-1/2">
                        <x-input
                            label="{{ __('projects.NOTES') }}"
                            name="notes"
                            id="notes"
                            value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->notes ?? '') : '' }}"
                        />
                    </div>
                    <div class="w-1/2">
                        <x-input
                            label="{{ __('checklists_checklist.SOURCE') }}"
                            name="source"
                            id="source"
                            value="{{ $mode === 'edit' && $taxonInfo ? ($taxonInfo->source ?? '') : '' }}"
                        />
                    </div>
                    <div class="w-1/2">
                        <x-select
                            class="font-bold"
                            label="{{ __('taxonomy_taxoneditor.LOC_SECURITY') }}"
                            name="securitystatus"
                            id="securitystatus"
                            :defaultValue="$mode === 'edit' && $taxonInfo ? $taxonInfo->securityStatus : 0"
                            :items="$securityOptions"
                        />
                    </div>
                    <x-fieldset
                        id="acceptence-status"
                        name="acceptence-status"
                        :legend="__('taxonomy_taxoneditor.ACCEPTANCE_STATUS')"
                        x-on:change="
                            if ($event.target.name === 'acceptstatus') {
                                acceptstatus = parseInt($event.target.value);
                            }
                        "
                    >
                        {{-- blade-formatter-disable --}}
                        <x-radio
                            name="acceptstatus"
                            :options="[
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
                            :default_value="$mode === 'edit' && $taxonInfo ? ($taxonInfo->tid == $taxonInfo->tidaccepted ? 1 : 0) : 1"
                        />
                        {{-- blade-formatter-enable --}}
                    </x-fieldset>
                    <div id="accdiv" x-show="parseInt(acceptstatus) === 0">
                        <div>
                            <x-taxa-search
                                label="{{ __('taxonomy_taxoneditor.ACCEPTED_TAXON') }}"
                                id="acceptedstr"
                                name="acceptedstr"
                                tidName="tidaccepted"
                                hide_selector="true"
                                hide_synonyms_checkbox="true"
                                :taxa_value="$mode === 'edit' && $taxonInfo && $taxonInfo->tid != $taxonInfo->tidaccepted ? $acceptedName : ''"
                                :tid_value="$mode === 'edit' && $taxonInfo ? ($taxonInfo->tidaccepted ?? '') : ''"
                            />
                        </div>
                        <div>
                            <x-input
                                label="{{ __('taxonomy_taxoneditor.UNACCEPT_REASON') }}"
                                id="unacceptabilityreason"
                                name="unacceptabilityreason"
                                value='{{ $mode === "edit" && $taxonInfo ? ($taxonInfo->UnacceptabilityReason ?? "") : "" }}'
                            />
                        </div>
                    </div>
                    <div>
                        <span class="text-base-content text-sm italic"
                            >* = {{ __('taxonomy_taxonomyloader.REQUIRED') }} Field</span
                        >
                    </div>
                    <div id="submit-container" name="submit-container" class="w-1/2">
                        <x-button
                            name="submitButton"
                            id="submitButton"
                            class="mt-2"
                            x-bind:disabled="!isValid"
                            x-text=" isValid ? '{{ $mode === 'create' ? __('taxonomy_taxonomyloader.SUBMIT_NEW_NAME') : __('profile_userprofile.SUBMIT_EDITS') }}' : '{{ __('taxonomy_taxonomyloader.SUBMISSION_DISABLED') }}'"
                        ></x-button>
                        <span
                            id="validationMessage"
                            class="text-sm text-red-700 italic"
                            x-text="validationMessage"
                        ></span>
                    </div>
                </x-fieldset>
            </form>
        </div>
    @endif
</div>
