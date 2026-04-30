@props(['users', 'editors', 'pid'])
<div class="flex flex-col gap-2">
    <div class="flex">
        <span class="text-2xl font-bold"> {{ __('checklists_checklistadmin.CURREDIT') }} </span>

        <span class="flex flex-grow justify-end">
            <x-modal>
                <x-slot name="button">
                    {{ __('checklists_checklistadmin.ADDEDITOR') }}
                </x-slot>
                <x-slot name="title" class="text-2xl">
                    {{ __('checklists_checklistadmin.ADDNEWUSER') }}
                </x-slot>
                <x-slot name="body">
                    <form class="flex flex-col gap-4">
                        <x-select id="editoruid" class="w-full" label="Select User" :items="$users" />
                        <input
                            type="hidden"
                            name="submitaction"
                            value="addEditor"
                            aria-label="{{ __('checklists_checklistadmin.ADDEDITOR') }}"
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
    <div class="flex flex-col gap-2">
        @foreach($editors as $uid => $editor)
            <div class="border-base-300 bg-base-200 flex items-center gap-2 rounded-md border p-2">
                <span class="flex-grow">{{ $editor['name'] }}</span>
                <form method="post">
                    @csrf
                    <input name="pid" type="hidden" value="{{ $pid }}" />
                    <input name="deleteuid" type="hidden" value="{{ $uid }}" />
                    <input name="submitaction" type="hidden" value="DeleteEditor" />
                    <button type="submit">
                        <x-icons.delete class="cursor-pointer" />
                        <button>
                </form>
            </div>
        @endforeach
    </div>
</div>
