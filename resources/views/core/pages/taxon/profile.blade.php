@props(['taxon', 'parents', 'common_names', 'children' => [], 'taxa_descriptions', 'external_links'])
<x-layout class="grid grid-col-1 gap-4">
    <div class="flex items-center">
        <h1 class="text-2xl font-bold w-fit">
            <i>{{ $taxon->sciName }}</i>
            @if ($taxon->author)
            {{ $taxon->author}}
            @endif
        </h1>
        <div class="flex justify-end flex-grow gap-2">
            <x-nav-link hx-boost="true" href="{{ url('collections/list') . '?taxa=' . $taxon->tid }}" hx-target="body">
                <x-button class="text-sm rounded-full">
                    {{ $occurrence_count }} Records
                </x-button>
            </x-nav-link>
            <a href="{{url(config('portal.name'). '/taxa/profile/tpeditor.php?tid=' . $taxon->tid )}}">
                <i class="text-xl float-right fas fa-edit cursor-pointer"></i>
            </a>
        </div>
    </div>
    <div class="flex-grow">
        <x-tabs :tabs="['Taxonomy', 'Synonyms/Vernaculars', 'Traits', 'About', 'Resources']" >
            {{-- Taxonomy Information --}}
            <div class="min-h-72">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl">Taxonomy</h2>
                    <x-link class="text-base"
                        href="{{url(config('portal.name') . '/taxa/taxonomy/taxonomydynamicdisplay.php?target=58358')}}">
                        See full taxonomic tree
                    </x-link>
                </div>
                @foreach($parents as $parent)
                @if($loop->first)
                <div>
                    @else
                    <div class="pl-1.5">
                        @endif
                        ->
                        <x-link class="text-base-content" href="{{ url('taxon/' . $parent->tid) }}">{{ $parent->sciName
                            }} ({{ $parent->rankname }})</x-link>
                        @endforeach
                        @foreach($parents as $parent)
                    </div>
                    @endforeach
                </div>

                {{-- Synonyms and Comon Names --}}
                <div class="min-h-72">

                    @isset($common_names)
                    @foreach($common_names as $common_name) {{$common_name->VernacularName}} @endforeach
                    @endisset
                    <div class="font-bold">Synonyms:</div>
                    <div>[ Synonyms ]</div>

                </div>

                {{-- Trait Plots --}}
                <div class="min-h-72">
                    Todo Traits
                </div>

                {{-- About --}}
                <div class="flex flex-col gap-4 min-h-72">
                    @foreach ($taxa_descriptions as $description)
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 item-center">
                            <span class="text-xl font-bold">{{ $description['source'] }}</span>
                            <x-link href="{{$description['sourceUrl']}}" target="_blank">See more</x-link>
                        </div>
                        @foreach ($description['statements'] as $heading => $statement)
                        <div>
                            <span class="font-bold">{{ $heading }}</span>: {{$statement}}
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>

                {{-- Resources --}}
                <div class="min-h-72">
                    @if(count($external_links))
                    <div class="text-xl font-bold">External Resources</div>
                    @foreach ($external_links as $link)
                        <li>
                            <x-link href="{{ $link->url }}" target="_blank">
                                {{ $link->sourcename }}
                            </x-link>
                        </li>
                    @endforeach @endif
                </div>
        </x-tabs>
    </div>


    @if(count($children))
    <div class="flex flex-wrap flex-row gap-3">
    @foreach ($children as $child)
        <x-image-card :src="$child->thumbnailUrl" :title="$child->sciName" />
    @endforeach
    </div>
    @else
    <div class="flex flex-wrap flex-row gap-3">
        <x-media.item :allow_empty_trigger="true" :fixed_start="0" :params="['tid' => $taxon->tid, 'taxon_sort_order' => true]"/>
        <div id="scroll-loader" class="htmx-indicator">
            <div class="stroke-accent w-full h-16 flex justify-center">
                <x-icons.loading />
            </div>
        </div>
    </div>
    @endif
</x-layout>
