@props([
    'id' => uniqid(),
    'button' => new Illuminate\View\ComponentSlot(),
    'title' => new Illuminate\View\ComponentSlot(),
    'body' => new Illuminate\View\ComponentSlot()
])
@if(isset($button) && !$button->isEmpty())
    <x-button command="show-modal" commandfor="{{ $id }}" @click="modalOpen = true" :attributes="$button->attributes">
        {{ $button }}
    </x-button>
@endif
<dialog
    id="{{ $id }}"
    class="m-auto bg-transparent backdrop:bg-black/40"
    onclick="event.target.id == '{{ $id }}'? event.target.close(): null"
>
    <div class="border-base-300 bg-base-100 rounded-md border p-4">
        <div class="flex items-center">
            @isset($title)
                <div {{ $title->attributes->twMerge('font-bold') }}>{{ $title }}</div>
            @endisset
            <div class="flex grow justify-end">
                <button
                    commandfor="{{ $id }}"
                    command="close"
                    class="hover:bg-base-200 hover:ring-accent focus:ring-accent h-8 w-8 cursor-pointer rounded-full focus:ring-2 focus:outline-none"
                >
                    <i class="fas fa-close"></i>
                </button>
            </div>
        </div>

        @isset($body)
            <div {{ $body->attributes->twMerge('relative w-[50rem] max-w-[75vw]') }}> {{ $body }}</div>
        @endisset
    </div>
</dialog>
