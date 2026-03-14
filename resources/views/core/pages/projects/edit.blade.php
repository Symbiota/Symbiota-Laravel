@props(['project'])

@php
global $LANG, $IS_KEY_MOD_IS_ACTIVE;
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load([
    'projects/index',
    'checklists/index'
]);
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
            <a href="{{ url('projects/' . $project->pid) }}">
                <i class="flex-end fas fa-edit"></i>
                Public View
            </a>
        </div>
    </div>

    <x-tabs :active="1" :tabs="[$LANG['METADATA'], $LANG['INVMANAG'], $LANG['CHECKMANAG']]">
        <div class="flex flex-col gap-4">
            <div>
                <h1 class="text-2xl font-bold text-primary">{{ $LANG['EDIT'] }}</h1>
                <hr/>
            </div>
            <form class="flex flex-col gap-4">
                <x-input id="projname" :label="$LANG['PROJNAME']" />
                <x-input id="managers" :label="$LANG['MANAG']" />
                <x-input id="fulldescription" :label="$LANG['DESCRIP']" />
                <x-input id="notes" :label="$LANG['NOTES']" />
                <x-select id="ispublic" :defaultValue="$project->isPublic" :label="$LANG['ACCESS']" :items="[
                    [ 'value' => 0, 'title' => $LANG['PRIVATE'] ],
                    [ 'value' => 1, 'title' => $LANG['PUBLIC'] ]
                ]" />
                <x-button>{{ $LANG['SUBMITEDIT'] }}</x-button>
            </form>

            <div>
                <h1 class="text-2xl font-bold text-primary">{{ $LANG['DELPROJECT'] }}</h1>
                <hr/>
            </div>
            <form class="flex flex-col gap-4">
                <x-button variant="error">{{ $LANG['SUBMITDELETE'] }}</x-button>

                <div class="bg-warning text-warning-content p-2 rounded-md">
                    @if($project->managers)
                    {{ $LANG['DELCONDITION1'] }}
                    @elseif($checklists)
                    {{ $LANG['DELCONDITION2'] }}
                    @endif
                </div>
            </form>
        </div>
        <div>
            TODO Inventory Managers
        </div>
        <div>
           Checklist Management
        </div>
    </x-tabs>


    {{-- Todo Add Edit and when to show mapping button logic --}}
    <div>
        <x-text-label :label="$LANG['PROJMANAG']">
            {{ $project->managers }}
        </x-text-label>

        @isset($project->fullDescription)
            <p>{{ $project->fullDescription }}</p>
        @endisset

        @isset($project->notes)
            <x-text-label :label="$LANG['NOTES']">{{ $project->notes }}</x-text-label>
        @endisset

        <div class="text-lg font-bold">Research checklists</div>
        <div class="flex flex-col gap-2 pl-4">
            @foreach ($checklists as $checklist)
            <li>
                <x-link
                    href="{{legacy_url('/checklists/checklist.php?clid=' . $checklist->clid . '&pid=' . $project->pid) }}">{{$checklist->name}}</x-link>
                |
                {{-- Todo find conditions for when this would not exist if any --}}
                <x-link
                    href="{{legacy_url('/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species')}}">
                    Key<i class="pl-1 text-base-content fa-solid fa-key"></i></x-link>
            </li>
            @endforeach
        </div>
    </div>
</x-margin-layout>
