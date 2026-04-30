@props(['title'=>'Taxon-linked item', 'warning'=>'Warning!', 'items'=>[], 'itemNamePlural'=>'items'])
@php $itemCount = is_countable($items) ? count($items) : (int)$items; @endphp
<span class="text-lg font-bold">{{ $title }}</span>
@if($itemCount > 0)
    <p class="text-error-darker mt-2">{{ $warning }}</p>
    @if(is_countable($items))
        <ul class="mt-2 list-inside list-disc">
            @foreach($items as $item)
                @php
                    $itemUrl = is_array($item) ? $item['url'] : (is_string($item) ? $item : $item->url);
                    $itemName = is_array($item) ? $item['name'] : (is_string($item) ? $item : $item->name);
                @endphp
                <li><x-link :href="$itemUrl">{{ $itemName }}</x-link></li>
            @endforeach
        </ul>
    @endif
@endif
@if($itemCount < 1)
    <p class="text-accent-darker mt-2">Approved: No {{ $itemNamePlural }} linked to this taxon.</p>
@endif
