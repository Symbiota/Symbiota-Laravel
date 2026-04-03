@props(['nodes'=>[], 'depth'=>0, 'rankMap' => [], 'standardizingFraction' => 3])
<ul>
    @foreach($nodes as $node)
        <li x-data="{ open: false }">
            @if(count($node->children ?? []) > 0)
                <div style="padding-left: {{ ($rankMap[$node->rankID]/$standardizingFraction ?? 0) }}rem" @click="open = !open">
                    <x-link class="no-underline">{{ $node->sciName }} {{$node->rankID}} (Click to <span x-text="open ? 'collapse' : 'expand'"></span>)</x-link>
                </div>
                <div style="padding-left: {{ ($rankMap[$node->rankID]/$standardizingFraction ?? 0) + ($depth * 2) }}rem" x-show="open">
                    <x-tree-node :nodes="$node->children ?? []" :depth="$depth + 1" :rankMap="$rankMap" :standardizingFraction="$standardizingFraction" />
                </div>
            @else
                <div style="padding-left: {{ ($rankMap[$node->rankID]/2 ?? 0) + ($depth * 2) }}rem">
                    <x-link href="{{url('taxon/' . $node->tid)}}">{{ $node->sciName }} {{$node->rankID}}</x-link>
                </div>
            @endif
        </li>
    @endforeach
</ul>