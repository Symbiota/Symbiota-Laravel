@props(['type' => null, 'title' => ''])
@if($type === 'divider')
<div class="h-px my-1 -mx-1 bg-base-300"></div>

@elseif($type === 'nested')
<div class="relative group">
    <div class="flex cursor-default select-none items-center rounded px-2 hover:bg-base-200 py-1.5 outline-none pl-8">
        <span>{{ $title }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="w-4 h-4 ml-auto">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </div>
    <div data-submenu
        class="absolute top-0 right-0 invisible mr-1 duration-200 ease-out translate-x-full opacity-0 group-hover:mr-0 group-hover:visible group-hover:opacity-100">
        <div
            class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md animate-in slide-in-from-left-1 w-48">
            {{ $slot }}
        </div>
    </div>
</div>
@else
<div {{ $attributes }} @click="contextMenuOpen=false"
    class="relative flex cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-base-200 outline-none pl-8  data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
    {{ $slot }}
</div>
@endif
