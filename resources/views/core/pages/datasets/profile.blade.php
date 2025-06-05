@props(['dataset', 'database_cnt' => $dataset->getRecordCount()])
<x-layout class="lg:w-3/4 md:w-full mx-auto flex flex-col gap-4">

    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('') ],
        ['title' => 'Dataset: ' . $dataset->name],
    ]" />

    <h1 class="text-4xl font-bold">Datasets: {{ $dataset->name }}</h1>

    <p> This dataset includes {{ $database_cnt }} records. <p>

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

</x-layout>
