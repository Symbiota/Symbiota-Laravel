@props(['creators' => [], 'collections' => []])
<x-layout class="sm:w-[90%] lg:w-[70%] m-auto flex flex-col gap-4">
    <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Media Library', 'href' => url('media/library')],
            ['title' => 'Media Contributors' ]
        ]"
    />

    <h1 class="text-4xl font-bold">Creator List</h1>

    <hr>

    <h2 class="text-2xl font-bold">Media Contributors</h2>
    <div>
    @foreach ($creators as $creator)
        <div>
            <x-link href="{{ url('media/search') }}?creatorUid={{ $creator->creatorUid }}">
                {{ $creator->lastName }},  {{ $creator->firstName}}
            </x-link>
            ({{ number_format($creator->media_count) }})
        </div>
    @endforeach
    </div>

    <h2 class="text-2xl font-bold">Specimens</h2>
    <div>
    @foreach ($collections as $specimen)
        @if($specimen->collType === \App\Models\Collection::Specimens)
            @php
                $dyn_props = json_decode($specimen->dynamicProperties);
                $media_parts = explode(':', $dyn_props->imgcnt);
                $media_count = count($media_parts) > 0? $media_parts[0]: 0;
            @endphp

            @if(is_numeric($media_count) && intval($media_count) > 0)
            <div>
                <x-link href="{{ url('media/search') }}?collId={{ $specimen->collId }}">{{ $specimen->collectionName }}</x-link> ({{ number_format($media_count) }})
            </div>
            @endif
        @endif
    @endforeach
    </div>

    <h2 class="text-2xl font-bold">Observations</h2>
    <div>
    @foreach ($collections as $observation)
        @if($observation->collType === \App\Models\Collection::Observations || $observation->collType === \App\Models\Collection::GeneralObservations)
            @php
                $dyn_props = json_decode($observation->dynamicProperties);
                $media_parts = explode(':', $dyn_props->imgcnt);
                $media_count = count($media_parts) > 0? $media_parts[0]: 0;
            @endphp

            @if(is_numeric($media_count) && intval($media_count) > 0)
            <div>
                <x-link href="{{ url('media/search') }}?collId={{ $observation->collId }}">{{ $observation->collectionName }}</x-link> ({{ number_format($media_count) }})
            </div>
            @endif
        @endif
    @endforeach
    </div>
</x-layout>

