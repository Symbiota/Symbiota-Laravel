@props([
    'upperTaxonomyEditInfo' => null,
])

@php
    $acceptedArr = $upperTaxonomyEditInfo['acceptedArr'] ?? [];
    $acceptedTid = array_key_first($acceptedArr);
    $currentTid = $upperTaxonomyEditInfo['tid'] ?? null;
    $tidAccepted = (int) ($upperTaxonomyEditInfo['isAccepted'] ?? 0) === 1 ? $currentTid : $acceptedTid;
    $parentNameFull = $upperTaxonomyEditInfo['parentNameFull'] ?? '';
@endphp

@if(!$upperTaxonomyEditInfo)
    <div class="alert alert-warning">
        <span
            class="text-2xl"
            style="color: var(--color-warning-darker)"
            >{{ __('taxonomy_taxonomyloader.TAXON_NOT_FOUND') }}</span
        >
    </div>
@else
    <form name="taxstatusform" action="{{ route('taxon.edit-upper') }}" method="post">
        @csrf
        @if(($upperTaxonomyEditInfo['rankId'] ?? 0) > 140 && $upperTaxonomyEditInfo['family'] ?? false)
            <div id="family-info" name="family-info">
                <div class="editLabel">{{ __('taxonomy_taxonomyloader.FAMILY') }}:</div>
                <div class="tsedit">{{ $upperTaxonomyEditInfo['family'] ?? '' }}</div>
            </div>
        @endif

        <div id="parent-link" name="parent-link" class="mt-3 mb-3">
            <span class="text-bold">{{ __('taxonomy_taxonomyloader.CURRENT_PARENT_TAXON') }} : </span>
            <i>{{ $parentNameFull }}</i>
            <x-link href="{{ url('taxon/' . ($upperTaxonomyEditInfo['parentTid'] ?? '') . '/edit') }}">
                {{ __('projects.EDIT') }}
            </x-link>
        </div>
        <x-taxa-search
            class="font-bold"
            label="{{ __('taxonomy_taxoneditor.PARENT_TAXON') }}"
            required
            id="new-parent-taxon"
            name="new-parent-taxon"
            tidName="newparenttid"
            hide_selector="true"
            hide_synonyms_checkbox="true"
            :taxa_value="$upperTaxonomyEditInfo['parentName']"
            :tid_value="$upperTaxonomyEditInfo['parentTid']"
        />
        <div id="hidden-inputs-container" name="hidden-inputs-container" class="mb-3">
            <input type="hidden" name="tid" value="{{ $currentTid }}" />
            <input type="hidden" name="taxauthid" value="{{ $upperTaxonomyEditInfo['taxauthid'] }}" />
            <input type="hidden" name="tidaccepted" value="{{ $tidAccepted }}" />
            <input type="hidden" name="tabindex" value="1" />
            <input type="hidden" name="submitaction" value="updatetaxstatus" />
        </div>
        <x-button type="submit" name="taxstatuseditsubmit">
            {{-- TODO run submitTaxStatusForm(this.form) on click --}}
            {{ __('taxonomy_taxonomyloader.SUBMIT_UPPER_EDITS') }}
        </x-button>
    </form>
@endif
