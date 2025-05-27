@props(['checklists' => []])
<x-layout class="grid grid-cols-1 gap-4 lg:w-3/4 md:w-full mx-auto">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        'Species Inventories'
    ]" />
    <h1 class="text-4xl font-bold text-primary">Species Inventories</h1>

    @php
    $prev_pid = -1;
    $projects = [];
    foreach($checklists as $checklist) {
        if(empty($checklist->pid)) {
            if(empty($projects['misc'])) {
                $projects['misc'] = '';
            }
            $projects['misc'] = [ $checklist ];
        } else if(array_key_exists($checklist->pid, $projects)) {
            array_push($projects[$checklist->pid], $checklist);
        } else {
            $projects[$checklist->pid] = [ $checklist ];
        }
    }
    @endphp

    <div class="flex flex-col gap-4">
        @foreach($projects as $pid => $proj_checklists)
        <div>
            @if(!empty($proj_checklists))
            <div class="flex items-center gap-4">
                @if($proj_checklists[0]->projname)
                <x-link
                    href="{{url('/projects/' . $pid)}}"
                    class="text-2xl font-bold text-primary">
                    {{$proj_checklists[0]->projname ?? 'Misc'}}
                </x-link>
                @else
                <div class="text-2xl font-bold text-primary">
                    Misc Inventories
                </div>
                @endif

                {{-- Todo needs to check actually point data --}}
                @if ($proj_checklists[0]->mapChecklist)
                <x-nav-link href="{{ url('/proj_checklists/map') }}?pid={{ $pid }}">
                    <x-button>Map <i class="fa-solid fa-earth-americas"></i></x-button>
                </x-nav-link>
                @endif
            </div>
            @endif
            <ul class="list-disc pl-4">
                @foreach($proj_checklists as $checklist)
                <li class="text-base">
                    <x-link hx-boost="true" href="{{ url('checklists/' . $checklist->clid) }}" >
                        {{ $checklist->name }}
                    </x-link>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</x-layout>
