@props(['nonVoucheredTaxa' => [], 'clVoucherReport'])

<div class="font-bold text-2xl">
  {{ __('checklists_voucheradmin.TAXWITHOUTVOUCH') }}: {{ $clVoucherReport->getNonVoucheredCnt() }} <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
</div>
<hr/>
<p>{{ __('checklists_voucheradmin.LISTEDBELOWARESPECINSTRUC') }}</p>
<x-select label="Display Mode"/>
@if($nonVoucheredTaxa)
<div>
@foreach($nonVoucheredTaxa as $family => $taxa)
<div>
    <div class="text-lg font-bold">{{ $family }}</div>
    @foreach($taxa as $tid => $taxon)
    <div class="pl-4">
        <x-link class="text-base" href="{{ url('taxon/' . $taxon['t']) }}">
            {{ $taxon['s'] }}
        </x-link>
        <a target="blank" href="{{ legacy_url('collections/list.php?usethes=1&reset=1&mode=voucher&taxa=' . $taxon['s'] . '&targetclid=' . $clid . '&targettid=' . $taxon['t']) }}">
            <i class="ml-4 fa-solid fa-list"></i>
        </a>
    </div>
    @endforeach
</div>
@endforeach
</div>
@else
<div class="font-bold text-xl">
    {{ __('checklists_voucheradmin.ALLTAXACONTAINVOUCH') }}
</div>
@endif
