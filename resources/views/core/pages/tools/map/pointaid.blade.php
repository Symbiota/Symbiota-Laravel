@pushOnce('js-scripts')
<script type="text/javascript">
    function enableEdit() {
        document.querySelector(".leaflet-draw-edit-edit").click();
    }

    function getFormLatLng() {
        const lat_input = document.getElementById('pointlat')
        const lng_input = document.getElementById('pointlong')

        if (!lat_input || !lng_input || !lat_input.value || !lng_input.value) {
            return [];
        }

        return [lat_input.value, lng_input.value];
    }

    function getErrorRadius() {
        const error_radius_elem = document.getElementById('error_radius');
        const error_radius = error_radius_elem ? error_radius_elem.value : false;

        if(!isNaN(parseFloat(error_radius)) && isFinite(error_radius)) {
            return parseFloat(error_radius);
        } else {
            return false;
        }
    }

    function setLatLngForm(lat, lng) {
        const lat_input = document.getElementById('pointlat')
        const lng_input = document.getElementById('pointlong')

        if (lat_input && lng_input && lat && lng) {
            lat_input.value = lat;
            lng_input.value = lng;
        }
    }

    function loadParentForm() {
        try {
            const lat_input = document.getElementById('pointlat')
            const lng_input = document.getElementById('pointlong')

            const parent_lat_input = opener.document.getElementById('pointlat')
            const parent_lng_input = opener.document.getElementById('pointlong')

            if (lat_input && lng_input && parent_lng_input && parent_lat_input) {
                parent_lat_input.value = lat_input.value;
                parent_lng_input.value = lng_input.value;
            }
        } catch (e) {
            console.warn('Parent Form Values could not be loaded')
        }
    }

    function add_marker(latlng) {
        let map = window.maps['map'];

        if (!latlng) {
            return false;
        }

        const error_radius = getErrorRadius();

        if (error_radius) {
            const circ = L.circle(latlng, error_radius).on('click', () => {
                if (!map.delete_on) enableEdit();
            }).addTo(map.symb_draw_items);

            map.fitBounds(circ.getBounds());
        } else {
            let marker = L.marker(latlng)
            .on('click', () => {
                if (!map.delete_on) enableEdit();
            }).addTo(map.symb_draw_items)

            map.setView(latlng, map.getZoom());
        }
    }

    document.addEventListener('mapIntialized', function (e) {
        // Setup Map
        let map = window.maps['map'];
        addDrawControls(map, {
            rectangle: false,
            circle: false,
            marker: true,
            polygon: false,
            polyline: false,
        });

        map.delete_on = false;
        map.on(L.Draw.Event.CREATED, function (e) {
            var type = e.layerType,
                layer = e.layer;
            layer.layerType = type;
            map.symb_draw_items.clearLayers();

            if(layer.getLatLng) {
                const pos = layer.getLatLng();
                add_marker(pos)
                setLatLngForm(pos.lat, pos.lng);
            }
        });

        map.on(L.Draw.Event.DELETESTART, () => map.delete_on = true)
        map.on(L.Draw.Event.DELETESTOP, () => map.delete_on = false)

        map.on(L.Draw.Event.EDITED, function (e) {
            var layers = e.layers;
            layers.eachLayer(function (layer) {
                if(layer.getLatLng) {
                    const pos = layer.getLatLng();
                    setLatLngForm(pos.lat, pos.lng);
                }
                if(layer.getRadius) {
                    const error_radius = layer.getRadius()
                    document.getElementById('error_radius').value = error_radius;
                }
            });
        });

        document.getElementById('error_radius').addEventListener('change', e => {
            const latlng = getFormLatLng();

            if(latlng) {
                map.symb_draw_items.clearLayers();
                add_marker(latlng);
            }
        });

        // Init Data
        loadParentForm();

        const default_latlng = getFormLatLng();

        if (default_latlng && default_latlng.length) {
            add_marker(default_latlng);
        }
    })
</script>
@endPushOnce
<x-layout class="p-0" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="p-4">
        <div class="flex items-center gap-4 mb-4">
            <p>Click once to capture coordinates. Click on the Submit button to transfer Coordinates. Enter
                uncertainty to create an error radius circle around the marker</p>
            <x-button onclick="self.close()">Save</x-button>
        </div>

        <div class="flex items-center gap-4">
            <x-input label="Latitude" id="pointlat" />
            <x-input label="Longitude" id="pointlong" />
            <x-input label="Uncertainty in Meters" id="error_radius" />
        </div>
    </div>

    <x-map id="map" />
</x-layout>
