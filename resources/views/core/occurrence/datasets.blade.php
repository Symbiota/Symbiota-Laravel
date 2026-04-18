@props(['occurrence', 'linked_datasets', 'user_datasets'])
@php
$user_dataset_options = [];
foreach($user_datasets as $datasets) {
    if(count($linked_datasets) <= 0 || $linked_datasets->search(fn ($v) => $v->datasetID == $datasets->datasetID) === false) {
        $user_dataset_options[] = item($datasets->datasetID, $datasets->name);
    }
}
@endphp
<div id="linked_datasets" class="relative" x-data="{ dataset_link_open: false}">
    <div>
        <span class="font-bold text-xl">
            {{ __('individual_linkedresources.DATASETLINKAGES') }}
        </span>
        <hr/>
    </div>

    @if(!empty($user_dataset_options))

    <i @click="dataset_link_open = true" class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
    <form hx-put="{{url('occurrence/' . $occurrence->occid . '/link/dataset' )}}" hx-target="#linked_datasets" x-show="dataset_link_open" class="flex flex-col gap-4">
        @csrf
        <x-select label="Dataset" name="datasetID" :items="$user_dataset_options" class="w-60"/>
        <x-input :label="__('projects.NOTES')" name="notes" />
        <input type="hidden" name="voucher_tid" value="{{ $occurrence->tidInterpreted}}"/>
        <div class="flex gap-2">
            <x-button>
                {{ __('individual_linkedresources.LINKTO') }}
            </x-button>
            <x-button @click="dataset_link_open=false" type="button" variant="neutral">
                {{ __('Cancel') }}
            </x-button>
        </div>
    </form>
    @endif

    <div>
        @if(count($linked_datasets))
            <ul>
            @foreach ($linked_datasets as $dataset)
                <li>
                    <x-link href="{{ legacy_url('/collections/datasets/public.php')}}?datasetid={{$dataset->datasetID}}">
                        {{ $dataset->name }}
                    </x-link>
                </li>
            @endforeach
            </ul>
        @else
            <p>{{ __('individual_linkedresources.OCCURRENCENOTLINKED') }}</p>
        @endif
    </div>
</div>
