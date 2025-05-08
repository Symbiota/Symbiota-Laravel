@props(['project', 'checklists' => []])
<x-layout class="lg:w-3/4 md:w-full mx-auto">
    <div class="mb-4">
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Species Inventories', 'href' => url('/checklists') ],
        $project->projname
    ]" />
    </div>

    <div class="flex items-center mb-4 gap-4">
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
    {{-- Todo Add Edit and when to show mapping button logic --}}
    <div>
        @if(isset($project->managers) && $project->managers)
        <div>
            <span class="text-lg font-bold">Projects Mangers:</span>
            {{$project->managers }}
        </div>
        @endif
        <div class="flex gap-2 items-center">
            <div class="text-lg font-bold">Research checklists</div>
            <x-tooltip text="What is a Research Species List">
                <x-popover>
                    <x-slot name="icon">
                        ?
                    </x-slot>
                    <div>
                        Research checklists are pre-compiled by biologists. This is a very controlled method for
                        building a species list, which allows for specific specimens to be linked to the species names
                        within the checklist and thus serve as vouchers. Specimen vouchers are proof that the species
                        actually occurs in the given area. If there is any doubt, one can inspect these specimens for
                        verification or annotate the identification when necessary
                    </div>
                </x-popover>
            </x-tooltip>
        </div>

        <div class="flex flex-col gap-2 pl-4">
            @foreach ($checklists as $checklist)
            <li class="">
                <x-link hx-boost="true" href="{{url('/checklists/' . $checklist->clid) }}">{{$checklist->name}}</x-link>
                |
                {{-- Todo find conditions for when this would not exist if any --}}
                <x-link
                    href="{{url(config('portal.name') . '/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species')}}">
                        <x-tooltip class="inline" text="Opens species list as an interactive key">
                            Key<i class="pl-1 text-base-content fa-solid fa-key"></i>
                        </x-tooltip>
                </x-link>
            </li>
            @endforeach
        </div>
    </div>
</x-layout>
