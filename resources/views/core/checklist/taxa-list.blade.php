@props([
    'checklist',
    'taxa' => [],
    'taxa_vouchers' => [],
    'children' => [],
    'show_common' => false,
    'show_notes_vouchers' => false,
    'show_taxa_authors' => false,
    'show_synonyms' => false,
    'show_taxa_alphabetically' => false,
    'sppEditToggle' => false,
])
@php global $LANG @endphp

@fragment('taxa-list')
<div id="taxa-list">
    @if(count($taxa) <= 0)
        <div>
            There are no taxa to list
        </div>
    @endif

    @php $previous = null @endphp
    @foreach($taxa as $tid => $taxon)
        @if($show_taxa_alphabetically)
            @if($loop->first || ($previous && $previous['taxongroup'] !== $taxon['taxongroup']))
                <div @class(['text-lg font-bold', 'mt-4' => !$loop->first])>{!! Purify::clean($taxon['taxongroup']) !!}</div>
            @endif
        @else
            @if($loop->first || ($previous && $previous['family'] !== $taxon['family']) )
                <div @class(['text-lg font-bold uppercase', 'mt-4' => !$loop->first])>{{ $taxon['family'] }}</div>
            @endif
        @endif

        <div class="pl-4 pb-1">
            <x-link class="text-base" href="{{ url('taxon/' . $tid) }}">
                @if(isset($taxon['sciname']))
                    {{ $taxon['sciname'] }}
                @else
                    {!! Purify::clean($taxon['taxongroup']) !!}
                @endif
                @if($show_taxa_authors && isset($taxon['author']))
                {{ $taxon['author'] }}
                @endif
            </x-link>

            @if($show_common && isset($taxon['vern']))
            <span> - {{ $taxon['vern']}}</span>
            @endif

            <span class="ml-1 inline-flex w-fit gap-2 items-center">
            <x-nav-link target="_blank" href="{{url('collections/list')}}?usethes=1&clid={{$checklist->clid}}&taxa={{$tid}}">
                <i class="fa-solid fa-list hover:text-base-content/50"></i>
            </x-nav-link>

            @if(isset($taxon['clid']) && $sppEditToggle)
                @foreach(explode(',',$taxon['clid']) as $id)
                @php
                $editTitle = array_key_exists($id, $children)? $children[$id]: $checklist->name;
                @endphp
                <a x-cloak x-show="{{ $sppEditToggle }}" target="_blank" href="{{ legacy_url('checklists/clsppeditor.php?tid=' . $tid . '&clid='. $id) }}" title="{{ $LANG['EDIT_DETAILS'] . ': ' . $editTitle }}">
                    <x-icons.edit />
                </a>
                @endforeach
            @endif
            </span>

            @if($show_synonyms && isset($taxon['syn']))
            <div class="pl-4">
                <span class="font-bold">Synonyms:</span>
                {!! Purify::clean($taxon['syn']) !!}
            </div>
            @endif

            @if($show_notes_vouchers && array_key_exists($tid, $taxa_vouchers))
            <div class="pl-4">
			    @if(isset($taxon['notes']))
                {!! Purify::clean($taxon['notes']) !!}
			    @endif
                @foreach ($taxa_vouchers[$tid] as $occid => $collName)
                <span>
                <x-link target="_blank" href="{{ url('occurrence/' . $occid) }}">
                    {{ $collName }}
                </x-link>
                @if (!$loop->last) , @endif
                @endforeach
                </span>
            </div>
            @endif
        </div>
        @php $previous=$taxon @endphp
    @endforeach
</div>
@endfragment
