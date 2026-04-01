@props(['links' => []])

@if(!empty($links))
    <ul class="pl-4">
    @foreach ($links as $title => $link)
        @if($link)
        <li class="list-disc"><x-link href="{{ $link }}">{{ $title }}</x-link></li>
        @endif
    @endforeach
    </ul>
@endif
