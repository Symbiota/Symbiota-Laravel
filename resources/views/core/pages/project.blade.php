@props(['project', 'checklists' => []])

@php
global $LANG;
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('projects/index');
@endphp

<x-margin-layout x-data="{ descOpen: false}">
    <div>
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Species Inventories', 'href' => url('/checklists') ],
        $project->projname
    ]" />
    </div>

    {{-- Todo Add Edit and when to show mapping button logic --}}
    <div class="flex items-center gap-4 h-fit">
        <h1 class="text-4xl font-bold text-primary">{{ $project->projname }}</h1>

        <div class="flex flex-grow justify-end gap-4 items-center">
            <x-button hx-boost="true" href="{{ url('checklists/map') }}?pid={{ $project->pid }}">
                <i class="flex-end fas fa-earth-americas"></i>
                Map
            </x-button>

            @can('PROJ_ADMIN', $project->pid)
            <x-button href="{{ url('projects/' . $project->pid . '/edit') }}">
                <i class="flex-end fas fa-edit"></i>
                Edit
            </x-button>
            @endcan
        </div>
    </div>

    <x-text-label :label="$LANG['PROJMANAG']">
        {{ $project->managers }}
    </x-text-label>

    @isset($project->fulldescription)
    <p>{{ $project->fulldescription }}</p>
    @endisset

    @isset($project->notes)
    <x-text-label :label="$LANG['NOTES']">{{ $project->notes }}</x-text-label>
    @endisset

    <div>
        <div class="flex gap-2 items-center">
            <div class="text-lg font-bold">Research checklists</div>
            <x-tooltip text="What is a Research Species List">
                <button class="h-6 w-6 bg-base-200 rounded-full border border-base-content font-bold text-base-content cursor-pointer" @click="descOpen = !descOpen">?</button>
            </x-tooltip>
        </div>
        <div x-cloak x-show="descOpen">
            {{ $LANG['RESCHECKQUES'] }}
        </div>
    </div>

    <div class="flex flex-col gap-2 pl-4">
        @foreach ($checklists as $checklist)
        <li class="">
            <x-link href="{{ url('/checklists/' . $checklist->clid) }}">
                {{$checklist->name}}
            </x-link>
            |
            {{-- Todo find conditions for when this would not exist if any --}}
            <x-link
                href="{{legacy_url('/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species')}}">
                    <x-tooltip class="inline" text="Opens species list as an interactive key">
                        <i class="pl-1 text-base-content fa-solid fa-key"></i> Key
                    </x-tooltip>
            </x-link>
        </li>
        @endforeach
    </div>
</x-layout>
