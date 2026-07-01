@props(['project', 'checklists' => []])

@php
$hasMappableChecklist = false;
foreach($checklists as $checklist) {
    if($checklist->longCentroid && $checklist->latCentroid && $checklist->mapChecklist) {
        $hasMappableChecklist = true;
        break;
    }
}
@endphp

<x-margin-layout x-data="{ descOpen: false }">
    <div>
        <x-breadcrumbs
            :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        ['title' => __('checklists.SPECIES_INVENTORIES'), 'href' => url('/checklists') ],
        $project->projname
    ]"
        />
    </div>

    <div class="flex h-fit items-center gap-4">
        <h1 class="text-primary text-4xl font-bold">{{ $project->projname }}</h1>

        <div class="flex flex-grow items-center justify-end gap-4">
            @if($hasMappableChecklist)
                <x-button
                    hx-boost="true"
                    href="{{ url('checklists/map') }}?pid={{ $project->pid }}"
                    :title="__('projects.MAPREP')"
                >
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

    <x-text-label :label="__('projects.PROJMANAG')"> {{ $project->managers }} </x-text-label>

    @isset($project->fullDescription)
        <div>{!! Purify::clean($project->fullDescription) !!}</div>
    @endisset

    @isset($project->notes)
        <x-text-label :label="__('projects.NOTES')">{{ $project->notes }}</x-text-label>
    @endisset

    <div>
        <div class="flex items-center gap-2">
            <div class="text-lg font-bold">{{ __('projects.RESCHECK') }}</div>
            <x-tooltip :text="__('projects.QUESRESSPEC')">
                <button
                    class="bg-base-100 hover:bg-base-300 border-base-content text-base-content flex h-6 w-6 cursor-pointer items-center rounded-full border font-bold"
                    @click="descOpen = !descOpen"
                >
                    <span class="h-6 w-6">?<span>
                </button>
            </x-tooltip>
        </div>
        <div x-cloak x-show="descOpen">{{ __('projects.RESCHECKQUES') }}</div>
    </div>

    @if(config('portal.module_checklist_key'))
        <div>
            <span>{{ __('projects.THE') }}</span>
            <i class="text-base-content fa-solid fa-key pl-1"></i>
            <span>{{ __('projects.SYMBOLOPEN') }}</span>.
        </div>
    @endif

    <div class="flex flex-col gap-2 pl-4">
        @foreach($checklists as $checklist)
            <li>
                <x-link href="{{ url('/checklists/' . $checklist->clid) }}"> {{ $checklist->name }} </x-link>
                @php $defaultSettings=json_decode($checklist->defaultSettings ?? '{}') @endphp
                @if($defaultSettings->activatekey ?? config('portal.module_checklist_key') ?? false)
                    |
                    <x-link
                        href="{{ legacy_url('/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species') }}"
                    >
                        <x-tooltip class="inline" :text="__('projects.SYMBOLOPEN')">
                            <i class="text-base-content fa-solid fa-key pl-1"></i> {{ __('ident_key.KEY') }}
                        </x-tooltip>
                    </x-link>
                @endif
            </li>
        @endforeach
    </div>
</x-margin-layout>
