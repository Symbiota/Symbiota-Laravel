@props(['project'])

@php
include_once(legacy_path('/classes/ImInventories.php'));

$projManager = new ImInventories('write');
$projManager->setPid($project->pid);

$userItems = itemize($projManager->getUserArr());
$managerArr = $projManager->getManagers('ProjAdmin', 'fmprojects', request('pid'));

$checklistItems = [];
foreach($projManager->getChecklistArr() as $clid => $checklist) {
    $checklistItems[] = item($clid, $checklist['name']);
}

@endphp

<x-margin-layout>
    <div>
        <x-breadcrumbs :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        ['title' => __('checklists.SPECIES_INVENTORIES'), 'href' => url('/checklists') ],
        $project->projname
    ]" />
    </div>

    <div class="flex items-center">
        <h1 class="text-4xl font-bold text-primary">{{ $project->projname }}</h1>
        <div class="flex flex-grow justify-end gap-4">
            <x-button href="{{ url('projects/' . $project->pid) }}">
                {{__('Public View')}}
            </x-button>
        </div>
    </div>

    <x-errors :errors="$errors ?? []"/>

    <x-tabs :active="0" :tabs="[__('projects.METADATA'), __('projects.INVMANAG'), __('projects.CHECKMANAG')]">
        <div class="flex flex-col gap-4">
            <fieldset class="flex flex-col gap-4">
                <div>
                    <legend class="text-2xl font-bold text-primary">{{ __('projects.EDIT') }}</legend>
                    <hr/>
                </div>
                <form method="post" action="{{ url('projects/' . $project->pid . '/edit') }}" class="flex flex-col gap-4">
                    <x-projects.form :project="$project" />
                    <x-button>{{ __('projects.SUBMITEDIT') }}</x-button>
                </form>
            </fieldset>

            <fieldset class="flex flex-col gap-4">
                <div>
                    <legend class="text-2xl font-bold text-primary">{{ __('projects.DELPROJECT') }}</legend>
                    <hr/>
                </div>
                @fragment('project_delete_form')
                <form id="project_delete_form" hx-delete="{{ url('projects/' . $project->pid . '/edit') }}" hx-confirm="{{ __('projects.CONFIRMDEL') }}" class="flex flex-col gap-4">
                    <x-button :disabled="$project->managers || count($checklists) > 0" variant="error">{{ __('projects.SUBMITDELETE') }}</x-button>

                    @csrf
                    @if($project->managers || count($checklists) > 0)
                    <div class="bg-warning text-warning-content p-2 rounded-md">
                        @if($project->managers)
                        {{ __('projects.DELCONDITION1') }}
                        @elseif(count($checklists) > 0)
                        {{ __('projects.DELCONDITION2') }}
                        @endif
                    </div>
                    @endif
                </form>
            </fieldset>
            <x-errors :errors="$delete_errors ?? []"/>
            @endfragment
        </div>

        <div id="inventory_managers" class="flex flex-col gap-4" x-cloak>
        @fragment('managers')
            <fieldset class="flex flex-col gap-4">
                <div>
                    <legend class="text-2xl font-bold text-primary">{{ __('projects.INVENTORY_PROJECT_MANAGERS') }}</legend>
                    <hr/>
                </div>
                @csrf

                <div class="flex flex-col gap-2">
                @foreach ($managerArr as $uid => $name)
                    <div class="flex items-center p-2 bg-base-200 border border-base-300">
                        <span>{{ $name }}</span>
                        <span class="flex-grow flex justify-end">
                            <x-icons.delete hx-delete="{{ url('projects/' . $project->pid . '/managers/' . $uid) }}" hx-target="#inventory_managers" hx-include="input[name='_token']"/>
                        </span>
                    </div>
                    @endforeach
                </div>
            </fieldset>

            <fieldset class="flex flex-col gap-4">
                <div>
                    <legend class="text-2xl font-bold text-primary">{{ __('projects.ADD_NEW_MANAGER') }}</legend>
                    <hr/>
                </div>
                <form hx-post="{{ url('projects/' . $project->pid . '/managers') }}" class="flex flex-col gap-4" hx-target="#inventory_managers">
                    @csrf
                    <x-select class="w-full" id="uid" :items="$userItems"/>
                    <x-button>{{ __('projects.ADD_TO_MANAGER_LIST') }}</x-button>
                </form>
            </fieldset>
            <x-errors :errors="$add_user_errors ?? []"/>
        @endfragment
        </div>

        <div id="inventory_checklists" class="flex flex-col gap-4" x-cloak>
        @fragment('checklists')
            <fieldset class="flex flex-col gap-4">
                <div>
                    <legend class="text-2xl font-bold text-primary">{{ __('projects.ADD_A_CHECKLIST') }}</legend>
                    <hr/>
                </div>
                <form class="flex flex-col gap-4"
                    hx-post="{{ url('projects/' . $project->pid . '/checklists') }}"
                    hx-target="#inventory_checklists"
                >
                    @csrf
                    <x-select class="w-full" :label="__('projects.SELECT_CHECKLIST_TO_ADD')" id="clid" :items="$checklistItems"/>
                    <x-button>{{ __('projects.ADD_CHECKLIST') }}</x-button>
                </form>
            </fieldset>

            <fieldset class="flex flex-col gap-4">
                <div>
                    <legend class="text-2xl font-bold text-primary">{{ __('projects.DELETE_A_CHECKLIST') }}</legend>
                    <hr/>
                </div>
                <div class="flex flex-col gap-2">
                    @foreach ($checklists as $checklist)
                    <div class="flex items-center p-2 bg-base-200 border border-base-300">
                        <x-link href="{{ url('/checklists/' . $checklist->clid) }}">
                            {{ $checklist->name }}
                        </x-link>
                        <span class="flex-grow flex justify-end">
                            @csrf
                            <x-icons.delete hx-delete="{{ url('projects/' . $project->pid . '/checklists/' . $checklist->clid) }}" hx-target="#inventory_checklists" hx-include="input[name='_token']" />
                        </span>
                    </div>
                    @endforeach
                </div>
            </fieldset>

            <x-errors :errors="$checklist_form_errors ?? []"/>
        @endfragment
        </div>
    </x-tabs>
</x-margin-layout>
