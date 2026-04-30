@props(['clid', 'clVoucherManager'] )
<div class="flex flex-col gap-2">
    <div class="font-bold text-2xl">
      {{ __('checklists_voucheradmin.REPORTS') }}
    </div>
    <hr/>
    <p>{{ __('checklists_voucheradmin.ADDITIONAL') }}</p>
</div>

<div class="flex flex-col gap-1">
    <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullcsv&clid=' . $clid) }}">
        {{ __('checklists_voucheradmin.FULLSPECLIST') }}
    </x-link>
    @if($vouchersExist = $clVoucherManager->vouchersExist())
    <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullvoucherscsv&clid=' . $clid) }}">
        {{ __('checklists_voucheradmin.FULLSPECLISTVOUCHER') }}
    </x-link>
    <x-link target="_blank"
        href="{{ legacy_url('collections/download/index.php?searchvar=' . urlencode('clid=' . $clVoucherManager->getClidFullStr()) . '&noheader=1') }}">
        {{ __('checklists_voucheradmin.VOUCHERONLY') }}
    </x-link>
    @endif
    <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullalloccurcsv&clid=' . $clid) }}">
        {{ __('checklists_voucheradmin.FULLSPECLISTALLOCCUR') }}
    </x-link>
    <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=pensoftxlsx&clid=' . $clid) }}">
        {{ __('checklists_voucheradmin.PENSOFT_XLSX_EXPORT') }}
    </x-link>
    <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=missingoccurcsv&clid=' . $clid) }}">
        {{ __('checklists_voucheradmin.SPECMISSTAXA') }}
    </x-link>
    <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=problemtaxacsv&clid=' . $clid) }}">
        {{ __('checklists_voucheradmin.SPECMISSPELLED') }}
    </x-link>
</div>
