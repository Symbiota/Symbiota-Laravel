@props(['nodes'=>[]])
<ul>
    @foreach($nodes as $node)
        <li x-data="{ open: false }">
            <div @click="open = !open">
                {{ $node->name }}
            </div>

            <ul x-show="open">
                @include('tree-node', ['nodes' => $node->children])
            </ul>
        </li>
    @endforeach
</ul>