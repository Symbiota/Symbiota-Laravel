@props(['id', 'label', 'name', 'open' => false])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function toggleNestedCheckboxes(event, id) {
        event.stopImmediatePropagation()
        //event.preventDefault()
        const master = document.querySelector(`#${id}`);

        console.log("fire")
        console.log(master.checked)
        const checkboxes = document.querySelectorAll(`#${id}-body input[type='checkbox']`);
        for (let checkbox of checkboxes) {
            checkbox.checked = master.checked;
        }
    }

    function toggleNested(event, id) {
    }
</script>
@endPushOnce
<div class="w-full" x-data="{ menu_open: true, checked: true}">
    <!-- Nested Checkbox Title --->
    <button @@click="menu_open = !menu_open" type="button" class="flex w-full outline-none">
        <x-checkbox onclick="toggleNestedCheckboxes(event, '{{$id}}')" :open="$open" :id="$id" :label="$label" />
        <div class="flex flex-grow justify-end">
            <i x-show="!menu_open" class="mr-5 fa-solid fa-angle-up"></i>
            <i x-cloak x-show="menu_open" class="mr-5 fa-solid fa-angle-down"></i>
        </div>
    </button>
    <!-- Nested Checkbox Body --->
    <div x-cloak x-show="menu_open" onclick="console.log('click')" id="{{$id}}-body" class="flex flex-col gap-3 ml-7 mt-2">
        {{ $slot }}
    </div>
</div>
