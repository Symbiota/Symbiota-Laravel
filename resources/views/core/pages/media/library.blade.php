<x-layout class="sm:w-[90%] lg:w-[70%] m-auto flex flex-col gap-4">
    <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Media Library' ]
        ]"
    />

    <h1 class="text-4xl font-bold">Taxa with Media</h1>

    <p>
        This page provides a complete list of taxa that have media. Use the controls below to browse and search for media by family, genus, or species.
    </p>

    <div class="flex items-center gap-2">
        <x-button hx-boost="true" href="{{ url('usagepolicy/#media') }}">Media Copyright Policy</x-button>
        <x-button hx-boost="true" href="{{ url('media/contributors') }}">Media Contributors</x-button>
        <x-button hx-boost="true" href="{{ url('media/search')}}">Media Search</x-button>
    </div>

    <form hx-get="{{ url('media/library') }}" hx-target="#taxa_list" class="flex flex-col gap-4 border-y py-4">
        <input type="hidden" name="fragment" value="taxa_list">
        <x-taxa-search :hide_selector="true" :hide_synonyms_checkbox="true" />
        <x-button type="submit">Search</x-button>
    </form>

    <div class="flex items-center gap-2">
        <x-button href="{{ url('media/library') }}?target=genus">Browse By Genus</x-button>
        <x-button href="{{ url('media/library') }}?target=family">Browse By Family</x-button>
    </div>

    @fragment('taxa_list')
        <div id="taxa_list">
            @if(!empty(request('taxa')))
            <h2 class="text-2xl font-bold" >
                Select a species to access available media
            </h2>
            @elseif(request('target') === 'genus')
            <h2 class="text-2xl font-bold" >
                Select a genus to see species list
            </h2>
            @else
            <h2 class="text-2xl font-bold" >
                Select a family to see species list
            </h2>
            @endif
            @foreach($taxa as $taxon)
                <div>
                    @if(!request('target') && !request('taxa'))
                        <x-link href="{{ url()->current() }}?taxa={{ $taxon->name }}&target=genus">{{ strtoupper($taxon->name) }}</x-link>
                    @elseif(isset($taxon->tid))
                        <x-link href="{{ url('taxon/' . $taxon->tid) }}">{{ $taxon->name }}</x-link>
                        <x-nav-link href="{{ url('media/search') }}?taxa={{ $taxon->name }}">
                            <i class="text-xl fas fa-camera"></i>
                        </x-nav-link>
                    @else
                        <x-link href="{{ url()->current() }}?taxa={{ $taxon->name }}">{{ $taxon->name }}</x-link>
                    @endif
                </div>
            @endforeach
        </div>
    @endfragment
</x-layout>
