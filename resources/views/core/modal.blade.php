@props([
    'button' => new Illuminate\View\ComponentSlot(),
    'title' => new Illuminate\View\ComponentSlot(),
    'body' => new Illuminate\View\ComponentSlot()
])
<span x-data="{ modalOpen: false }"
    @keydown.escape.window="modalOpen = false"
    class="relative"
    :class="{'z-50 w-auto h-auto':modalOpen === true}"
    >

    @if(isset($button) && !$button->isEmpty())
    <x-button @click="modalOpen=true" :attributes="$button->attributes" >
        {{ $button }}
    </x-button>
    @endif

    <template x-teleport="body">
        <div x-show="modalOpen" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen" x-cloak>
            <div x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="modalOpen=false" class="absolute inset-0 w-full h-full bg-black/40"></div>
            <div x-show="modalOpen"
                x-trap.inert.noscroll="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-fit py-6 bg-white px-7 sm:rounded-lg">
                <div class="flex items-center justify-between pb-2">
                    @isset($title)
                        <div {{ $title->attributes->twMerge('font-bold') }}>{{ $title }}</div>
                    @endisset
                    <button @click="modalOpen=false" class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 text-gray-600 rounded-full hover:text-gray-800 hover:bg-gray-50">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                @isset($body)
                    <div {{ $body->attributes->twMerge('relative w-[50rem] max-w-[75vw]') }}>{{ $body }}</div>
                @endisset
            </div>
        </div>
    </template>
</span>
