@props([
    'datasets' => [],
])

@php
    $breadcrumbs = [
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('datasets_publiclist.PUB_DAT_LIST')],
    ];

    $hasCategories = false;
    $groupedDatasets = [];

    foreach ($datasets as $dataset) {
        if (array_key_exists('category', $dataset)) {
            $hasCategories = true;
            $groupedDatasets[$dataset['category'] ?: ''][] = $dataset;
        }
    }
@endphp

<x-margin-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>

    <div class="space-y-4">
        <x-page-title class="text-4xl">{{ __('datasets_publiclist.PUB_DAT_LIST') }}</x-page-title>

        @if($datasets)
            <ul class="list-disc pl-6">
                @if($hasCategories)
                    @foreach($groupedDatasets as $category => $categoryDatasets)
                        @if($category)
                            <h3 class="text-xl font-bold">{{ $category }}</h3>
                        @endif
                        @foreach($categoryDatasets as $dataset)
                            <li>
                                <x-link href="{{ route('datasets.profile', ['dataset_id' => $dataset['datasetid']]) }}">
                                    {{ $dataset['name'] }}
                                </x-link>
                            </li>
                        @endforeach
                    @endforeach
                @else
                    @foreach($datasets as $dataset)
                        <li>
                            <x-link href="{{ route('datasets.profile', ['dataset_id' => $dataset['datasetid']]) }}">
                                {{ $dataset['name'] }}
                            </x-link>
                        </li>
                    @endforeach
                @endif
            </ul>
        @endif
    </div>
</x-margin-layout>
