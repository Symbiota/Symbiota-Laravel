@props(['title'=>'Taxon-linked item', 'warning'=>'Warning!', items=>[], 'itemNamePlural'=>'items'])
<span class="font-bold text-lg">{{ $title }}</span>
@if($items->count() > 0)
    <p class="mt-2">{{ $warning }}</p>
    <ul class="list-disc list-inside mt-2">
        @foreach($items as $item)
            <li><x-link :href="$item->url">{{ $item->name }}</x-link></li>
        @endforeach
@endif
@if($items->count() < 1)
    <p class="mt-2">Approved: No {{ $itemNamePlural }} linked to this taxon.</p>
@endif