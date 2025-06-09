@props(['dataset'])
<x-layout class="lg:w-3/4 md:w-full mx-auto flex flex-col gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Dataset' . ($dataset && isset($dataset->name)? ': ' . $dataset->name: '')],
    ]" />

    @if(!empty($dataset))

    <h1 class="text-4xl font-bold">Datasets: {{ $dataset->name }}</h1>

    <p> This dataset includes {{ $dataset->getRecordCount() }} records. <p>

    <div class="flex flex-col gap-4">
        <x-link href="{{ url('collections/list') }}?datasetID={{$dataset->datasetID}}">
            View and download samples in this Dataset (List view)
        </x-link>

        <x-link href="{{ url('collections/table') }}?datasetID={{$dataset->datasetID}}">
            View samples in this Dataset (Table view)
        </x-link>

        <x-link href="{{ url('collections/list') }}?datasetID={{$dataset->datasetID}}&active_tab=0">
            View list of taxa in this Dataset
        </x-link>
    </div>
    @else
        <div class="flex justify-center my-auto">
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-bold">No Dataset Accessible</h1>
                <p></p>

                <x-link href="{{ url('datasets') }}">See publicly accessible datasets</x-link>

                @if (Auth::check())
                <x-link href="{{ url('user/profile') }}?active_tab=datasets">See datasets with permissions</x-link>
                @endif
            </div>
        </div>
    @endif

</x-layout>
