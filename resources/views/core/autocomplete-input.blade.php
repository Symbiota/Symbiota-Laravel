@props(['id', 'label', 'error_text', 'assistive_text'])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function autoSearchInit(el) {
        const input = el.querySelector('input');
        const menu = el.querySelector('#search-results');

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
            e.detail.parameters.taxa = getLastValue(e.detail.parameters.taxa)
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
                    const incoming_option = menu.children[getIndex()].innerHTML
                    const prev_options = input.value.slice(0, input.value.lastIndexOf(",") + 1)
                    input.value = (prev_options ? prev_options + " " : prev_options) + incoming_option;
                    break;
            }
        })
    }
</script>
@endPushOnce
<div x-data="{el: $el, open: false, results: false}" x-init="autoSearchInit($el)">
    <x-input
        autocomplete="off"
        type="search"
        hx-get="/api/taxa/search"
        hx-trigger="input changed delay:500ms, search"
        hx-indicator=".htmx-indicator"
        hx-target="#search-results"
        x-on:blur="open = false"
        x-on:keyup.enter="open = false"
        x-on:focus="open = true"
        x-on:click="open = true"
        name='taxa'
        :id="$id"
        :label="$label" />
    <div class="relative w-full">
        <div class="htmx-indicator absolute w-full mt-1 bg-base-100 border-base-300 border p-1">
            <div class="flex items-center justify-center gap-1 text-base-content">
                <div class="stroke-secondary w-8 h-8">
                    <x-icons.loading/>
                </div>
                Searching
            </div>
        </div>
        <div
            x-on:htmx:after-swap="open = true; results = $el.children.length > 0"
            x-on:click="open = false"
            data-selected-index="0"
            x-cloak
            x-show="open && results"
            x-ref="menu"
            class="mt-1 h-fit absolute bg-base-100 w-full border-base-300 border"
            id="search-results">
        </div>
    </div>
</div>
