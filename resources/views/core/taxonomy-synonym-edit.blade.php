@props(['mode'=>'edit', 'taxonInfo'=>null])
<div id="taxonomy-synonym-container" name="taxonomy-synonym-container"
    x-data="{isValid: false,
    acceptedstr: '',
    tidaccepted: '',
    init() {
        this.validateAcceptedStatusForm();
    },
    async validateAcceptedStatusForm() {
        this.acceptedstr = this.$el.querySelector('#acceptedstr')?.value ?? '';
        this.tidaccepted = this.$el.querySelector('[name=tidaccepted]')?.value ?? '';
        console.log('Validating form with acceptedstr:', this.acceptedstr, 'and tidaccepted:', this.tidaccepted);
        if (this.acceptedstr && this.tidaccepted) {
            this.isValid = true;
        } else {
            this.isValid = false;
        }
    }}"
    >
    <form
        id="taxonomic-status-edit-form"
        method="POST"
        action="{{ route('taxon.update', ['tid' => $taxonInfo->tid ?? '']) }}"
        @change="await validateAcceptedStatusForm()"
    >
        @csrf
        <x-input type="hidden" name="mode" id="mode" :value="$mode" />
        <x-input type="hidden" name="edit-type" id="edit-type" value="synonymedits" />
        @if(count($taxonInfo->synonyms ?? []) < 1)
            <span>{{ __('taxonomy_taxoneditor.NO_SYN_LINKED_TAXON') }}</span>
        @else
            @foreach ($taxonInfo->synonyms as $synonym)
                <div class="mb-2 flex items-center gap-2 rounded border p-2">
                    <span>{{ $synonym->sciName ?? 'Name missing' }}</span>
                </div>
            @endforeach
        @endif
        <x-fieldset>
            <legend class="text-lg font-bold">Edit Taxonomic Status</legend>
            <span>Status: {{ $taxonInfo->isAccepted ? 'Accepted' : 'Synonym' }}</span>
            <x-taxa-search
                class="font-bold"
                label="{{ __('taxonomy_taxoneditor.ACCEPTED_NAME') }}"
                required
                id="acceptedstr"
                name="acceptedstr"
                :tidName="'tidaccepted'"
                :hide_selector="true"
                :hide_synonyms_checkbox="true"
            />
            <x-input name="unacceptabilityreason" id="unacceptabilityreason" label="{{ __('taxonomy_taxoneditor.REASON') }}" />
            <x-input name="notes" id="notes" label="{{ __('projects.NOTES') }}" />
            <x-button x-bind:disabled="!isValid" type="submit" class="mt-4"
            x-text=" isValid ? '{{ $taxonInfo->isAccepted ? __('taxonomy_taxoneditor.CHANGE_STAT_NOT_ACCEPT') : __('taxonomy_taxoneditor.CHANGE_STAT_ACCEPT') }}' : '{{ __('taxonomy_taxonomyloader.SUBMISSION_DISABLED') }}'">
            </x-button>
            <span>*{{ __('taxonomy_taxoneditor.SYNONYMS_TRANSFERRED') }}</span>
        </x-fieldset>
    </form>
</div>
