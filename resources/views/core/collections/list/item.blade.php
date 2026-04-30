@props(['occurrence' => null])
<div class="border-base-300 relative flex items-center gap-4 rounded-md border p-4">
    <div class="grid grid-cols-1 gap-2">
        <img
            class="mx-auto w-16"
            src="{{ $occurrence->icon ?? 'https://cch2.org/portal/content/collicon/blmar.jpg' }}"
        />
        <div class="mx-auto text-sm">
            {{ $occurrence->institutionCode . ($occurrence->collectionCode? ':' . $occurrence->collectionCode: '') }}
        </div>
    </div>
    <div class="grid grid-cols-1">
        @if($occurrence->sciname && $occurrence->scientificNameAuthorship)
            <div>
                <x-link href="{{ url('taxon/' . $occurrence->tidInterpreted) }}"
                    ><i>{{ $occurrence->sciname }}</i> {{ $occurrence->scientificNameAuthorship }}</x-link
                >
            </div>
        @endif
        <div>
            {{ $occurrence->catalogNumber . ' ' . $occurrence->recordedBy . ' ' . $occurrence->recordNumber . ' ' . ' ' . $occurrence->eventDate }}
        </div>
        <div>
            {{ implode(' | ', array_filter([substr($occurrence->locality, 0, 30), $occurrence->decimalLatitude, $occurrence->minimumElevationInMeters], fn ($v) => $v != null)) }}
        </div>
        <x-link href="{{ url('occurrence/' . $occurrence->occid ) }}" target="_blank">Full Record Details</x-link>
    </div>
    {{-- Icon Container --}}
    <div class="absolute top-0 right-0 flex items-center gap-2 p-4">
        @if($occurrence->image_cnt)
            <i class="fas fa-camera text-xl"></i>
        @endif

        @if($occurrence->audio_cnt)
            <i class="fas fa-file-audio text-xl"></i>
        @endif

        @if(Auth::check())
            <a href="{{ legacy_url('/collections/editor/occurrenceeditor.php?occid=' . $occurrence->occid) }}">
                <i class="fas fa-edit hover:text-base-content/50 cursor-pointer text-xl"></i>
            </a>
        @endif
    </div>
</div>
