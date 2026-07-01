@props(['checklists' => []])
@php
$has_checklist_coords = [];
$projects = [];

foreach($checklists as $checklist) {
    $key = empty($checklist->pid)? 'misc': $checklist->pid;

    if(array_key_exists($key, $projects)) {
        array_push($projects[$key], $checklist);
    } else {
        $projects[$key] = [ $checklist ];
    }

    if(!array_key_exists($key, $has_checklist_coords) && $checklist->latCentroid && $checklist->longCentroid) {
        $has_checklist_coords[$key] = true;
    }
}

@endphp
<x-margin-layout>
    <x-breadcrumbs
        :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        __('checklists.SPECIES_INVENTORIES')
    ]"
    />
    <x-page-title> {{ __('checklists.SPECIES_INVENTORIES') }} </x-page-title>

    <div class="flex flex-col gap-4">
        @foreach($projects as $pid => $proj_checklists)
            <div>
                @if(!empty($proj_checklists))
                    <div class="flex items-center gap-4">
                        @if($proj_checklists[0]->projname)
                            <x-link href="{{ url('/projects/' . $pid) }}" class="text-primary text-2xl font-bold">
                                {{ ($proj_checklists[0]->projname ?? 'Misc') . (!$proj_checklists[0]->ispublic? ' (Private)': '') }}
                            </x-link>
                        @else
                            <div class="text-primary text-2xl font-bold">{{ __('checklists.MISC_INVENTORIES') }}</div>
                        @endif

                        @if(array_key_exists($pid, $has_checklist_coords))
                            <x-nav-link href="{{ url('/checklists/map') }}?pid={{ $pid }}">
                                <x-button>{{ __('header.H_MAP') }} <i class="fa-solid fa-earth-americas"></i></x-button>
                            </x-nav-link>
                        @endif
                    </div>
                @endif
                <ul class="list-disc pl-4">
                    @foreach($proj_checklists as $checklist)
                        <li class="text-base">
                            <x-link hx-boost="true" href="{{ url('checklists/' . $checklist->clid) }}">
                                {{ $checklist->name . ($checklist->access=='private'? ' (Private)': '') }}
                            </x-link>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</x-margin-layout>
