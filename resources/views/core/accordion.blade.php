@props(['id', 'label', 'name', 'open' => false, 'variant' => false])
<div id="{{$id ?? uniqid()}}" data-blade-accordion x-data="{ open: {{ $open? 'true': 'false'}}}" {{ $attributes->twMerge('w-full')}}>
    <!-- Accordion Title --->
    <x-button type="button" x-on:click="open = !open" :variant="$variant"
        {{ $attributes->twMergeFor('button', 'w-full rounded-sm px-0 text-xl mb-1 hover:ease-in duration-150') }}
    >
        <div class="flex w-full py-1 ">
            <div class="w-12"></div>
            <div class="m-auto">{{ $label }}</div>
            <div class="flex w-12 justify-end">
                <i x-show="!open" class="text-2xl fa-solid fa-caret-up mr-5"></i>
                <i x-cloak x-show="open" class="text-2xl fa-solid fa-caret-down mr-5"></i>
            </div>
        </div>
    </x-button>

    <!-- Accordion Body ---->
    <div x-cloak x-show="open"
        x-transition:enter="transition ease-out duration-300 delay-50"
        x-transition:enter-start="opacity-0 h-10 "
        x-transition:enter-end="opacity-100 h-100"
        {{ $attributes->twMergeFor('body', 'border-base-300 bg-base-100 border-b border-x p-4') }}
        >
        {{$slot}}
    </div>
</div>
