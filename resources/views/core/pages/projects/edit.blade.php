@props(['project'])

@php
global $LANG, $IS_KEY_MOD_IS_ACTIVE;
include_once(legacy_path('/classes/utilities/Language.php'));
include_once(legacy_path('/classes/ImInventories.php'));

Language::load([
    'projects/index',
    'checklists/index'
]);

$projManager = new ImInventories('write');
$projManager->setPid($project->pid);

@endphp

<x-margin-layout>
    <div>
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Species Inventories', 'href' => url('/checklists') ],
        $project->projname
    ]" />
    </div>

    <div class="flex items-center">
        <h1 class="text-4xl font-bold text-primary">{{ $project->projname }}</h1>
        <div class="flex flex-grow justify-end gap-4">
            <x-button href="{{ url('projects/' . $project->pid) }}">
                Public View
            </x-button>
        </div>
    </div>

    <x-tabs :active="0" :tabs="[$LANG['METADATA'], $LANG['INVMANAG'], $LANG['CHECKMANAG']]">
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-2xl font-bold text-primary">{{ $LANG['EDIT'] }}</h3>
                <hr/>
            </div>
            <form method="post" action="{{ url('projects/' . $project->pid . '/edit') }}" class="flex flex-col gap-4">
                @csrf
                <x-input id="projname" :label="$LANG['PROJNAME']" :value="$project->projname"/>
                <x-input id="managers" :label="$LANG['MANAG']" :value="$project->managers" />
                <x-rich-editor id="fulldescription" :label="$LANG['DESCRIP']">
                    {{ Purify::clean($project->fullDescription) }}
                </x-rich-editor>
                <x-input id="notes" :label="$LANG['NOTES']" :value="$project->notes"/>
                <x-select id="ispublic" :defaultValue="$project->isPublic" :label="$LANG['ACCESS']" :items="[
                    [ 'value' => 0, 'title' => $LANG['PRIVATE'], 'disabled' => false ],
                    [ 'value' => 1, 'title' => $LANG['PUBLIC'], 'disabled' => false ]
                ]" />
                <x-button>{{ $LANG['SUBMITEDIT'] }}</x-button>
            </form>

            <div>
                <h3 class="text-2xl font-bold text-primary">{{ $LANG['DELPROJECT'] }}</h3>
                <hr/>
            </div>
            @fragment('project_delete_form')
            <form id="project_delete_form" hx-delete="{{ url('projects/' . $project->pid . '/edit') }}" hx-confirm="{{ $LANG['CONFIRMDEL'] }}" class="flex flex-col gap-4">
                <x-button :disabled="$project->managers || count($checklists) > 0" variant="error">{{ $LANG['SUBMITDELETE'] }}</x-button>

                @csrf
                @if($project->managers || count($checklists))
                <div class="bg-warning text-warning-content p-2 rounded-md">
                    @if($project->managers)
                    {{ $LANG['DELCONDITION1'] }}
                    @elseif(count($checklists) > 0)
                    {{ $LANG['DELCONDITION2'] }}
                    @endif
                </div>
                @endif
            </form>
            @isset($delete_errors)
            <x-errors :errors="$delete_errors"/>
            @endisset
            @endfragment
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-2xl font-bold text-primary">{{ $LANG['INVENTORY_PROJECT_MANAGERS'] }}</h3>
                <hr/>
            </div>
            <div class="flex flex-col gap-2">
            @foreach (['thing 1', 'thing 2'] as $key => $value)
            <div class="flex items-center p-2 bg-base-200 border border-base-300">
                <span>{{ $value }}</span>
                <span class="flex-grow flex justify-end">
                    <x-icons.delete/>
                </span>
            </div>
            @endforeach
            </div>

            <div>
                <h3 class="text-2xl font-bold text-primary">{{ $LANG['ADD_NEW_MANAGER'] }}</h3>
                <hr/>
            </div>
            <form class="flex flex-col gap-4">
                <x-select class="w-full" id="user" :items="[
                    [ 'value' => 0, 'title' => 'user', 'disabled' => false ]
                ]"/>
                <x-button>{{ $LANG['ADD_TO_MANAGER_LIST'] }}</x-button>
            </form>
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-2xl font-bold text-primary">{{ $LANG['ADD_A_CHECKLIST'] }}</h3>
                <hr/>
            </div>
            <form class="flex flex-col gap-4">
                <x-select class="w-full" :label="$LANG['SELECT_CHECKLIST_TO_ADD']" id="user" :items="[
                    [ 'value' => 0, 'title' => 'user' ]
                ]"/>
                <x-button>{{ $LANG['ADD_CHECKLIST'] }}</x-button>
            </form>

            <div>
                <h3 class="text-2xl font-bold text-primary">{{ $LANG['DELETE_A_CHECKLIST'] }}</h3>
                <hr/>
            </div>
            <form class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                @foreach ($checklists as $checklist)
                <div class="flex items-center p-2 bg-base-200 border border-base-300">
                    <x-link href="{{ url('/checklists/' . $checklist->clid) }}">
                        {{ $checklist->name }}
                    </x-link>
                    <span class="flex-grow flex justify-end">
                        <x-icons.delete/>
                    </span>
                </div>
                @endforeach
                </div>
            </form>
        </div>
    </x-tabs>
</x-margin-layout>
