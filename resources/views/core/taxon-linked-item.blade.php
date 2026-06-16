@props(['title'=>'Taxon-linked item', 'warning'=>'Warning!', 'items'=>[], 'itemNamePlural'=>'items', 'itemType'=>'synoym'])
@php $itemCount = is_countable($items) ? count($items) : (int)$items; @endphp
<span class="text-lg font-bold">{{ $title }}</span>
@if($itemCount > 0)
    <p class="text-error-darker mt-2">{{ $warning }}</p>
    @if(is_countable($items))
        <ul class="mt-2 list-inside list-disc">
            @foreach($items as $id => $item)
                @php
                    $itemUrl = is_array($item) ? $item['url'] : (is_string($item) ? $item : $item->url);
                    $editItemUrl = $itemUrl . '/edit';
                    if($itemType === 'synonym') {
                        $itemUrl = url('/taxon/' . ($id ?? ''));
                        $editItemUrl = url('/taxon/' . ($id ?? '') . '/edit');
                    }
                    $itemName = is_array($item) ? $item['name'] : (is_string($item) ? $item : $item->name);
                @endphp
                <li>
                    <span>
                        <x-link class="pr-3" :href="$itemUrl">{{ $itemName }}</x-link>
                        @can('SUPER_ADMIN')
                            <x-link :href="$editItemUrl"><i class="fa-solid fa-pen pr-1"></i>({{ __('profile_tpeditor.EDIT_TAXON') }})</x-link>
                        @endcan
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
@endif
@if($itemCount < 1)
    <p class="text-accent-darker mt-2">Approved: No {{ $itemNamePlural }} linked to this taxon.</p>
@endif
