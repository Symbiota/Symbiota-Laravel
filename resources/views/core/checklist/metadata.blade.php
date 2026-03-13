@props(['checklist', 'parent' => [], 'children' => [], 'exclusions' => []])
@php global $LANG @endphp

@if($checklist->type === 'rarespp')
{{-- TODO --}}
@elseif($checklist->type === 'excludespp' && $parent && count($parent) > 0)
<x-text-label :label="$LANG['EXCLUSION_LIST']">
    <x-link href="{{ url('checklists/' . key($parent)) }}">
        {{ current($parent) }}
    </x-link>
</x-text-label>
@endif

<x-text-label :label="$LANG['INCLUDE_TAXA']">
@foreach($children as $clid => $name)
    <div class="pl-4">
        <li><x-link href="{{ url('checklists/'. $clid)}}">
            {{ $name }}
        </x-link></li>
    </div>
@endforeach
</x-text-label>

<x-text-label :label="$LANG['TAXA_EXCLUDED']">
@foreach($exclusions as $clid => $name)
    <div class="pl-4">
        <li><x-link href="{{ url('checklists/'. $clid)}}">
            {{ $name }}
        </x-link></li>
    </div>
@endforeach
</x-text-label>

@foreach ([
    $LANG['AUTHORS'] => $checklist->authors ?? false,
    $LANG['CITATION'] => $checklist->publication ?? false,
] as $label => $value)
    <x-text-label :label="$label">{{ $value }}</x-text-label>
@endforeach
@if($checklist->type !== 'excludespp')
    <x-text-label :label="$LANG['LOCALITY']">
        @isset($checklist->locality)
        {{ $checklist->locality }}
        @endisset
        @if($checklist->latCentroid && $checklist->longCentroid)
        ({{ $checklist->latCentroid }} {{ $checklist->longCentroid }})
        @endif
    </x-text-label>
@endif
<x-text-label class="markdown" :label="$checklist->type == 'excludespp'? $LANG['COMMENTS']: $LANG['ABSTRACT']">
    {!! Purify::clean($checklist->abstract) !!}
</x-text-label>
<x-text-label :label="$LANG['NOTES']">{{ $checklist->notes }}</x-text-label>
