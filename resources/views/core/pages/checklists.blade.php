@props(['checklists' => []])
<x-layout class="grid grid-cols-1 gap-4">
    <h1 class="text-4xl font-bold text-primary">Species Inventories</h1>

    @php
    $prev_pid = -1;
    @endphp

    <div>
        @foreach($checklists as $checklist)
        <div>
            @if ($prev_pid !== $checklist->pid)
            @php $prev_pid = $checklist->pid; @endphp
            <br>
            <div class="flex items-center gap-4">
                @if($checklist->projname)
                <x-link
                    href="{{url(config('portal.name') . '/projects/index.php?pid='.$checklist->pid)}}"
                    class="text-2xl font-bold text-primary">
                    {{$checklist->projname ?? 'Misc'}}
                </x-link>
                @else
                <div class="text-2xl font-bold text-primary">
                    Misc Inventories
                </div>
                @endif

                {{-- Todo needs to check actually point data --}}
                @if ($checklist->mapChecklist)
                    <x-button>Map <i class="fa-solid fa-earth-americas"></i></x-button>
                @endif
            </div>
            @endif
            <li>
                <x-link href="{{ url(config('portal.name') . '/checklists/checklist.php?clid=' . $checklist->clid . ($checklist->pid? '&pid=' . $checklist->pid: ''))}}">
                    {{ $checklist->name }}
                </x-link>
            </li>
        </div>
        @endforeach
    </div>
</x-layout>
