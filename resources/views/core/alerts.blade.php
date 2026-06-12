@props(['messages'])
@if(count($messages) > 0)
    <div class="mb-4 flex flex-col gap-4">
        @foreach($messages->all() as $msg)
            <div {{ $attributes->twMerge('bg-base-200 text-base-content rounded-md p-4') }}>{{ $msg }}</div>
        @endforeach
    </div>
@endif
