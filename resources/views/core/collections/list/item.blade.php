@props(['taxa' => ''])
<div class="flex items-center gap-4 p-4 rounded-md border border-base-300 relative">
    <div class="grid grid-cols-1 gap-2">
        <img class="w-16 mx-auto" src="https://cch2.org/portal/content/collicon/blmar.jpg">
        <div class="mx-auto">
            BLMAR
        </div>
    </div>
    <div class="grid grid-cols-1">
        <div>Taxa Name (Author)</div>
        <div>Catalog # | Creator | Collector Number | date</div>
        <div>Locality | Coordinates | Elevation </div>
        <x-link href="#">Full Record Details</x-link>
    </div>
    {{-- Icon Container --}}
    <div class="absolute right-0 top-0 p-4 flex items-center gap-2">
        <i class="text-xl fas fa-camera"></i>
        <i class="text-xl fas fa-file-audio"></i>
        <a href="#">
            <i class="text-xl fas fa-edit hover:text-base-content/50 cursor-pointer"></i>
        </a>
    </div>
</div>
