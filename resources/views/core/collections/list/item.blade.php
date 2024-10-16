@props(['occurrence' => null])
<div class="flex items-center gap-4 p-4 rounded-md border border-base-300 relative">
    <div class="grid grid-cols-1 gap-2">
        <img class="w-16 mx-auto" src="https://cch2.org/portal/content/collicon/blmar.jpg">
        <div class="mx-auto">
            BLMAR
        </div>
    </div>
    <div class="grid grid-cols-1">
        @if($occurrence->sciname && $occurrence->scientificNameAuthorship)
        <div>{{ $occurrence->sciname . '(' . $occurrence->scientificNameAuthorship . ')'}}</div>
        @endif
        <div>{{ $occurrence->catalogNumber . $occurrence->recordedBy .  $occurrence->recordedBy . $occurrence->recordNumber . $occurrence->eventDate}}</div>
        <div>{{ implode(' | ', array_filter([$occurrence->locality, $occurrence->decimalLatitude, $occurrence->minimumElevationInMeters], fn ($v) => $v != null)) }}</div>
        <x-link href="{{url( config('portal.name') . '/collections/individual/index.php?occid=' . $occurrence->occid) }}" target="_blank">Full Record Details</x-link>
    </div>
    {{-- Icon Container --}}
    <div class="absolute right-0 top-0 p-4 flex items-center gap-2">
        @if($occurrence->image_cnt)
        <i class="text-xl fas fa-camera"></i>
        @endif

        @if($occurrence->audio_cnt)
        <i class="text-xl fas fa-file-audio"></i>
        @endif

        @if (Auth::check())
        <a href="{{url( config('portal.name') . '/collections/editor/occurrenceeditor.php?occid=' . $occurrence->occid)}}">
            <i class="text-xl fas fa-edit hover:text-base-content/50 cursor-pointer"></i>
        </a>
        @endif
    </div>
</div>
