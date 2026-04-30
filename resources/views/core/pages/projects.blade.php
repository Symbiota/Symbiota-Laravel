@php
global $LANG;
include_once(legacy_path('/classes/ImInventories.php'));
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('projects/index');

$projManager = new \ImInventories('write');
$projectArr = $projManager->getProjectList();
@endphp

<x-margin-layout>
    <div>
        <h1 class="text-primary my-3 font-sans text-4xl font-bold">{{ $LANG['INVPROJ'] }}</h1>
    </div>
    @foreach($projectArr as $pid => $project)
        <div class="border-base-300 bg-base-200 rounded-md border p-4">
            <h3 class="text-2xl font-bold">
                <x-link href="{{ url('projects/' . $pid) }}"> {{ $project['projname'] }} </x-link>
            </h3>
            <x-text-label :label="$LANG['MANAG']">
                {{ $project["managers"]? $project["managers"]: $LANG['NOT_DEFINED'] }}
            </x-text-label>
            @if($project["descr"])
                <div>{{ $project["descr"] }}</div>
            @endif
        </div>
    @endforeach
</x-margin-layout>
