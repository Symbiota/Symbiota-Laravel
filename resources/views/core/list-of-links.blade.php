@props(['links' => []])

@if(!empty($links))
    <ul class="list-disc pl-4">
        @foreach($links as $title => $link)
            @if($link)
                <li><x-link href="{{ $link }}">{{ $title }}</x-link></li>
            @endif
        @endforeach

        @if($slot->isNotEmpty()) {{ $slot }} @endif
    </ul>
@endif
