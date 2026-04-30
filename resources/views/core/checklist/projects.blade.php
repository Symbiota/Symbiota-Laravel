<div class="flex flex-col gap-4">
    <div class="flex">
        <span class="font-bold text-2xl">
            {{ __('checklists_checklistadmin.INVENTORYPROJECTS') }}
        </span>

        <span class="flex flex-grow justify-end">
            <x-modal>
                <x-slot name="button" :disabled="count($userProjects) === 0" >
                    Add Project
                </x-slot>
                <x-slot name="title" class="text-2xl">
                    {{ __('checklists_checklistadmin.LINKTOPROJECT') }}
                </x-slot>
                <x-slot name="body">
                    <form class="flex flex-col gap-4">
                        <x-select id="pid" class="w-full" label="Select a Project" :items="$userProjects" />
                        <input type="hidden" name="submitaction" value="addToProject" aria-label="{{__('checklists_checklistadmin.SUBMIT_BUTTON') }}" />

                        <div class="flex align-items gap-2">
                            <x-button type="submit">Add</x-button>
                            <x-button variant="error" type="button">Cancel</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        </span>
    </div>
    <hr />
    @foreach($projects as $linked_pid => $name)
    <div class="flex items-center gap-2 border p-2 border-base-300 bg-base-200 rounded-md">
        <span class="flex-grow">
            <x-link href="{{ url('projects/' . $pid) }}">{{ $name }}</x-link>
        </span>
        @can('PROJ_ADMIN', $linked_pid)
        <form method="post">
            @csrf
            <input name="pid" type="hidden" value="{{ $linked_pid }}" />
            <input name="submitaction" type="hidden" value="deleteProject" />
            <button type="submit">
                <x-icons.delete class="cursor-pointer" />
            <button>
        </form>
        @endcan
    </div>
    @endforeach
</div>
