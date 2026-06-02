@props(['determinations' => [
    ['sciname' => 'Pinus aristata', 'author' => 'Engelm.', 'date'=> 's.d.', 'determiner' =>
    'unknown','isCurrent' => true],
    ['sciname' => 'Pinus aristata', 'author' => 'Engelm.', 'date'=> 's.d.', 'determiner' =>
    'unknown','isCurrent' => false]
]])
@php
$confidence_options = [
    item(0, '0 - ' . __('includes_determinationtab.UNLIKELY')),
    item(1, '1 - ' . __('editor_batchdeterminations.LOW')),
    item(2, '2 - ' . __('editor_batchdeterminations.LOW')),
    item(3, '3 - ' . __('editor_batchdeterminations.LOW')),
    item(4, '4 - ' . __('editor_batchdeterminations.MEDIUM')),
    item(5, '5 - ' . __('editor_batchdeterminations.MEDIUM')),
    item(6, '6 - ' . __('editor_batchdeterminations.MEDIUM')),
    item(7, '7 - ' . __('editor_batchdeterminations.HIGH')),
    item(8, '8 - ' . __('editor_batchdeterminations.HIGH')),
    item(9, '9 - ' . __('editor_batchdeterminations.HIGH')),
    item(10, '10 - ' . __('includes_determinationtab.ABSOLUTE')),
]
@endphp
<div class="flex flex-col gap-4">
    <x-fieldset :legend="__('includes_determinationtab.ID_CONFIDENCE')">
        <x-select
            class="w-60"
            :label="__('includes_determinationtab.CONFIDENCE_IN_DET')"
            defaultValue="5"
            :items="$confidence_options"
        />
        <x-input :label="__('projects.NOTES')" />
        <x-button>{{ __('includes_determinationtab.SUBMIT_VERIFY_EDITS') }}</x-button>
    </x-fieldset>

    <x-fieldset :legend="__('includes_determinationtab.ADD_NEW_DET')">
        <x-input :label="__('individual.ID_QUALIFIER')" />
        <x-input required :label="__('imagelib_imgdetails.SCIENTIFIC_NAME')" />
        <x-input :label="__('editor_occurrencetabledisplay.SCI_NAME_AUTHOR')" />
        <x-select
            class="w-60"
            :label="__('includes_determinationtab.CONFIDENCE_IN_DET')"
            defaultValue="5"
            :items="$confidence_options"
        />
        <x-input required :label="__('individual.DETERMINER')" />
        <x-input required :label="__('individual.DATE')" />
        <x-input :label="__('editor_batchdeterminations.REFERENCE')" />
        <x-input :label="__('projects.NOTES')" />

        <x-checkbox :label="__('includes_determinationtab.MAKE_THIS_CURRENT')" />
        <x-checkbox :label="__('includes_determinationtab.ADD_TO_PRINT')" />
        <x-button> {{ __('includes_determinationtab.SUBMIT_DET') }} </x-button>
    </x-fieldset>

    <x-fieldset :legend="__('individual.DET_HISTORY')">
        @foreach($determinations as $determination)
            <div class="p-2">
                <div>
                    <span>{{ $determination['sciname'] }} {{ $determination['author'] }}</span>
                    @if($determination['isCurrent'])
                        <span class="text-error">{{ __('includes_determinationtab.CURRENT_DET') }}</span>
                    @endif
                    <x-icons.edit />
                </div>
                <div class="flex gap-4">
                    <x-text-label :label="__('individual.DETERMINER')">
                        {{ $determination['determiner'] }}
                    </x-text-label>
                    <x-text-label :label="__('individual.DATE')"> {{ $determination['date'] }} </x-text-label>
                </div>
            </div>
            @if(!$loop->last)
                <hr />
            @endif
        @endforeach
    </x-fieldset>
</div>
