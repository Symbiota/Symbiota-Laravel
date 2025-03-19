@props(['checklists' => []])
<x-layout class="grid grid-cols-1 gap-4 lg:w-3/4 md:w-full mx-auto">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        'Species Inventories'
    ]"/>
    <h1 class="text-4xl font-bold text-primary">Species Inventories</h1>

    @php
    $prev_pid = -1;
    @endphp

    <div>
        @foreach($checklists as $checklist)
        <div>
            @if ($prev_pid !== $checklist->pid)
            @php $prev_pid = $checklist->pid; @endphp

            @if(!$loop->first) <br> @endif
            <div class="flex items-center gap-4">
                @if($checklist->projname)
                <x-link
                    href="{{url('/projects/' . $checklist->pid)}}"
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
                <x-nav-link href="{{ url(config('portal.name') . '/checklists/clgmap.php') .'?pid=' . $checklist->pid }}">
                    <x-button>Map <i class="fa-solid fa-earth-americas"></i></x-button>
                </x-nav-link>
                @endif
            </div>
            @endif
            <li class="text-base">
                <x-link hx-boost="true" href="{{ url('checklists/' . $checklist->clid) }}" >
                    {{ $checklist->name }}
                </x-link>
            </li>
        </div>
        @endforeach
    </div>
</x-layout>
