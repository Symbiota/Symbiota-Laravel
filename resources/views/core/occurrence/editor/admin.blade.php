@props(['occurrence', 'edit_history' => []])
<div class="flex flex-col gap-4">
    <x-occurrence.edit-history
        :occurrence="$occurrence"
        :edit_history="App\Models\OccurrenceEdit::getGroupedByEdit($occurrence->occid)"
    />

    {{-- Transfer Record --}}
    <x-fieldset :legend="__('exsiccati.TRANSFER_SPEC')">
        <x-select
            label="Target Collection"
            :items="[
            ['title' => 'Select Collection', 'value' => null, 'disabled' => false]
        ]"
        />
        <x-button>{{ __('exsiccati.TRANSFER_SPEC') }}</x-button>
    </x-fieldset>

    {{-- Delete Occurrence Record --}}

    <x-fieldset :legend="__('includes_admintab.DEL_RECORD')">
        <p>{{ __('includes_admintab.REC_MUST_EVALUATE') }}</p>
        <x-button variant="error"> {{ __('includes_admintab.EVALUATE_FOR_DEL') }} </x-button>
        <div class="font-bold">{{ __('includes_admintab.MEDIA_LINKS') }}:</div>
        <div class="font-bold">{{ __('includes_admintab.CHECKLIST_LINKS') }}:</div>
    </x-fieldset>
</div>
