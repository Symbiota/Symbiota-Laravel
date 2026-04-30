{{-- See Laravel Paginator Docs For LengthAwarePaginator properties and methods --}}
@props(['lengthAwarePaginator', 'hx_target' => 'body', 'jumpId' => false])
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

<div class="flex w-full items-center justify-between">
    <p class="text-base-content text-sm">Showing <span class="font-medium">{{ $start }}</span> to <span class="font-medium">{{ $end }}</span> of <span class="font-medium">{{ $lengthAwarePaginator->total() }}</span> results</p>
    <nav>
        <ul
            class="bg-base-100 text-base-content/75 divide-base-300 border-base-300 flex h-9 items-center divide-x rounded border text-sm leading-tight"
        >
            <li class="divide-r h-full">
                <a
                    hx-push-url="true"
                    hx-target="{{ $hx_target }}"
                    hx-get="{{ $lengthAwarePaginator->previousPageUrl() }}"
                    class="group hover:text-base-content focus:ring-accent relative ml-0 inline-flex h-full cursor-pointer items-center rounded-l px-3 outline-none ring-inset focus:ring"
                >
                    <span>Previous</span>
                    <span
                        class="bg-base-content group-hover:border-base-300 absolute bottom-0 left-1/2 -mx-px box-content h-px w-0 translate-y-px border-transparent duration-200 ease-out group-hover:left-0 group-hover:w-full group-hover:border-r group-hover:border-l"
                    ></span>
                </a>
            </li>
            @for($i = $start_page; $i <= $end_page; $i++)
                @php $isCurrent = $lengthAwarePaginator->currentPage() === $i; @endphp
                <li class="hidden h-full md:block">
                    <a
                        hx-push-url="true"
                        hx-target="{{ $hx_target }}"
                        hx-get="{{ $lengthAwarePaginator->url($i) }}"
                        {{ $jumpId? 'href='. $jumpId: '' }}
                        @class([
                        'relative inline-flex items-center h-full px-3 outline-none ring-inset focus:ring focus:ring-accent cursor-pointer',
                        'text-base-content group bg-base-300/50' => $isCurrent,
                        'group hover:text-base-content outline-none focus:ring ring-inset focus:ring-accent' => !$isCurrent,
                        ])
                    >
                        <span>{{ $i }}</span>
                        <span
                            @class([
                            'box-content absolute bottom-0 translate-y-px bg-base-content',
                            'left-0 w-full h-px mx-px  border-l border-r  border-base-300' => $isCurrent,
                            'w-0 h-px mx-px duration-200 ease-out border-transparent group-hover:border-l group-hover:border-r group-hover:border-base-300 left-1/2 group-hover:left-0 group-hover:w-full' => !$isCurrent
                            ])
                        ></span>
                    </a>
                </li>
            @endfor

            <li class="h-full">
                <a
                    hx-push-url="true"
                    hx-target="{{ $hx_target }}"
                    hx-get="{{ $lengthAwarePaginator->nextPageUrl() }}"
                    class="group hover:text-base-content focus:ring-accent relative inline-flex h-full cursor-pointer items-center rounded-r px-3 outline-none ring-inset focus:ring"
                >
                    <span>Next</span>
                    <span
                        class="bg-base-content group-hover:border-base-300 absolute bottom-0 left-1/2 -mx-px box-content h-px w-0 translate-y-px border-transparent duration-200 ease-out group-hover:left-0 group-hover:w-full group-hover:border-r group-hover:border-l"
                    ></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
