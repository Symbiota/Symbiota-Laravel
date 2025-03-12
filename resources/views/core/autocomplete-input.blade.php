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
{{-- See resouces/js/components/autocomplete-input.js for scripts --}}
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
