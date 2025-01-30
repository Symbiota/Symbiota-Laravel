@props([
    'id',
    'label' => false,
    'placeholder' => '',
    'search' => '',
    'name' => 'search',
    'request_config' => '{}',
    'vals' => '',
    'include' => '',
    'value' => '',
    'error_text',
    'assistive_text',
    'menu' => new Illuminate\View\ComponentSlot(),
    'input' => new Illuminate\View\ComponentSlot(),
    'indicator' => new Illuminate\View\ComponentSlot(),
    'result' => new Illuminate\View\ComponentSlot(),
])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function autoSearchInit(el) {
        const input = el.querySelector('input');

        const input_name = input.name;
        const request_settings = JSON.parse(input.getAttribute('data-request-config'));

        const menu = el.querySelector(`#search-results-${input.id}`);

        const getMenuLength = () => {
            return menu.children.length;
        }
        const getIndex = () => {
            return parseInt(menu.getAttribute('data-selected-index'));
        }
        const setIndex = (new_idx) => {
            const old_idx = getIndex();

            if (menu.children[old_idx]) {
                menu.children[old_idx].classList.remove("bg-base-200")
            }

            const maxIndex = menu.children.length;
            if (new_idx >= maxIndex) {
                new_idx = 0;
            } else if (new_idx < 0) {
                new_idx = maxIndex
            }

            menu.setAttribute('data-selected-index', new_idx);
            if (menu.children[new_idx]) {
                menu.children[new_idx].classList.add("bg-base-200")
            }

            return new_idx
        }

        function getLastValue(str_val) {
            if(!str_val) return str_val;
            return str_val.slice(str_val.lastIndexOf(",") + 1).trim();
        }

        menu.addEventListener('htmx:after-swap', (e) => {
            setIndex(0);
            for (let i = 0; i < e.target.children.length; i++) {
                //child.classList.add("hover:bg-base-200")
                e.target.children[i].classList.add("cursor-pointer")
                e.target.children[i].addEventListener('mouseover', () => setIndex(i))
                e.target.children[i].addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    const incoming_option = menu.children[i].innerHTML
                    const prev_options = input.value.slice(0, input.value.lastIndexOf(",") + 1)
                    input.value = (prev_options ? prev_options + " " : prev_options) + incoming_option;
                })
            }
        });

        input.addEventListener('htmx:configRequest', (e) => {
            e.detail.parameters[input_name] = getLastValue(e.detail.parameters[input_name])

            if(request_settings) {
                if(request_settings.alias) {
                    for(let alias in request_settings.alias) {
                        if(e.detail.parameters[alias]) {
                            const aliased_value = e.detail.parameters[alias];
                            delete e.detail.parameters[alias];
                            e.detail.parameters[request_settings.alias[alias]] = aliased_value;
                        }
                    }
                }
            }
        })

        input.addEventListener('keydown', (e) => {
            switch (e.key) {
                case "ArrowUp":
                    setIndex(getIndex() - 1)
                    e.preventDefault();
                    break;
                case "ArrowDown":
                    setIndex(getIndex() + 1)
                    e.preventDefault();
                    break;
                case "Enter":
                    e.preventDefault();
                    const incoming_option = menu.children[getIndex()].innerHTML
                    const prev_options = input.value.slice(0, input.value.lastIndexOf(",") + 1)
                    input.value = (prev_options ? prev_options + " " : prev_options) + incoming_option;
                    break;
            }
        })
    }
</script>
@endPushOnce
<div x-data="{el: $el, open: false, results: {{!$result->isEmpty()? 'true' :'false'}}}" x-init="autoSearchInit($el)" class="w-full">
    <x-input
        value="{{ $value }}"
        autocomplete="off"
        type="search"
        hx-get="{{ $search }}"
        hx-include="{{ $include }}"
        hx-vals="{{ $vals }}"
        data-request-config="{{ $request_config }}"
        hx-trigger="input changed delay:700ms, search"
        hx-indicator="#menu-loader-{{$id}}"
        hx-target="#search-results-{{$id}}"
        hx-replace-url="false"
        hx-push-url="false"
        x-on:htmx:before-send.stop="results = false"
        x-on:blur="open = false"
        x-on:keyup.enter="open = false"
        x-on:focus="open = true"
        x-on:click="open = true"
        :placeholder="$placeholder"
        :name='$name'
        :id="$id"
        :label="$label"
        :class="$input->attributes->get('class')"
    />
    <div {{$menu->attributes->twMerge('relative w-full')}}>
        <div id="menu-loader-{{$id}}" class="htmx-indicator">
        <div {{$indicator->attributes->twMerge('absolute w-full mt-1 bg-base-100 border-base-300 border p-1')}}>
               @if ($indicator->isEmpty())
                <div class="flex items-center justify-center gap-1 text-base-content">
                    <div class="stroke-accent w-8 h-8">
                        <x-icons.loading/>
                    </div>
                    Searching
                </div>
               @else
                    {{ $indicator }}
               @endif
        </div>
        </div>
        <div
            x-on:htmx:after-swap="open = true; results = $el.children.length > 0"
            x-on:click="open = false"
            data-selected-index="0"
            x-cloak
            x-show="open && results"
            x-ref="menu"
            id="search-results-{{$id}}"
            {{ $result->attributes->twMerge("mt-1 h-fit absolute bg-base-100 z-50 w-full border-base-300 border")}}>
            {{ $result }}
        </div>
    </div>
</div>
