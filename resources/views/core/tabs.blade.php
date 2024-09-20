@props(['tabs' => ['Tab 1', 'Tab 2'], 'active' => 0])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function wireTabs(tab_group, tab_count) {
        const tab_bodies = tab_group.querySelector('#tab-body');

        if(tab_bodies && tab_bodies.children) {
            for(let i = 0; i < tab_bodies.children.length; i++) {
                const body = tab_bodies.children[i].setAttribute('x-show', "active === " + i);
            }
        }
    }
</script>
@endPushOnce
<div {{$attributes->twMerge('w-full')}} x-data="{el: $el, active: {{ $active }}}" x-init="wireTabs(el, {{ count($tabs) }})">
    {{-- Tab Menu --}}
    <div class="flex gap-1">
        @for ($i = 0; $i < count($tabs); $i++)
        <div :class="active === {{ $i }}? 'bg-base-100': 'bg-opacity-50 bg-base-200'" class="relative bg-base-100 border-x border-t">
            <input class="appearance-none outline-none focus:ring ring-accent absolute cursor-pointer w-full h-full"
                id="tab-{{ $i }}" x-on.self:change="active = {{ $i }}"
                x-on.self:focus="active = {{ $i }}"
                type="radio"
                name="tab"
            />
            <label for="tab-{{ $i }}">
                <div class="py-2 px-4 text-lg">{{$tabs[$i]}}</div>
            </label>
            <div class="absolute bottom-0 w-full bg-accent" :class="active === {{ $i }} && 'h-1'"></div>
        </div>
        @endfor
    </div>

    {{-- Tab Body --}}
    <div id="tab-body" class="p-4 border">
        {{ $slot }}
    </div>
</div>
