@php
$navigations = [
    ["title" => __("header.home"), "link" => '/'],
    ["title" => __("header.collections"), "link" => config('portal.name') .'/collections/index.php'],
    ["title" => __("header.map_search"), "link" => config('portal.name') . '/collections/map'],
    ["title" => __("header.species_checklists"), "link" => config('portal.name') . '/checklists'],
    ["title" => __("header.images"), "link" => config('portal.name') . '/imagelib/search.php'],
    ["title" => __("header.data_use"), "link" => config('portal.name') . '/imagelib/search.php'],
    ["title" => __("header.symbiota_help"), "link" => 'https =>//biokic.github.io/symbiota-docs/'],
    ["title" => __("header.sitemap"), "link" => config('portal.name') . 'sitemap.php'],
];
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/js/app.js', env('VITE_CSS_TARGET', 'resources/css/app.css')])
  </head>
  <body>
    <x-header>
        <x-navbar :navigations="$navigations"/>
    </x-header>
    {{ $slot }}
    <x-footer/>
  </body>
</html>
