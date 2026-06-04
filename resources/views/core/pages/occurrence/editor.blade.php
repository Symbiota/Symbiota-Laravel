@props(['occurrence'])
<x-layout :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="mb-4 flex items-center gap-2">
        @if($collection->icon)
            <img class="w-10" src="{{ $collection->icon }}" />
        @endif
        <div class="text-2xl font-bold">{{ $collection->collectionName }}</div>

        <div class="text-2xl font-bold">
            <x-nav-link href="{{ url('occurrence/' . $occurrence->occid) }}">
                <x-tooltip :text="__('Public View')">
                    <i class="fas fa-globe"></i>
                </x-tooltip>
            </x-nav-link>
        </div>
    </div>
    <div class="mb-4 flex items-center gap-2">
        <x-breadcrumbs
            :items="[
        ['title' => __('header.H_HOME'), 'href' => route('home') ],
        ['title' => __('editor_skeletalsubmit.COL_MNGMT'), 'href' => url('collections/' . $occurrence->collid)],
        ['title' => __('editor_occurrenceeditor.OCCEDITOR')]
        ]"
        />

        <span class="border-base-300 ml-auto flex gap-3 rounded-md border px-2 py-1">
            <x-link href="#"><</x-link>
            <x-link href="#"><<</x-link>
            <span class="font-bold"> |1 of ... | </span>
            <x-link href="#">>></x-link>
            <x-link href="#">></x-link>
        </span>
        <x-button href="{{ url('collections/table?collid=' . $occurrence->collid) }}" class="h-8">
            <x-icons.search />
        </x-button>
        <x-button> {{ __('editor_occurrenceeditor.NEW_REC') }} </x-button>
    </div>

    <x-tabs
        :tabs="[__('editor_occurrenceeditor.OCC_DATA'), __('individual.DET_HISTORY'), __('header.H_MEDIA'), __('includes_materialsampleinclude.MAT_SAMP'), __('individual.LINKED_RESOURCES'), __('individual.TRAITS'), __('Admin')]"
        :active="0"
    >
        {{-- Occurrence Data --}}
        <form class="flex flex-col gap-4">
            <x-occurrence.editor.collector-info :occurrence="$occurrence" :identifiers="$identifiers" />

            <x-occurrence.editor.latest-identification :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.locality :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.misc :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-occurrence.editor.curation :occurrence="$occurrence" {{-- TODO (Logan) Data piping --}} />

            <x-fieldset :legend="__('editor_occurrenceeditor.RECORD_CLONING')">
                <x-radio
                    :default_value="1"
                    :options="[
                        ['label' => 'Collection Event Fields', 'value' => 1],
                        ['label' => 'All Fields', 'value' => 0]
                    ]"
                    :label="__('editor_occurrenceeditor.CARRY_OVER')"
                    name="cloning-type"
                />
                <x-checkbox label="Carry over media" />
                {{-- TODO (Logan) Load Options for Cloning --}}
                <x-select
                    :items="[
                    [
                        'title' => 'Undefined',
                        'value' => null,
                        'disabled' => false
                    ],
                ]"
                />
                <x-input value="1" label="Number of Records" />
                {{-- TODO (Logan) Prepopulate Catalog numbers work --}}
                <x-button> {{ __('editor_occurrenceeditor.CREATE_RECORD') }} </x-button>
            </x-fieldset>

            {{-- Options should be the same as proccessing Status--}}
            <x-select
                label="Status Auto-Set"
                :items="[
                [
                    'title' => 'No Set Status',
                    'value' => 'No Set Status',
                    'disabled' => false
                ],
            ]"
            />

            <x-button> {{ __('exsiccati.SAVE_EDITS') }} </x-button>
        </form>

        <x-occurrence.editor.determination-history {{-- TODO (Logan) Prepopulate Catalog numbers work --}} />

        <x-occurrence.editor.media {{-- TODO (Logan) Prepopulate Catalog numbers work --}} />

        <div>Material Sample WIP</div>

        <x-occurrence.editor.linked-resources />

        <x-occurrence.editor.traits />

        <x-occurrence.editor.admin :occurrence="$occurrence" />
    </x-tabs>
</x-layout>
