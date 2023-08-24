@php
$navigations = [
    ["title" => __("header.home"), "link" => '/'],
    ["title" => __("header.collections"), "link" => '/collections'],
    ["title" => __("header.map_search"), "link" => '/collections/map'],
    ["title" => __("header.species_checklists"), "link" => '/checklists'],
    ["title" => __("header.images"), "link" => '/imagelib/search.php'],
    ["title" => __("header.data_use"), "link" => '/imagelib/search.php'],
    ["title" => __("header.symbiota_help"), "link" => 'https =>//biokic.github.io/symbiota-docs/'],
    ["title" => __("header.sitemap"), "link" => 'sitemap.php'],
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
