@props(['hasHeader' => true, 'hasNavbar' => true, 'hasFooter' => true])
@php
$navigations = [
    ["title" => __("header.home"), "link" => url('/'), "htmx" => true],
    ["title" => __("header.collections"), "link" => url('/collections/search'), 'htmx' => true ],
    ["title" => __("header.map_search"), "link" => legacy_url('/collections/map/index.php')],
    ["title" => __("header.species_checklists"), "link" => url('/checklists'), 'htmx' => true],
    ["title" => __("header.media"), "link" => url('/media/search'), 'htmx' => true],
    ["title" => __("header.data_use"), "link" => url('/usagepolicy'), 'htmx' => true],
    ["title" => __("header.symbiota_help"), "link" => docs_url()],
    ["title" => __("header.sitemap"), "link" => url('/sitemap'), 'htmx' => true],
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

<body x-trap>
    {{-- This div with the snapshots is to prevent alpine from try to render dom state using the html history,
         dom history will need to be handled in a different way
    --}}
    <div id="app-body" class="min-h-screen flex flex-col bg-base-100 text-base-content" x-data="{
        innerHTMLSnapshot: null,
        init: () => {
            innerHTMLSnapshot = $el.innerHTML
        },
        setSnapshot: () => { $el.innerHTML = innerHTMLSnapshot }
        }"
        x-on:htmx:before-history-save.window.camel="setSnapshot()"
        >
        @if($hasHeader)
        <x-header buttonVariant="primary"/>
        @endif
        @if($hasNavbar)
        <x-navbar :navigations="$navigations" class="bg-navbar text-navbar-content" />
        @endif
        <x-toaster />
        <div {{ $attributes->twMerge('flex-grow p-10') }} >
            {{ $slot }}
        </div>
        @if($hasFooter)
        <div class="bg-footer text-footer-content p-8">
            <x-footer :logos="$logos" :grants="$grants" />
        </div>
        @endif
        @stack('js-scripts')
    </div>
</body>

</html>
