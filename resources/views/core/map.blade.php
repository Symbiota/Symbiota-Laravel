@props(['hasNavbar' => false])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function initMap() {
        const DEFAULT_MAP_OPTIONS = {
            center: [0, 0],
            zoom: 2,
            minZoom: 2,
        };

        let map = L.map('map', DEFAULT_MAP_OPTIONS);

        const terrainLayer = L.tileLayer('https://{s}.google.com/vt?lyrs=p&x={x}&y={y}&z={z}', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            maxZoom: 20,
            worldCopyJump: true,
            detectRetina: true,
        }).addTo(map);

        const basicLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            displayRetina: true,
            maxZoom: 20,
            noWrap: true,
            tileSize: 256,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        const layers = {
            "Terrain": terrainLayer,
            "Basic": basicLayer,
        };
        L.control.layers(layers).addTo(map)
    }

    document.addEventListener('DOMContentLoaded', function () {
        initMap();
    });
</script>
@endPushOnce

<x-layout :hasHeader="false" :hasFooter="false" :hasNavbar="$hasNavbar" class="p-0">
    @if($hasNavbar)
        {{-- TODO (Logan) figure out how to make this value always reflect nav bar height--}}
        <div id="map" class="w-full h-[calc(100vh_-_56px)]"></div>
    @else
        <div id="map" class="w-full h-[100vh]"></div>
    @endif
</x-layout>
