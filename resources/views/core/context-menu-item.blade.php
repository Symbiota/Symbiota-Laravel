@props(['type' => null, 'title' => ''])
@if($type === 'divider')
    <div class="bg-base-300 -mx-1 my-1 h-px"></div>

@elseif($type === 'nested')
    <div class="group relative">
        <div
            class="hover:bg-base-200 flex cursor-default items-center rounded px-2 py-1.5 pl-8 outline-none select-none"
        >
            <span>{{ $title }}</span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="ml-auto h-4 w-4"
            >
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </div>
        <div
            data-submenu
            class="invisible absolute top-0 right-0 mr-1 translate-x-full opacity-0 duration-200 ease-out group-hover:visible group-hover:mr-0 group-hover:opacity-100"
        >
            <div
                class="animate-in slide-in-from-left-1 z-50 w-48 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md"
            >
                {{ $slot }}
            </div>
        </div>
    </div>
@else
    <div
        {{ $attributes }}
        @click="contextMenuOpen = false"
        class="group hover:bg-base-200 relative flex cursor-default items-center rounded px-2 py-1.5 pl-8 outline-none select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
    >
        {{ $slot }}
    </div>
@endif
