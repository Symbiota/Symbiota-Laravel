@props(['taxon'])
<x-layout class="grid grid-col-1 gap-4">
    <div class="flex items-center">
        <h1 class="text-2xl font-bold w-fit">Taxon Name (Author)</h1>
        <div class="flex-grow">
        <a href="{{url(config('portal.name'). '/taxa/profile/tpeditor.php?tid=' . request('tid') )}}">
            <i class="float-right fas fa-edit cursor-pointer"></i>
        </a>
        </div>
    </div>
    <div class="flex gap-4 min-h-0">
        <div>
            <img src="https://s3.msi.umn.edu/mbaenrms3fs/images/MIN_JFBM_PLANTS/01003/1003938_tn.jpg" alt="">
            <div><b class="font-bold">Family:</b> [ Family ]</div>
            <div>[ Common Name ]</div>
            <div class="font-bold">Synonyms:</div>
            <div>[ Synonyms ]</div>
        </div>
        <div class="flex-grow">
            <x-tabs :tabs="['Resources']">
                <div class="min-h-72">
                    <div class="text-lg font-bold">Internal Resources</div>
                    <li>
                        <x-link class="text-base"
                            href="{{url(config('portal.name') . '/collections/list.php?usethes=1&taxa=58358')}}">
                            [Count] occurrences
                        </x-link>
                    </li>
                    <li>
                        <x-link class="text-base"
                            href="{{url(config('portal.name') . '/taxa/taxonomy/taxonomydynamicdisplay.php?target=58358')}}">
                            Taxonomic Tree
                        </x-link>
                    </li>
                </div>
            </x-tabs>
        </div>
    </div>
    {{-- Todo Ignore the Hero Taxa Image --}}
    @if(request('tid'))
    <div class="flex flex-wrap flex-row gap-3">
        <x-media.item :allow_empty_trigger="true" :fixed_start="0"/>
        <div id="scroll-loader" class="htmx-indicator">
            <div class="stroke-accent w-full h-16 flex justify-center">
                <x-icons.loading/>
            </div>
        </div>
    </div>
    @endif
</x-layout>
