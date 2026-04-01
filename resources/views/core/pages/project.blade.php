@props(['project', 'checklists' => []])

@php
global $IS_KEY_MOD_IS_ACTIVE;

$hasMappableChecklist = false;
foreach($checklists as $checklist) {
    if($checklist->longCentroid && $checklist->latCentroid && $checklist->mapChecklist) {
        $hasMappableChecklist = true;
        break;
    }
}
@endphp

<x-margin-layout x-data="{ descOpen: false}">
    <div>
        <x-breadcrumbs :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        ['title' => __('checklists.SPECIES_INVENTORIES'), 'href' => url('/checklists') ],
        $project->projname
    ]" />
    </div>

    <div class="flex items-center gap-4 h-fit">
        <h1 class="text-4xl font-bold text-primary">{{ $project->projname }}</h1>

        <div class="flex flex-grow justify-end gap-4 items-center">
            @if($hasMappableChecklist)
            <x-button hx-boost="true" href="{{ url('checklists/map') }}?pid={{ $project->pid }}" :title="__('projects.MAPREP')">
                <i class="flex-end fas fa-earth-americas"></i>
                {{ __('checklists.MAP') }}
            </x-button>
            @endif

            @can('PROJ_ADMIN', $project->pid)
            <x-button href="{{ url('projects/' . $project->pid . '/edit') }}" :title="__('projects.TOGGLEEDIT')">
                <i class="flex-end fas fa-edit"></i>
                {{ __('projects.EDIT') }}
            </x-button>
            @endcan
        </div>
    </div>

    <x-text-label :label="__('projects.PROJMANAG')">
        {{ $project->managers }}
    </x-text-label>

    @isset($project->fullDescription)
    <div>{!! Purify::clean($project->fullDescription) !!}</div>
    @endisset

    @isset($project->notes)
    <x-text-label :label="__('projects.NOTES')">{{ $project->notes }}</x-text-label>
    @endisset

    <div>
        <div class="flex gap-2 items-center">
            <div class="text-lg font-bold">{{ __('projects.RESCHECK') }}</div>
            <x-tooltip text="What is a Research Species List">
                <button class="flex items-center h-6 w-6 bg-base-100 hover:bg-base-300 rounded-full border border-base-content font-bold text-base-content cursor-pointer" @click="descOpen = !descOpen">
                    <span class="h-6 w-6">?<span>
                </button>

            </x-tooltip>
        </div>
        <div x-cloak x-show="descOpen">
            {{ __('projects.RESCHECKQUES') }}
        </div>
    </div>

    <div class="flex flex-col gap-2 pl-4">
        @foreach ($checklists as $checklist)
        <li>
            <x-link href="{{ url('/checklists/' . $checklist->clid) }}">
                {{ $checklist->name }}
            </x-link>
            @php $defaultSettings=json_decode($checklist->defaultSettings ?? '{}') @endphp
            @if($defaultSettings->activatekey ?? $IS_KEY_MOD_IS_ACTIVE ?? false)
            |
            <x-link
                href="{{legacy_url('/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species')}}">
                    <x-tooltip class="inline" :text="__('projects.SYMBOLOPEN')">
                        <i class="pl-1 text-base-content fa-solid fa-key"></i> {{ __('ident_key.KEY') }}
                    </x-tooltip>
            </x-link>
            @endif
        </li>
        @endforeach
    </div>
</x-layout>
