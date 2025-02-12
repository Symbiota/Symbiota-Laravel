@props(['taxon', 'parents', 'common_names'])
@php
$breadcrumbs = [['title' => 'Home', 'href' => url('')]];
foreach($parents as $parent) {
if($taxon->tid === $parent->tid) {
array_push($breadcrumbs, ['title' => $taxon->sciName ]);
} else {
array_push($breadcrumbs, ['title' => $parent->sciName, 'href' => url('taxon/' . $parent->tid)]);
}
}
@endphp
<x-layout class="grid grid-col-1 gap-4">
    <!-- <x-breadcrumbs :items="$breadcrumbs" /> -->
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
        <x-tabs :tabs="['Taxonomy', 'Synonyms/Vernaculars', 'Traits']">
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
                <div>

                    @isset($common_names)
                    @foreach($common_names as $common_name) {{$common_name->VernacularName}} @endforeach
                    @endisset
                    <div class="font-bold">Synonyms:</div>
                    <div>[ Synonyms ]</div>

                </div>

                {{-- Trait Plots --}}
                <div>
                    Todo Traits
                </div>
        </x-tabs>
    </div>

    <div class="flex gap-4">
        <div>
            <img src="https://s3.msi.umn.edu/mbaenrms3fs/images/MIN_JFBM_PLANTS/01003/1003938_tn.jpg" alt="">
        </div>

        <div>
            <img src="https://s3.msi.umn.edu/mbaenrms3fs/images/MIN_JFBM_PLANTS/01003/1003938_tn.jpg" alt="">
        </div>

        <div>
            <img src="https://s3.msi.umn.edu/mbaenrms3fs/images/MIN_JFBM_PLANTS/01003/1003938_tn.jpg" alt="">
        </div>

        <div>
            <img src="https://s3.msi.umn.edu/mbaenrms3fs/images/MIN_JFBM_PLANTS/01003/1003938_tn.jpg" alt="">
        </div>
    </div>

    {{-- Todo Ignore the Hero Taxa Image --}}
    @if($taxon->tid)
    <div class="flex flex-wrap flex-row gap-3">
        <x-media.item :allow_empty_trigger="true" :fixed_start="0" />
        <div id="scroll-loader" class="htmx-indicator">
            <div class="stroke-accent w-full h-16 flex justify-center">
                <x-icons.loading />
            </div>
        </div>
    </div>
    @endif
</x-layout>
