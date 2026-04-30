@props(['occurrence', 'edit_history'])
@php
$lower_case_lookup = [];
foreach($occurrence->toArray() as $key => $value) {
    $lower_case_lookup[strtolower($key)] = $value;
}
@endphp
<div>
    <x-text-label :label="__('editor_occurrencetabledisplay.RECORD_ENTERED_BY')">
        {{ $occurrence->recordEnteredBy ?? 'Not Recorded' }}
    </x-text-label>
    <x-text-label :label="__('reports_labelmanager.DATE_ENTER')"> {{ $occurrence->dateEntered }} </x-text-label>
    <x-text-label :label="__('reports_labelmanager.DATE_MOD')"> {{ $occurrence->dateLastModified }} </x-text-label>
    <div class="mt-4 text-xl font-bold">{{ __('individual.INTERNAL_EDITS') }}</div>
    <hr />
    @foreach($edit_history as $editGroup)
        <div class="border-base-300 flex flex-col gap-2 border-b py-2">
            <div>
                <x-text-label :label="__('profile_usermanagement.EDITOR')"> {{ $editGroup['name'] }} </x-text-label>
                <x-text-label :label="__('individual.DATE')"> {{ $editGroup['initialTimestamp'] }} </x-text-label>
                <x-text-label :label="__('individual.APPLIED_STATUS')">
                    {{ $editGroup['appliedStatus'] ? __('individual.APPLIED'): __('individual.NOT_APPLIED') }}
                </x-text-label>
            </div>

            <div class="ml-4 flex flex-col gap-2">
                @foreach($editGroup['edits'] as $edit)
                    @php
                        $currentValue = $lower_case_lookup[$edit->fieldName] ?? $occurrence->{$edit->fieldName} ?? false;
                    @endphp
                    <div class="flex gap-2">
                        <span class="bg-base-300 rounded-full px-2">
                            {{ (!$edit->fieldValueOld? __('Added'): __('Updated')) }}
                        </span>
                        <x-text-label :label="$edit->fieldName">
                            @if(!$edit->fieldValueOld)
                                {{ $edit->fieldValueNew }}
                            @else
                                <span>{{ $edit->fieldValueOld }}</span>
                                @if($currentValue == $edit->fieldValueOld)
                                    <span class="bg-base-300 rounded-full px-2 capitalize">
                                        {{ __('individual.CURRENT') }}
                                    </span>
                                @endif
                                <i class="fa-solid fa-arrow-right"></i>
                                @if($edit->fieldValueNew)
                                    <span>{{ $edit->fieldValueNew }}</span>
                                @else
                                    <span class="bg-base-300 rounded-full px-2"> {{ __('None') }} </span>
                                @endif
                            @endif
                            @if($currentValue == $edit->fieldValueNew)
                                <span class="bg-base-300 rounded-full px-2 capitalize">
                                    {{ __('individual.CURRENT') }}
                                </span>
                            @endif
                        </x-text-label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    <div class="mt-2">{{ __('individual.EDIT_NOTE') }}</div>
</div>
