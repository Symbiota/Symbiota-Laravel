@props(['project', 'checklists' => []])
<x-layout>
    <h1 class="text-4xl font-boldt text-primary">{{$project->projname}}</h1>
    <div>
        <span class="font-bold">Projects Mangers:</span>
        {{$project->managers }}
    </div>
    <div class="text-lg font-bold">Research checklists</div>

    <div>
        @foreach ($checklists as $checklist)
        <li>
        <x-link href="{{url(config('portal.name') . '/checklists/checklist.php?clid=' . $checklist->clid . '&pid=' . $project->pid) }}">{{$checklist->name}}</x-link>
        |
        {{-- Todo find conditions for when this would not exist if any --}}
        <x-link href="{{url(config('portal.name') . '/ident/key.php?clid=' . $checklist->clid . '&pid=' . $project->pid . '&taxon=All+Species')}}">
            Key<i class="pl-1 text-base-content fa-solid fa-key"></i></x-link>
        </li>
        @endforeach
    </div>
</x-layout>
