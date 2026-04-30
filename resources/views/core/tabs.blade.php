@props(['tabs' => ['Tab 1', 'Tab 2'], 'active' => 0])
<div
    x-data="{
        el: $el, active: {{ $active }},
        wireTabs: (tab_group, tab_count) => {
            const tab_bodies = tab_group.querySelector('#tab-body');

            if(tab_bodies && tab_bodies.children) {
                for(let i = 0; i < tab_bodies.children.length; i++) {
                    const body = tab_bodies.children[i].setAttribute('x-show', 'active === ' + i);
                }
            }
        }
    }"
    x-init="
        wireTabs(el, {{ count($tabs) }})
        $watch('active', function(){
            $el.dispatchEvent(new CustomEvent('tabChanged', {
                detail: {
                    active,
                }
            }))
        })
    "
    {{ $attributes->twMerge('w-full') }}
>
    {{-- Tab Menu --}}
    <div {{ $attributes->twMergeFor('head', 'flex gap-1') }}>
        @for($i = 0; $i < count($tabs); $i++)
            <div
                :class="active === {{ $i }}? 'bg-base-100': 'bg-base-200/50'"
                class="bg-base-100 border-base-300 relative border-x border-t"
            >
                <input
                    class="ring-accent absolute h-full w-full cursor-pointer appearance-none outline-none focus:ring"
                    id="tab-{{ $i }}"
                    x-on.self:change="active = {{ $i }}"
                    x-on.self:focus="active = {{ $i }}"
                    type="radio"
                    name="tab"
                />
                <label for="tab-{{ $i }}">
                    <div class="px-4 py-2 text-lg">{{ $tabs[$i] }}</div>
                </label>
                <div class="bg-accent absolute bottom-0 w-full" :class="active === {{ $i }} && 'h-1'"></div>
            </div>
        @endfor
    </div>

    {{-- Tab Body --}}
    <div id="tab-body" {{ $attributes->twMergeFor('body', 'p-4 border border-base-300') }}> {{ $slot }}</div>
</div>
