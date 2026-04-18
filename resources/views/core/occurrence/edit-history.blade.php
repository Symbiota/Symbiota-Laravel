@props(['occurrence', 'editHistory'])
<div>
    <x-text-label :label="__('editor_occurrencetabledisplay.RECORD_ENTERED_BY')">
        {{ $occurrence->recordEnteredBy ?? 'Not Recorded' }}
    </x-text-label>
    <x-text-label :label="__('reports_labelmanager.DATE_ENTER')">
        {{ $occurrence->dateEntered }}
    </x-text-label>
    <x-text-label :label="__('reports_labelmanager.DATE_MOD')">
        {{ $occurrence->dateLastModified }}
    </x-text-label>
    <div class="font-bold text-xl mt-4">
        {{ __('individual.INTERNAL_EDITS') }}
    </div>
    <hr/>
    @foreach ($editHistory as $editGroup)
        <div class="border-b border-base-300 py-2 flex flex-col gap-2">
            <div>
                <x-text-label :label="__('profile_usermanagement.EDITOR')">
                    {{ $editGroup['name'] }}
                </x-text-label>
                <x-text-label :label="__('individual.DATE')">
                    {{ $editGroup['initialTimestamp'] }}
                </x-text-label>
                <x-text-label :label="__('individual.APPLIED_STATUS')">
                    {{ $editGroup['appliedStatus'] ? __('individual.APPLIED'): __('individual.NOT_APPLIED') }}
                </x-text-label>
            </div>

            <div class="ml-4 flex flex-col gap-2">
                @foreach ($editGroup['edits'] as $edit)
                    <div class="flex gap-2">
                        <span class="bg-base-300 px-2 rounded-full">
                            {{ (!$edit->fieldValueOld? 'Added': 'Updated') }}
                        </span>
                        {{-- TODO Solve lower case storage to new model keys issue to impl current tag --}}
                        <x-text-label :label="$edit->fieldName">
                            @if(!$edit->fieldValueOld)
                                {{ $edit->fieldValueNew }}
                            @else
                                <span>{{ $edit->fieldValueOld }}</span>
                                <i class="fa-solid fa-arrow-right"></i>
                                @if($edit->fieldValueNew)
                                <span>{{ $edit->fieldValueNew }}</span>
                                @else
                                <span class="bg-base-300 px-2 rounded-full">
                                    {{ __('None') }}
                                </span>
                                @endif
                            @endif
                        </x-text-label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    <div class="mt-2">
        {{ __('individual.EDIT_NOTE') }}
    </div>
</div>
