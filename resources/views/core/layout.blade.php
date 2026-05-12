@props(['hasHeader' => true, 'hasNavbar' => true, 'hasFooter' => true, 'hasToaster' => false])
@php
$navigations = [
    ["title" => __("header.H_HOME"), "link" => url('/'), "htmx" => true],
    ["title" => __("header.H_SEARCH"), "link" => url('/collections/search'), 'htmx' => true ],
    ["title" => __("header.H_MAP_SEARCH"), "link" => legacy_url('/collections/map/index.php')],
    ["title" => __("header.H_INVENTORIES"), "link" => url('/checklists'), 'htmx' => true],
    ["title" => __("header.H_MEDIA"), "link" => url('/media/search'), 'htmx' => true],
    ["title" => __("header.H_DATA_USAGE"), "link" => url('/usagepolicy'), 'htmx' => true],
    ["title" => __("header.H_HELP"), "link" => docs_url()],
    ["title" => __("header.H_SITEMAP"), "link" => url('/sitemap'), 'htmx' => true],
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
        "label" => '#------',
        "grant_id" => ''
    ],
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite([
        'resources/js/htmx.js',
        'resources/js/editor.js',
        'resources/js/leaflet.js',
        'resources/js/chart.js',
        'resources/js/app.js',
        'resources/css/app.css'
    ])
    @stack('head')
    @stack('css-styles')
    {{-- Note This stack should only be used if navigating without partial load. Currently only dev documentation --}}
    @stack('js-libs')
</head>

<body x-trap="true" class="bg-base-100 text-base-content flex min-h-screen flex-col">
    @if($hasHeader)
        <x-header buttonVariant="primary" />
    @endif

    @if($hasNavbar)
        <x-navbar :navigations="$navigations" />
    @endif

    <main id="app-body" {{ $attributes->twMerge('flex-grow p-10') }}>
        {{ $slot }}

        @if($hasToaster)
            <x-toaster />
        @endif
    </main>

    @if($hasFooter)
        <x-footer :logos="$logos" :grants="$grants" />
    @endif

    @stack('js-scripts')
</body>
</html>
