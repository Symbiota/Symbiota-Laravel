@props(['hasHeader' => true, 'hasNavbar' => true, 'hasFooter' => true])
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
    @vite(['resources/js/app.js','resources/css/app.css'])
    @stack('head')
    @stack('css-styles')
    {{-- Note This stack should only be used if navigating without partial load. Currently only dev documentation --}}
    @stack('js-libs')

    {{--
    This Script cleans up alpine dom manipulations before htmx snapshots the page.
    Note other was have have been tried such as snapshotting before alpine does the dom
    manipulations but this did not work for repeated backwards and forwards history swapping.
    --}}
    <script>
        document.addEventListener('htmx:beforeHistorySave', (evt) => {
            document.querySelectorAll('[x-for]').forEach((item) => {
                item._x_lookup && Object.values(item._x_lookup).forEach((el) => el.remove())
            })
            document.querySelectorAll('[x-if]').forEach((item) => {
                item._x_currentIfEl && item._x_currentIfEl.remove()
            })

            document.querySelectorAll('[x-teleport]').forEach((item) => {
                item._x_teleport && item._x_teleport.remove();
            })

            if(window.tinymce_editor) {
                window.tinymce_editor.remove();
            }
        })
    </script>
</head>

<body
    x-trap="true"
>
    <div id="app-body" class="min-h-screen flex flex-col bg-base-100 text-base-content"
        >
        @if($hasHeader)
        <x-header buttonVariant="primary"/>
        @endif
        @if($hasNavbar)
        <x-navbar :navigations="$navigations" />
        @endif
        <x-toaster />
        <div {{ $attributes->twMerge('flex-grow p-10') }} >
            {{ $slot }}
        </div>
        @if($hasFooter)
        <x-footer :logos="$logos" :grants="$grants" />
        @endif
        @stack('js-scripts')
    </div>
</body>

</html>
