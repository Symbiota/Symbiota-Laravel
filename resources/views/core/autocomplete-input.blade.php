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

            if(menu.children[old_idx]) {
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

        /*
        input.addEventListener('input', (e) => {
        });

        input.addEventListener('htmx:afterSettle', (e) => {
            console.log('settle')
        });

        */
        menu.addEventListener('htmx:after-swap', (e) => {
            setIndex(0);
        });

        input.addEventListener('keydown', (e) => {
            switch (e.key) {
                case "ArrowUp":
                    setIndex(getIndex() - 1)
                    console.log(getIndex());
                    e.preventDefault();
                    break;
                case "ArrowDown":
                    setIndex(getIndex() + 1)
                    console.log(getIndex());
                    e.preventDefault();
                    break;
                case "Enter":
                    console.log(getIndex());
                    break;
            }
        })
    }
</script>
@endPushOnce
<div x-data="{el: $el, open: false, results: false}" x-init="autoSearchInit($el)">
    <div class="htmx-indicator">
        ...Searching
    </div>
    <x-input type="search" placeholder="Begin Typing To Search Users..."
        x-on:focus="open = true"
        x-on:blur="open = false"
        hx-get="Portal/rpc/taxasuggest.php" hx-trigger="input changed delay:500ms, search"
        hx-indicator=".htmx-indicator" hx-target="#search-results" name='term' :id="$id" :label="$label" />
    <div class="relative w-full">
        <div x-on:htmx:after-swap="open = true; results = $el.children.length > 0" data-selected-index="0" x-cloak x-show="open && results" x-ref="menu"
            class="mt-1 h-fit absolute bg-base-100 w-full border-base-300 border" id="search-results">
        </div>
    </div>

</div>
