@props([
    'datasetArr' => [],
    'canCreate' => false,
])

@php
    $errors = $errors ?? message_bag([]);
    $ownedDatasets = $datasetArr['owner'] ?? [];
    $sharedDatasets = $datasetArr['other'] ?? [];
    $hasDatasets = ! empty($ownedDatasets) || ! empty($sharedDatasets);
    $showCreateForm = $canCreate && ($errors->any() || old('name') !== null);

    $breadcrumbs = [
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('datasets.MY_PROFILE'), 'href' => url('/user/profile')],
        ['title' => __('datasets.DATLIST')],
    ];

    $roleLabels = [
        'DatasetReader' => __('datasets.DATASET_READER'),
        'DatasetAdmin' => __('datasets.DATASET_ADMIN'),
        'DatasetEditor' => __('datasets.DATASET_EDITOR'),
    ];
@endphp

<x-margin-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>

    <div class="space-y-6" x-data="{ showCreateForm: @js($showCreateForm) }">
        @if(session('status'))
            <div class="m-4 {{ session('statusType') === 'success' ? 'text-success' : 'text-error' }}">
                {!! Purify::clean(session('status')) !!}
            </div>
        @endif

        <x-errors :errors="$errors" />

        <div>
            @if($canCreate)
                <div class="float-right m-2">
                    <x-button
                        type="button"
                        variant="clear-primary"
                        title="{{ __('datasets.CRT_NEW_DAT') }}"
                        @click="showCreateForm = !showCreateForm"
                    >
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        <span class="sr-only">{{ __('datasets.ADD_BUTTON') }}</span>
                    </x-button>
                </div>
            @endif

            <x-page-title class="text-4xl">{{ __('datasets.OCC_DAT_MNG') }}</x-page-title>
            <p>{{ __('datasets.TOOL_DESCR') }}</p>
        </div>

        @if($canCreate)
            <div id="adddiv" x-show="showCreateForm" x-cloak>
                <fieldset class="rounded border p-4">
                    <legend class="px-1 font-bold">{{ __('datasets.CRT_NEW_DAT') }}</legend>

                    <form method="POST" action="{{ route('datasets.store') }}" class="space-y-4">
                        @csrf

                        <x-input
                            id="name"
                            name="name"
                            :label="__('datasets.NAME')"
                            type="text"
                            :value="old('name')"
                            required
                            class="w-[90%]"
                        />

                        <x-checkbox
                            id="ispublic"
                            name="ispublic"
                            :label="__('datasets.PUB_VIS')"
                            value="1"
                            :checked="(bool) old('ispublic')"
                        />

                        <x-input
                            id="notes"
                            name="notes"
                            :label="__('datasets.NOTES')"
                            type="text"
                            :value="old('notes')"
                            class="w-[90%]"
                        />

                        <x-rich-editor
                            id="description"
                            name="description"
                            :label="__('datasets.DESCR')"
                            class="min-h-40"
                            >{{ old('description') }}</x-rich-editor
                        >

                        <div class="flex flex-wrap gap-2">
                            <x-button name="submitaction" type="submit" value="createNewDataset">
                                {{ __('datasets.CRT_NEW_DAT') }}
                            </x-button>
                            <x-button type="button" variant="error" @click="showCreateForm = false">
                                {{ __('datasets.CANCEL') }}
                            </x-button>
                        </div>
                    </form>
                </fieldset>
            </div>
        @endif

        @if($hasDatasets)
            <fieldset class="rounded border p-4">
                <legend class="px-1 font-bold">{{ __('datasets.OWNED') }}</legend>

                @forelse($ownedDatasets as $datasetId => $dataset)
                    <div class="mb-3">
                        <div>
                            <x-link
                                href="{{ route('datasets.edit', ['dataset_id' => $datasetId]) }}"
                                title="{{ __('datasets.MNG_EDIT') }}"
                            >
                                <span class="font-bold">{{ $dataset['name'] ?? '' }} (#{{ $datasetId }})</span>
                            </x-link>
                        </div>
                        <div class="ml-4">
                            @if(! empty($dataset['notes']))
                                <div>{{ $dataset['notes'] }}</div>
                            @endif
                            <div>{{ __('datasets.CREATED') }}: {{ $dataset['ts'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="font-bold">{{ __('datasets.NO_OWNED') }}</div>
                @endforelse
            </fieldset>
            <fieldset class="rounded border p-4">
                <legend class="px-1 font-bold">{{ __('datasets.SHARED') }}</legend>

                @forelse($sharedDatasets as $datasetId => $dataset)
                    @php
                        $role = $roleLabels[$dataset['role'] ?? 'DatasetReader'] ?? $roleLabels['DatasetReader'];
                    @endphp
                    <div class="mb-3">
                        <div>
                            <x-link
                                href="{{ route('datasets.edit', ['dataset_id' => $datasetId]) }}"
                                title="{{ __('datasets.ACCESS_DATASET') }}"
                            >
                                <span class="font-bold">{{ $dataset['name'] ?? '' }} (#{{ $datasetId }})</span>
                            </x-link>
                            - {{ $role }}
                        </div>
                        <div class="ml-4">
                            @if(! empty($dataset['notes']))
                                <div>{{ $dataset['notes'] }}</div>
                            @endif
                            <div>{{ __('datasets.CREATED') }}: {{ $dataset['ts'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="font-bold">{{ __('datasets.NO_SHARED') }}</div>
                @endforelse
            </fieldset>
        @else
            <div class="m-5">
                <div class="font-bold">{{ __('datasets.NO_LOGIN') }}</div>
                @if($canCreate)
                    <x-button type="button" variant="clear-primary" @click="showCreateForm = true">
                        {{ __('datasets.CRT_NEW_DAT') }}
                    </x-button>
                @endif
            </div>
        @endif
    </div>
</x-margin-layout>
