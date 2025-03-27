{{-- See Laravel Paginator Docs For LengthAwarePaginator properties and methods --}}
@props(['lengthAwarePaginator', 'hx_target' => 'body'])
@php
$start = ($lengthAwarePaginator->currentPage() - 1 ) * $lengthAwarePaginator->perPage();
$end = $start + $lengthAwarePaginator->perPage();
$start += 1;

$start_page = $lengthAwarePaginator->currentPage() < 6? 1: $lengthAwarePaginator->currentPage() - 4;
$end_page = $lengthAwarePaginator->currentPage() < 6? 9: $lengthAwarePaginator->currentPage() + 4;
$max_page = ceil($lengthAwarePaginator->total() / $lengthAwarePaginator->perPage());

if($end > $lengthAwarePaginator->total()) {
    $end = $lengthAwarePaginator->total();
}
if($end_page > $max_page) {
    $end_page = $max_page;
}
@endphp

<div class="flex items-center justify-between w-full">
    <p class="text-sm text-base-content">Showing <span class="font-medium">{{ $start }}</span> to <span class="font-medium">{{ $end }}</span> of <span class="font-medium">{{ $lengthAwarePaginator->total() }}</span> results</p>
    <nav>
        <ul class="flex items-center text-sm leading-tight bg-base-100 border divide-x rounded h-9 text-base-content/75 divide-base-300 border-base-300">
            <li class="h-full divide-r">
                <a hx-push-url="true" hx-target="{{$hx_target}}" hx-get="{{ $lengthAwarePaginator->previousPageUrl() }}" class="relative inline-flex items-center h-full px-3 ml-0 rounded-l group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent cursor-pointer">
                    <span>Previous</span>
                    <span class="box-content absolute bottom-0 w-0 h-px -mx-px duration-200 ease-out translate-y-px border-transparent bg-base-content group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full"></span>
                </a>
            </li>
            @for ($i = $start_page; $i <= $end_page; $i++)
            @php $isCurrent = $lengthAwarePaginator->currentPage() === $i; @endphp
                <li class="hidden h-full md:block">
                    <a hx-push-url="true" hx-target="{{$hx_target}}" hx-get="{{ $lengthAwarePaginator->url($i) }}" @class([
                        'relative inline-flex items-center h-full px-3 outline-none ring-inset focus:ring focus:ring-accent cursor-pointer',
                        'text-base-content group bg-base-300/50' => $isCurrent,
                        'group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent' => !$isCurrent,
                        ])>
                        <span>{{ $i }}</span>
                            <span @class([
                            'box-content absolute bottom-0 translate-y-px bg-base-content',
                            'left-0 w-full h-px mx-px  border-l border-r  border-base-300' => $isCurrent,
                            'w-0 h-px mx-px duration-200 ease-out border-transparent group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full' => !$isCurrent
                            ])></span>
                    </a>
                </li>
            @endfor

            <li class="h-full">
                <a hx-push-url="true" hx-target="{{$hx_target}}" hx-get="{{ $lengthAwarePaginator->nextPageUrl() }}" class="relative inline-flex items-center h-full px-3 rounded-r group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent cursor-pointer">
                    <span>Next</span>
                    <span class="box-content absolute bottom-0 w-0 h-px -mx-px duration-200 ease-out translate-y-px border-transparent bg-base-content group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full"></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
