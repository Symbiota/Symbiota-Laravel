@props(['errors'])
@php
global $LANG;
include_once(legacy_path('/classes/utilities/Language.php'));

Language::load([
	'checklists/dynamicmap',
	'checklists/checklist'
]);

@endphp

<x-margin-layout>
    <x-breadcrumbs :items="[
        ['title' => $LANG['HOME'], 'href' => url('')],
        ['title' => $LANG['DYNAMIC_MAP']],
        ]" />

    <h1 class="sr-only">{{ $LANG['DYNAMIC_MAP'] }}></h1>

    <div class="flex flex-col gap-1" x-data="{'moreDetail': false}">
        <p>
            {{ $LANG['CAPTURE_COORDS'] }}
        <p/>

        <p x-show="moreDetail">
            {{ $LANG['RADIUS_DESCRIPTION'] }}
        <p/>
        <x-button class="cursor-pointer" @click="moreDetail=!moreDetail">
            <span x-show="moreDetail">{{ $LANG['LESS_DETAILS'] }}</span>
            <span x-show="!moreDetail">{{ $LANG['MORE_DETAILS'] }}</span>
        </x-button>
    </div>
    <form method="post" class="flex flex-col gap-4">
        @csrf
        <div class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="interface" value="{{ request('interface') ?? 'checklist' }}" />
            <input type="hidden" name="buildChecklist" value="1" />
            <div class="min-w-20 max-w-40 flex-grow"><x-input id="lat" label="Latitude" value=""/></div>
            <div class="min-w-20 max-w-40 flex-grow"><x-input id="lng" label="Longitude" value=""/></div>
            <div class="min-w-20 max-w-40 flex-grow"><x-input id="radius" label="Radius" type="number" /></div>
            <x-select class="min-w-10 flex-grow" id="radiusunits" label="Units" default="0" :items="[
            ['title' => $LANG['KM'], 'value' => 'km' ],
            ['title' => $LANG['MILES'], 'value' => 'mi' ]
        ]" />
        </div>

        <x-taxa-search class="z-100" label="Taxon Filter" />

        <div>
            <x-button>{{ $LANG['BUILD_CHECKLIST'] }}</x-button>
        </div>

        <x-errors :errors="$errors" />
    </form>

    <script>
        document.addEventListener('mapIntialized', function (e) {
            let map = window.maps['map'];
            let markerGroup = new L.layerGroup().addTo(map);
            let latlng;

            const radiusEl = document.getElementById('radius');
            const radiusUnitsEl = document.getElementById('radiusunits');
            const latEl= document.getElementById('lat');
            const lngEl= document.getElementById('lng');

            function getRadius() {
                if(radiusUnitsEl.value === "km") return radiusEl.value * 1000;
                const MILES_TO_METERS = 1609.344;
                return radiusEl.value * MILES_TO_METERS;
            }

            function drawMarker(lat, lng) {
                //Clear Layers In Between Clicks
                if(markerGroup) markerGroup.clearLayers();

                lat = lat = parseFloat(lat);
                lng = lng = parseFloat(lng);

                if((lat >= -90 || lat <= 90) && (lng >= -180 || lng <= 180)) {
                    latlng = {lat, lng};
                    //Render Marker
                    L.marker(latlng).addTo(markerGroup);
                    let radius = getRadius();
                    //Render Radius if Input
                    if(radius > 0) {
                        let circle = L.circle(latlng, radius)
                            .setStyle(DEFAULT_SHAPE_OPTIONS)
                            .addTo(markerGroup);
                    }

                    map.setView(latlng, map.getZoom());
                }
            }

            radiusEl.addEventListener('input', () => drawMarker(latlng.lat, latlng.lng));
            latEl.addEventListener('change', (e) => drawMarker(e.target.value, lngEl.value));
            lngEl.addEventListener('change', (e) => drawMarker(latEl.value, e.target.value));
            map.on('click', (e) => {
                drawMarker(e.latlng.lat, e.latlng.lng)
                latEl.value = e.latlng.lat;
                lngEl.value = e.latlng.lng;
            });
        })
    </script>
    <x-map class="z-0"/>
</x-margin-layout>
