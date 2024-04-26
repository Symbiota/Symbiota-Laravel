@props(['id', 'label', 'name', 'open' => false])
<div class="w-full" x-data="{ open: {{ $open? 'true': 'false'}}}">
    <!-- Accordion Title --->
    <button
        class="bg-primary mb-[-0.25rem] text-lg font-bold rounded-sm uppercase text-primary-content w-full py-2 focus:outline-none focus:ring-4 ring-secondary hover:ring-4"
        @@click="open = !open">
        <div class="flex">
            <div class="w-12"></div>
            <div class="m-auto">{{ $label }}</div>
            <div class="flex w-12 justify-end">
                <i x-show="!open" class="text-2xl fa-solid fa-caret-up mr-5"></i>
                <i x-cloak x-show="open" class="text-2xl fa-solid fa-caret-down mr-5"></i>
            </div>
        </div>
    </button>

    <!-- Accordion Body ---->
    <div class="border-base-300 bg-base-100 border-b border-x p-4" x-cloak x-show="open">
        {{$slot}}
    </div>
</div>
