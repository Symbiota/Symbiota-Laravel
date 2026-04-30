@props(['title'=>'Taxon-linked item', 'warning'=>'Warning!', 'items'=>[], 'itemNamePlural'=>'items'])
@php $itemCount = is_countable($items) ? count($items) : (int)$items; @endphp
<span class="font-bold text-lg">{{ $title }}</span>
@if($itemCount > 0)
    <p class="mt-2 text-error-darker">{{ $warning }}</p>
    @if(is_countable($items))
        <ul class="list-disc list-inside mt-2">
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
    <p class="mt-2 text-accent-darker">Approved: No {{ $itemNamePlural }} linked to this taxon.</p>
@endif