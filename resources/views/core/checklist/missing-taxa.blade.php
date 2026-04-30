@props(['displayMode', 'clVoucherReport'])
<div class="font-bold text-2xl">
  <span>
  {{ $displayMode == 2? __('checklists.vamissingtaxa.PROBLEMS'): __('checklists.vamissingtaxa.POSS_MISSING') }}:
  </span>
  <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
  {{ $clVoucherReport->getMissingTaxaCount() }}
</div>
<hr/>

<x-select label="Display Mode"/>
<p>
Listed below are taxon names not found in the checklist but are represented by one or more specimens that have a locality matching the above search term.
</p>

<div>
    @foreach ([
    'Somelong taxanomic (syn: Synonym)',
    'Somelong taxanomic var. someother taxonomic (syn: Synonym)'
    ] as $item)
        <div class="flex items-center gap-2">
            <x-link href="#">
                {{$item}}
            </x-link>
            <i class="fa-solid fa-link"></i>
        </div>
    @endforeach
</div>
