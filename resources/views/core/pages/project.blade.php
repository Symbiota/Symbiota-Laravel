@props(['project', 'checklists' => []])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Species Inventories', 'href' => url('/checklists') ],
        $project->projname
    ]" />
    </div>

    <div class="flex items-center mb-4">
        <h1 class="text-4xl font-bold text-primary">{{ $project->projname }}</h1>
        <div class="flex flex-grow justify-end gap-4">
            <a href="{{ url('projects/' . $project->pid . '/edit') }}">
                <i class="flex-end fas fa-edit"></i>
                Edit
            </a>
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
        <div class="text-lg font-bold">Research checklists</div>

        <div class="flex flex-col gap-2">
            @foreach ($checklists as $checklist)
            <li>
                <x-link
                    href="{{url(config('portal.name') . '/checklists/checklist.php?clid=' . $checklist->clid . '&pid=' . $project->pid) }}">{{$checklist->name}}</x-link>
                |
                {{-- Todo find conditions for when this would not exist if any --}}
                <x-link
                    href="{{url(config('portal.name') . '/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species')}}">
                    Key<i class="pl-1 text-base-content fa-solid fa-key"></i></x-link>
            </li>
            @endforeach
        </div>
    </div>
</x-layout>
