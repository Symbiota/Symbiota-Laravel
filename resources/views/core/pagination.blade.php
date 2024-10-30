{{-- See Laravel Paginator Docs For LengthAwarePaginator properties and methods --}}
@props(['lengthAwarePaginator'])
@php
$start = ($lengthAwarePaginator->currentPage() - 1 ) * $lengthAwarePaginator->perPage();
$end = $start + $lengthAwarePaginator->perPage();

if($end > $lengthAwarePaginator->total()) {
    $end = $lengthAwarePaginator->total();
}
@endphp

<div class="flex items-center justify-between w-full h-16">
    <p class="text-sm text-base-content">Showing <span class="font-medium">{{ $start }}</span> to <span class="font-medium">{{ $end }}</span> of <span class="font-medium">{{ $lengthAwarePaginator->total() }}</span> results</p>
    <nav>
        <ul class="flex items-center text-sm leading-tight bg-base-100 border divide-x rounded h-9 text-base-content/75 divide-base-300 border-base-300">
            <li class="h-full divide-r">
                <a href="{{ $lengthAwarePaginator->previousPageUrl() }}" class="relative inline-flex items-center h-full px-3 ml-0 rounded-l group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent">
                    <span>Previous</span>
                    <span class="box-content absolute bottom-0 w-0 h-px -mx-px duration-200 ease-out translate-y-px border-transparent bg-base-content group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full"></span>
                </a>
            </li>
            @for ($i = 1; $i <= ceil($lengthAwarePaginator->total() / $lengthAwarePaginator->perPage()); $i++)
            @php $isCurrent = $lengthAwarePaginator->currentPage() === $i; @endphp
                @if($isCurrent)
                <li class="hidden h-full md:block">
                    <a href="{{$lengthAwarePaginator->url($i)}}" class="relative inline-flex items-center h-full px-3 text-base-content group bg-base-200/50 outline-none ring-inset focus:ring focus:ring-accent">
                        <span>{{ $i }}</span>
                        <span class="box-content absolute bottom-0 left-0 w-full h-px -mx-px translate-y-px border-l border-r bg-base-content border-base-300"></span>
                    </a>
                </li>
                @else
                    <li class="hidden h-full md:block">
                        <a href="{{$lengthAwarePaginator->url($i)}}" class="relative inline-flex items-center h-full px-3 group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent">
                            <span>{{ $i }}</span>
                            <span class="box-content absolute bottom-0 w-0 h-px -mx-px duration-200 ease-out translate-y-px border-transparent bg-base-content group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full"></span>
                        </a>
                    </li>
                @endif
            @endfor

            <li class="h-full">
                <a href="{{ $lengthAwarePaginator->nextPageUrl() }}" class="relative inline-flex items-center h-full px-3 rounded-r group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent">
                    <span>Next</span>
                    <span class="box-content absolute bottom-0 w-0 h-px -mx-px duration-200 ease-out translate-y-px border-transparent bg-base-content group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full"></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
