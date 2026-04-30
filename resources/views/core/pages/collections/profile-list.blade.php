@props(['collections' => \App\Models\Collection::query()->get()])
<x-layout class="flex flex-col gap-4">
    <x-breadcrumbs
        :items="[
        ['title' => __('header.H_HOME'), 'href' => url('')],
        ['title' => __('misc_collprofiles.COLLECTION_SEARCH'), 'href' => url('collections/search'), ],
        ['title' => __('misc_collmetadata.COL_PROFS')]
        ]"
    />
    <div class="text-4xl font-bold">{{ __('misc_collprofiles.COLLECTION_PROJECTS') }}</div>

    <x-link target="_blank" href="{{ legacy_url('/collections/datasets/rsshandler.php') }}">
        {{ __('misc_collprofiles.RSS_FEED') }}
    </x-link>

    @foreach($collections as $collection)
        <div class="border-base-300 border p-4">
            <div class="flex items-center gap-4">
                <img class="h-16" src="{{ $collection->icon }}" />
                <div class="text-lg font-bold">{{ $collection->collectionName }}</div>
            </div>

            <hr class="my-2" />

            <div>{!! Purify::clean($collection->fullDescription) !!}</div>

            <div>
                <x-link href="{{ $collection->homepage }}"> {{ __('misc_collmetadata.HOMEPAGE') }} </x-link>
            </div>

            <div>
                @php
            $contacts = json_decode($collection->contactJson, true);
            @endphp
                @if(is_array($contacts) && count($contacts))
                    @foreach($contacts as $contact)
                        @if(isset($contact['firstName']) && isset($contact['lastName']) && isset($contact['email']))
                            <div>
                                <span class="font-bold">
                                    {{ $contact['role'] ?? __('misc_collmetadata.CONTACT') }}:
                                </span>
                                {{ $contact['firstName'] . ' ' . $contact['lastName'] . ' ' . $contact['email'] }}
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <x-link hx-boost="true" href="{{ url('collections/' . $collection->collID) }}">
                {{ __('header.H_MORE_INFO') }}
            </x-link>
        </div>
    @endforeach
</x-layout>
