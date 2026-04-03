@props(['nodes'=>[]])
<ul>
    @foreach($nodes as $node)
        <li x-data="{ open: false }">
            @if($node->children ?? false)
                <div @click="open = !open">
                    <x-link>{{ $node->sciName }}+</x-link>
                </div>
            @endif
            @if(!($node->children ?? false))
                <div class="pl-2">
                    <x-link href="{{url('taxon/' . $node->tid)}}">{{ $node->sciName }}</x-link>
                </div>
            @endif
            @if($node->children ?? false)
                <div class="pl-2">
                    <ul x-show="open">
                        <x-tree-node :nodes="$node->children ?? []" />
                    </ul>
                </div>
            @endif
        </li>
    @endforeach
</ul>