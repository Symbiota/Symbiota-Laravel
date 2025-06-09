@props(['hasNavbar' => false, 'id' => 'map'])
@pushOnce('js-scripts')
<script type="text/javascript">
    function addDrawControls(map, options, onDrawChange = () => {}) {
        const draw_options = {...DEFAULT_DRAW_OPTIONS, ...options};

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems)
        map.symb_draw_items = drawnItems;

        const shape_options = ['polyline', 'polygon', 'rectangle', 'circle'];

        if(draw_options.drawColor) {
            L.Path.mergeOptions(draw_options.drawColor);

            for (let shape of shape_options) {
                if (draw_options[shape]) {
                    draw_options[shape] = {
                        shapeOptions: draw_options.drawColor
                    }
                }
            }
        }

        var drawControl = new L.Control.Draw({
            position: 'topright',
            draw: draw_options,
            edit: {
                featureGroup: drawnItems,
            }
        });

        map.addControl(drawControl);
    }

    const DEFAULT_SHAPE_OPTIONS = {
        color: '#000000',
        opacity: 0.85,
        fillOpacity: 0.55
    };

    const DEFAULT_DRAW_OPTIONS = {
        polyline: false,
        circle: true,
        rectangle: true,
        polygon: true,
        control: true,
        circlemarker: false,
        marker: false,
        multiDraw: false,
        drawColor: DEFAULT_SHAPE_OPTIONS,
        lang: "en",
    };

    function initMap(id) {
        const DEFAULT_MAP_OPTIONS = {
            center: [0, 0],
            zoom: 2,
            minZoom: 2,
        };

        let map = L.map(id, DEFAULT_MAP_OPTIONS);

        L.Path.mergeOptions(DEFAULT_SHAPE_OPTIONS);

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
        L.control.layers(layers).addTo(map);

        if (!window.maps) {
            window.maps = {
                [id]: map
            }
        } else {
            window.maps[id] = map;
        }

        document.dispatchEvent(new CustomEvent("mapIntialized", {
            detail: {
                map_id: id,
                type: 'leaflet'
            }
        }));
    }
</script>
@endPushOnce

@push('js-scripts')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        initMap("{{ $id }}");
    }, {once: true});
</script>
@endpush

@if($hasNavbar)
{{-- TODO (Logan) figure out how to make this value always reflect nav bar height--}}
<div id="{{ $id }}" class="w-full h-[calc(100vh_-_56px)]"></div>
@else
<div id="{{ $id }}" class="w-full h-[100vh]"></div>
@endif
