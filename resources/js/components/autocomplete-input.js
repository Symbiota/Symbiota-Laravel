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
        if (!str_val) return str_val;
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

        if (request_settings) {
            if (request_settings.alias) {
                for (let alias in request_settings.alias) {
                    if (e.detail.parameters[alias]) {
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
window.autoSearchInit = autoSearchInit;
