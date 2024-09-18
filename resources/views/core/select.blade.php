@props(['label', 'id' => uniqid(), 'name' => 'select'])
<div>
    @if($label ?? false)
    <label class="text-lg" for="{{ $id }}">{{$label}}</label>
    @endif
    <div class="grid">
        <select name="{{$name}}" id="{{ $id }}" {{ $attributes->twMerge("
            focus:ring-accent
            focus:outline-none
            focus:ring-2
            rounded-md appearance-none
            bg-base-200 bg-opacity-50 border-base-300
            p-1 border
            row-start-1 col-start-1
            ")}}>
            {{ $slot }}
        </select>
        <div
            class="pointer-events-none row-start-1 col-start-1 fa-solid fa-caret-down items-end w-fit h-fit ml-auto my-auto mr-4">
        </div>
    </div>
</div>
