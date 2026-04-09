@props(['nodes'=>[], 'depth'=>0, 'rankMap' => [], 'standardizingFraction' => 3, 'parentRankId' => null])
<ul>
    @foreach($nodes as $node)
        <li x-data="{ open: false }">
            @if(count($node->children ?? []) > 0)
                <div style="padding-left: {{ ($rankMap[$node->rankID]/$standardizingFraction ?? 0) }}rem" @click="open = !open">
                    <x-link class="no-underline">{{ $node->sciName }} author is: {{$node->author ?? ''}} rank is: {{$node->rankID}} [depth is {{$depth}}] (Click to <span x-text="open ? 'collapse' : 'show all direct children'"></span>)</x-link>
                </div>
                <div style="padding-left: {{ $node->rankID > $parentRankId ? ($rankMap[$node->rankID]/$standardizingFraction + $depth ?? 0) : 0 }}rem" x-show="open">
                    <x-tree-node :nodes="$node->children ?? []" :depth="$depth + 1" :parentRankId="$node->rankID" :rankMap="$rankMap" :standardizingFraction="$standardizingFraction" />
                </div>
            @else
                <div style="padding-left: {{ $node->rankID > $parentRankId ? ($rankMap[$node->rankID]/$standardizingFraction + $depth ?? 0) : 0 }}rem">
                    <x-link href="{{url('taxon/' . $node->tid)}}">{{ $node->sciName }} author is: {{$node->author ?? ''}} rank is: {{$node->rankID}} [depth is {{$depth}}] [parent rankID is {{$parentRankId}}]</x-link>
                </div>
            @endif
        </li>
    @endforeach
</ul>