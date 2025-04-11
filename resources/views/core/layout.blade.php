@props(['hasHeader' => true, 'hasNavbar' => true, 'hasFooter' => true])
@php
$navigations = [
    ["title" => __("header.home"), "link" => '/', "htmx" => true],
    //["title" => __("header.collections"), "link" => config('portal.name') . '/collections/search/index.php'],
    ["title" => __("header.collections"), "link" => '/collections/search', 'htmx' => true ],
    ["title" => __("header.map_search"), "link" => config('portal.name') . '/collections/map/index.php'],
    //["title" => __("header.species_checklists"), "link" => config('portal.name') . '/checklists/index.php'],
    ["title" => __("header.species_checklists"), "link" => '/checklists', 'htmx' => true],
    ["title" => __("header.media"), "link" => '/media/search', 'htmx' => true],
    ["title" => __("header.data_use"), "link" => '/usagepolicy', 'htmx' => true],
    ["title" => __("header.symbiota_help"), "link" => 'https://biokic.github.io/symbiota-docs/'],
    ["title" => __("header.sitemap"), "link" => config('portal.name') . '/sitemap.php'],
];

$logos = [
    [
        "img" => url('/images/logo_nsf.gif'),
        "link" => 'https://www.nsf.gov',
        "title" => 'NSF'
    ],
    [
        "img" =>url('/images/logo_idig.png'),
        "link" => 'http://idigbio.org',
        "title" => 'iDigBio'
    ],
    [
        "img" => url('/images/logo-asu-biokic.png'),
        "link" => 'https://biokic.asu.edu',
        "title" => 'Biodiversity Knowledge Integration Center'
    ],
];

$grants= [
    [
        "link" => 'https://www.nsf.gov',
        "label" => '#-------',
        "grant_id" => ''
    ],
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/js/app.js','resources/css/app.css'])
    @stack('css-styles')
    {{-- Note This stack should only be used if navigating without partial load. Currently only dev documentation --}}
    @stack('js-libs')
</head>

<body id="app-body" x-trap>
    {{-- This div with the snapshots is to prevent alpine from try to render dom state using the html history,
         dom history will need to be handled in a different way
    --}}
    <div class="min-h-screen flex flex-col bg-base-100 text-base-content" x-data="{
        innerHTMLSnapshot: null,
        init: () => {
            innerHTMLSnapshot = $el.innerHTML
        },
        setSnapshot: () => { $el.innerHTML = innerHTMLSnapshot }
        }"
        x-on:htmx:before-history-save.window.camel="setSnapshot()"
        >
        @if($hasHeader)
        <x-header />
        @endif
        @if($hasNavbar)
            <x-navbar :navigations="$navigations" />
        @endif
        <x-toaster />
        <div {{ $attributes->twMerge('flex-grow p-10') }} >
            {{ $slot }}
        </div>
        @if($hasFooter)
        <div class="bg-base-200 p-8">
            <x-footer :logos="$logos" :grants="$grants" />
        </div>
        @endif
        @stack('js-scripts')
    </div>
</body>

</html>
