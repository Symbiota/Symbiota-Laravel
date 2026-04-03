@props(['nodes'=>[]])
<ul>
    @foreach($nodes as $node)
        <li x-data="{ open: false }">
            <div @click="open = !open">
                {{ $node->sciName }}
            </div>
            @if($node->children ?? false)
                <div class="flex flex-row gap-2">
                    <span>--->
                    </span>
                    <ul x-show="open">
                        <x-tree-node :nodes="$node->children ?? []" />
                    </ul>
                </div>
            @endif
        </li>
    @endforeach
</ul>