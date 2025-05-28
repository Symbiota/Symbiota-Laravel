@props(['collections'])
<x-layout class="flex flex-col gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('')],
        ['title' => 'Collection Search', 'href' => url('collections/search'), ],
        ['title' => 'Collection Profiles']
        ]" />
    <div class="text-4xl font-bold">
        Natural History Collections and Observation Projects
    </div>

    <x-link target="_blank" href="{{ url(config('portal.name') . '/collections/datasets/rsshandler.php') }}">RSS
        Feed</x-link>

    @foreach ($collections as $collection)
    <div class="border border-base-300 p-4">
        <div class="flex items-center gap-4">
            <img class="h-16" src="{{ $collection->icon }}" />
            <div class="text-lg font-bold">{{ $collection->collectionName }}</div>
        </div>

        <hr class="my-2"/>

        <div>{!! Purify::clean($collection->fullDescription) !!}</div>

        <div>
            <x-link href="{{ $collection->homepage}}">Homepage</x-link>
        </div>

        <div>
            @php
            $contacts = json_decode($collection->contactJson, true);
            @endphp
            @if(is_array($contacts) && count($contacts))
                @foreach($contacts as $contact)
                    @if(isset($contact['firstName']) && isset($contact['lastName']) && isset($contact['email']))
                    <div>
                        <span class="font-bold">{{ $contact['role'] ?? 'Contact' }}:</span>
                        {{ $contact['firstName'] . ' ' . $contact['lastName'] . ' ' . $contact['email'] }}
                    </div>
                    @endif
                @endforeach
            @endif
        </div>

        <x-link href="{{ url('collections/' . $collection->collID) }}">More Information</x-link>
    </div>
    @endforeach
</x-layout>
