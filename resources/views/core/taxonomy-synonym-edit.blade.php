@props(['mode'=>'edit', 'taxonInfo'=>null])
<div
    id="taxonomy-synonym-container"
    name="taxonomy-synonym-container"
    x-data="{
        isValid: false,
        errorMessage: '',
        acceptedstr: '',
        tidaccepted: '',
        init() {
            this.validateAcceptedStatusForm();
        },
        async validateAcceptedStatusForm() {
            this.acceptedstr = this.$el.querySelector('#synonym-acceptedstr')?.value ?? '';
            this.tidaccepted = this.$el.querySelector('[name=tidaccepted]')?.value ?? '';
            console.log('Validating form with acceptedstr:', this.acceptedstr, 'and tidaccepted:', this.tidaccepted);
            if(this.tidaccepted === '{{ $taxonInfo->tid }}'){
                this.isValid = false;
                this.errorMessage = '* {{ __('taxonomy_taxoneditor.CANNOT_BE_OWN_ACCEPTED') }}';
                return;
            }
            if(!this.acceptedstr || !this.tidaccepted){
                this.isValid = false;
                this.errorMessage = '* {{ __('taxonomy_taxoneditor.TARGET_TAXON_MISSING') }}';
                return;
            }
            this.isValid = true;
        },
    }"
>
    <x-fieldset>
        <legend class="text-lg font-bold">
            {{ $taxonInfo->sciname ?? __('taxonomy_taxoneditor.ACCEPTANCE_STATUS') }}
        </legend>
        @if($taxonInfo->isAccepted ?? false)
            <span class="text-success-darker">{{ __('taxonomy_taxoneditor.ACCEPTED') }}</span>
        @else
            <span class="text-error-darker">{{ __('taxonomy_taxoneditor.SYNONYM') }}</span>
        @endif
    </x-fieldset>
    @if(count($taxonInfo->acceptedArr ?? []) > 0)
        <x-fieldset>
            <legend class="text-lg font-bold">{{ __('taxonomy_taxoneditor.ACCEPTED_TAXON') }}</legend>
            <ul>
                @foreach($taxonInfo->acceptedArr ?? [] as $tidAccepted => $linkedTaxonArr)
                    <li id="acclink-{{ $tidAccepted }}">
                        <x-link href="{{ url('/taxon/' . $tidAccepted) }}">
                            <i>{{ $linkedTaxonArr["sciname"] }}</i>
                        </x-link>
                        {{ $linkedTaxonArr["author"] ?? '' }}
                        @if(count($taxonInfo->acceptedArr ?? []) > 1)
                            <span class="hidden">
                                <a href="{{ url('/taxon/' . $tid) }}">
                                    {{-- @TODO add delete icon --}}
                                </a>
                            </span>
                        @endif
                        @if($linkedTaxonArr["usagenotes"])
                            <div><u>Notes</u>: {{ $linkedTaxonArr["usagenotes"] }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-fieldset>
    @endif
    @if(count($taxonInfo->synonyms ?? []) > 0)
        <x-fieldset>
            <legend class="text-lg font-bold">{{ __('taxonomy_taxoneditor.SYNONYMS') }}</legend>
            <ul>
                @foreach($taxonInfo->synonyms as $tid => $synonym)
                    <li>
                        <div class="mb-2 flex items-center">
                            <x-link href="{{ url('/taxon/' . $tid) }}">
                                <span>{{ $synonym['sciname'] ?? __('taxonomy_taxoneditor.NAME_MISSING') }}</span>
                            </x-link>
                            <x-button
                                type="button"
                                class="ml-2 text-sm"
                                @click="
                            console.log('Clicked here. @TODO implement a small editor form');
                            "
                                >{{ __('taxonomy_taxoneditor.EDIT_SYNONYM_LINKS') }}</x-button
                            >
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-fieldset>
    @endif
    @if($taxonInfo->isAccepted ?? false)
        <form
            id="taxonomic-status-edit-form"
            method="POST"
            action="{{ route('taxon.changeAccepted') }}"
            @change="await validateAcceptedStatusForm()"
        >
            @csrf
            <x-input type="hidden" name="mode" id="mode" :value="$mode" />
            <x-input type="hidden" name="edit-type" id="edit-type" value="synonymedits" />
            <x-input type="hidden" name="tid" id="tid" :value="$taxonInfo->tid ?? ''" />
            <x-fieldset>
                <legend class="text-lg font-bold">{{ __('taxonomy_taxoneditor.CONVERT_TO_SYNONYM') }}</legend>
                <x-taxa-search
                    class="font-bold"
                    label="{{ __('taxonomy_taxoneditor.ACCEPTED_NAME') }}"
                    required
                    id="synonym-acceptedstr"
                    name="acceptedstr"
                    tidName="tidaccepted"
                    hide_selector="true"
                    hide_synonyms_checkbox="true"
                />
                <x-input
                    name="unacceptabilityreason"
                    id="unacceptabilityreason"
                    label="{{ __('taxonomy_taxoneditor.REASON') }}"
                />
                <x-input name="notes" id="notes" label="{{ __('projects.NOTES') }}" />
                <x-button
                    x-bind:disabled="!isValid"
                    type="submit"
                    class="mt-4"
                    x-text=" isValid ? '{{ __('taxonomy_taxoneditor.CHANGE_STAT_NOT_ACCEPT') }}' : '{{ __('taxonomy_taxonomyloader.SUBMISSION_DISABLED') }}'"
                >
                </x-button>
                <span
                    x-show="!isValid"
                    class="text-red-500"
                    id="error-container"
                    name="error-container"
                    x-text="errorMessage"
                ></span>
                <span>*{{ __('taxonomy_taxoneditor.SYNONYMS_TRANSFERRED') }}</span>
            </x-fieldset>
        </form>
    @else
        <form method="POST" action="{{ route('taxon.changeToNotAccepted') }}">
            @csrf
            <x-input type="hidden" name="mode" id="mode" :value="$mode" />
            <x-input type="hidden" name="edit-type" id="edit-type" value="synonymedits" />
            <x-input type="hidden" name="tid" id="tid" :value="$taxonInfo->tid ?? ''" />
            <x-fieldset>
                <legend class="text-lg font-bold">{{ __('taxonomy_taxoneditor.CHANGE_TO_ACCEPTED') }}</legend>
                <x-radio
                    name="switchacceptance"
                    id="switchacceptance"
                    label="{{ __('taxonomy_taxoneditor.SWITCH_ACCEPTANCE') }}"
                    :options="[['value' => '1', 'label' => __('taxonomy_taxoneditor.YES')], ['value' => '0', 'label' => __('taxonomy_taxoneditor.NO')]]"
                    required
                />
                <x-button type="submit" class="mt-4" x-text="'{{ __('taxonomy_taxoneditor.CHANGE_STATUS_ACCEPTED') }}'">
                </x-button>
                @php
                    $firstKey = array_key_first($taxonInfo->acceptedArr ?? []);
                @endphp
                <x-input type="hidden" name="new-tid" id="new-tid" :value="$firstKey ?? ''" />
            </x-fieldset>
        </form>
    @endif
</div>
