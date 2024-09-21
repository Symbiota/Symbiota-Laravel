@props(['' => $checklist])
<x-layout class="grid grid-cols-1 gap-4">
    <div class="flex items-center">
        <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
        <div class="flex flex-grow justify-end gap-4">
            <a href="{{url(config('portal.name') . '/checklists/checklistadmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> A
            </a>
            <a href="{{url(config('portal.name') . '/checklists/voucheradmin.php?clid=' . $checklist->clid)}}">
                <i class="flex-end fas fa-edit"></i> V
            </a>
            {{-- TODO (Logan) Toggle Spp controls --}}
            <a href="">
                <i class="flex-end fas fa-edit"></i> Spp
            </a>
        </div>
    </div>
    {{-- TODO (Logan) figure out alternatives to this --}}
    <x-accordion label='More Details' variant="clear-primary">
        <div>
            Locality:
        </div>
        <div>
            Abstract:
            <p>
                [Description]
            </p>
        </div>
    </x-accordion>
    <div class="grid grid-cols-2">
        <div>
            <div>Families: [Count]</div>
            <div>Genera: [Count]</div>
            <div>Species: [Count]</div>
            <div>Total Taxa: [Count]</div>

            <div>
                <div class="text-lg font-bold">[Family 1]</div>
                <div class="pl-4">
                    <div>
                        <x-link href="#">[Taxa 1]</x-link>
                        <i class="ml-4 fa-solid fa-list"></i>
                    </div>
                    <div>
                        <x-link href="#">[Taxa 2]</x-link>
                        <i class="ml-4 fa-solid fa-list"></i>
                    </div>
                </div>
            </div>
        </div>
        <fieldset class="flex flex-col gap-2">
            <legend class="text-lg font-bold">Options</legend>
            <x-link href="">Open Symbiota Key</x-link>
            <x-link href="">Games</x-link>
            {{-- TODO (Logan) scope to clid --}}
            <x-taxa-search />
            <x-select>
                <option>Original Checklist</option>
                <option>Central Thesaurus</option>
            </x-select>
            <div class="text-lg font-bold">Taxonmic Filter</div>
            <x-checkbox label="Display Synonyms" />
            <x-checkbox label="Common Names" />
            <x-checkbox label="Notes & Vouchers" />
            <x-checkbox label="Taxon Authors" />
            <x-checkbox label="Show Taxa Alphabetically" />
            <div class="flex items-center">
                <x-button>Build List</x-button>
                <div class="flex flex-grow justify-end gap-4 text-xl">
                    <i class="fa-solid fa-download"></i>
                    <i class="fa-solid fa-print"></i>
                    <i class="fa-regular fa-file-word"></i>
                </div>
            </div>
        </fieldset>
    </div>
</x-layout>
