@props(['taxon', 'parents', 'common_names' => [], 'synonyms' => [], 'children' => [], 'taxa_descriptions',
'external_links', 'media'])
<x-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('') ],
            ['title' => 'Taxon Profile Public Display', 'href' => url('/taxon/' . $taxon->tid ) ],
            'Taxon Profile Editor'
        ]" />
    </div>

    <h1 class="text-4xl font-bold"><i>{{ $taxon->sciName }}</i> {{ $taxon->author }}</h1>
    <div class="mb-4 flex">
        @if($taxon->family)
        <h2 class="text-2xl font-bold">Family: {{ $taxon->family }}</h2>
        @endif
    </div>

    <x-tabs :tabs="['Synonyms/Vernaculars', 'Media', 'Descriptions']">
        {{-- Synonyms/Vernaculars --}}
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <div class="font-bold text-xl">Common Names</div>
                <x-button>Add</x-button>
            </div>
            @foreach ($common_names as $name)
            <div class="p-2 border border-base-300 relative">
                <div class="absolute right-2 top-2 flex gap-2">
                    <x-icons.edit />
                    <x-icons.delete />
                </div>

                {{ $name->VernacularName }}
                <x-select label="Language" name="language"
                    :items="[['title' => 'English', 'value' => 1, 'disabled' =>false]]" :default="0" />
                <x-input label="Notes" name="notes" value="{{ $name->notes }}" />
                <x-input label="Source" name="source" value="{{ $name->Source }}" />
                <x-input label="Sort Sequence" name="sortSequence" value="{{ $name->SortSequence }}" />
            </div>
            @endforeach

            <div class="flex items-center gap-2 mb-2">
                <div class="font-bold text-xl">Synonyms</div>
            </div>
            @if(count($synonyms))
            @foreach ($synonyms as $name)
            <div class="p-2">{{ $name }}</div>
            @endforeach
            @else
            <div>
                No synonym links. Most of the synonym management must be done in the Taxonmic Thesaurus editing module
                (see <x-link href="{{ url('/sitemap') }}">Sitemap</x-link> )
            </div>
            @endif
        </div>

        {{-- Media --}}
        <div>
            TODO (Logan) Combine Media edit | sort | add
            <x-button>Add</x-button>
            <div class="flex flex-wrap">
                @foreach($media as $m)
                <x-image-card src="{{$m->thumbnailUrl ?? $m->url}}" title="Image" />
                @endforeach
            </div>
        </div>

        {{-- Descriptions --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <x-modal>
                    <x-slot:label>
                        Add Description Block
                    </x-slot>
                    <x-slot:title class="text-2xl">
                        Add Description
                    </x-slot>
                    <x-slot name="body">
                        <form class="flex flex-col gap-2">
                            <x-input label="Language" name="language" />
                            <x-input label="Source" name="source" />
                            <x-input label="Source Url" name="sourceUrl" />
                            <x-input label="Notes" name="notes" />
                            <x-input label="Sort Sequence" name="sortSequence" />
                            <x-button>Submit</x-button>
                        </form>
                    </x-slot>
                </x-modal>

                {{-- TODO (Logan) experiment with support for certain websites to pull data kinda of like a data hook plugin --}}
                {{-- Would also be interesting to explore this for taxa's external links --}}
                <x-button>Add North American Flora</x-button>
                <x-button>Add Wikipedia</x-button>
            </div>

            <div class="flex flex-col gap-4">
                @foreach($taxa_descriptions as $description)
                <div class="flex flex-col gap-2 border border-base-300 p-2">
                    <div class="text-xl font-bold">
                        {{ $description['source'] }}
                    </div>
                    @foreach($description['statements'] as $heading => $statement)
                    <div>
                        <span>
                            <x-modal>
                                <x-slot:label class="border-0 p-0 text-2xl text-base h-fit">
                                    <x-icons.edit />
                                </x-slot>
                                <x-slot:title class="text-2xl">
                                    Edit Statement
                                </x-slot>
                                <x-slot name="body">
                                    <form class="flex flex-col gap-2">
                                        <x-input label="Heading" name="heading" value="{{ $heading }}" />
                                        <x-input label="Statement" name="statement" :area="true" rows="4">
                                            {{ $statement }}
                                        </x-input>
                                    </form>
                                </x-slot>
                            </x-modal>
                        </span>
                        <span class="font-bold">{{$heading}}:</span>
                        <span>{{$statement}}</span>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </x-tabs>
</x-layout>
