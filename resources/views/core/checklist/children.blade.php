@props([
    'clid',
    'childChecklists',
    'childChecklistsItems',
    'clManager'
])

<div class="flex flex-col gap-2">
    <div class="flex">
        <span class="text-2xl font-bold"> {{ __('checklists_checklistadminchildren.CHILD_CHECKLIST') }} </span>

        <span class="flex flex-grow justify-end">
            <x-modal>
                <x-slot name="button">
                    {{ __('checklists_checklistadminchildren.ADD_CHILD') }}
                </x-slot>
                <x-slot name="title" class="text-2xl">
                    {{ __('checklists_checklistadminchildren.ADD_CHILD') }}
                </x-slot>
                <x-slot name="body">
                    <form method="post" class="flex flex-col gap-4">
                        <x-select id="clidadd" class="w-full" label="Checklist" :items="$childChecklistsItems" />
                        <input
                            type="hidden"
                            name="submitaction"
                            value="addChildChecklist"
                            aria-label="{{ __('checklists_checklistadminchildren.ADD_CHILD') }}"
                        />
                        <div class="align-items flex gap-2">
                            <x-button type="submit">Add</x-button>
                            <x-button variant="error" type="button">Cancel</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        </span>
    </div>
    <hr />
    <p>{{ __('checklists_checklistadminchildren.CHILD_DESCRIBE') }}</p>
    @if($childChecklists)
        @foreach($childChecklists as $child_clid => $child)
            <div class="border-base-300 bg-base-200 flex items-center gap-2 rounded-md border p-2">
                <span class="flex-grow">
                    <x-link target="_blank" href="{{ url('checklists/' . $child_clid) }}">
                        {{ $child['name'] }}
                    </x-link>
                </span>
                <form method="post">
                    @csrf
                    <input name="cliddel" type="hidden" value="{{ $child_clid }}" />
                    <input name="submitaction" type="hidden" value="delchild" />
                    <button type="submit">
                        <x-icons.delete class="cursor-pointer" />
                        <button>
                </form>
            </div>
        @endforeach
    @else
        <p>{{ __('checklists_checklistadminchildren.NO_CHILDREN') }}</p>
    @endif

    <x-link href="{{ legacy_url('/profile/viewprofile.php?excludeparent=' . $clid) }}">
        {{ __('checklists_checklistadminchildren.CREATE_EXCLUSION_LIST') }}
    </x-link>
</div>
