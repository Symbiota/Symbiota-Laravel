@pushOnce('js-scripts')
<script type="text/javascript">
    function activateMode(mode) {
        if (!mode) return;
        switch (mode) {
            case "polygon":
                document.querySelector(".leaflet-draw-draw-polygon").click();
                break;
            case "rectangle":
                document.querySelector(".leaflet-draw-draw-rectangle").click();
                break;
            case "marker":
                document.querySelector(".leaflet-draw-draw-marker").click();
                break;
            case "circle":
                document.querySelector(".leaflet-draw-draw-circle").click();
                break;
            default:
                console.warn(mode + ' Is not a support coordinate helper mode choose(rectangle, circle, or polygon')
                break;
        }
    }

    const MILEStoKM = 1.60934;
    const KMtoM = 1000;
    const SIG_FIGS = 6;

    const coordaid_fields = [
        "footprintwkt",
        "upperlat",
        "bottomlat",
        "leftlong",
        "rightlong",
        "radiusunits",
        "radius",
        "pointlat",
        "pointlng"
    ];

    function parse_layer(layer) {
        switch (layer.layerType) {
            case "polygon":
                return {
                    //TODO (Logan) change this to more agnostic term. Allow for wkt or geosjon?
                    footprintwkt: JSON.stringify(layer.toGeoJSON())
                }
            case "rectangle":
                let rect = {}
                const upperLat = layer._bounds._northEast.lat;
                const rightlng = layer._bounds._northEast.lng;

                const bottomlat = layer._bounds._southWest.lat;
                const leftlng = layer._bounds._southWest.lng;

                return {
                    upperlat_NS: upperLat > 0 ? "N" : "S",
                    upperlat: Math.abs(upperLat).toFixed(SIG_FIGS),

                    bottomlat_NS: bottomlat > 0 ? "N" : "S",
                    bottomlat: Math.abs(bottomlat).toFixed(SIG_FIGS),

                    leftlong_EW: leftlng > 0 ? "E" : "W",
                    leftlong: Math.abs(leftlng).toFixed(SIG_FIGS),

                    rightlong_EW: rightlng > 0 ? "E" : "W",
                    rightlong: Math.abs(rightlng).toFixed(SIG_FIGS),
                };
            case "circle":
                const radius = layer._mRadius;
                const center_lat = layer._latlng.lat;
                const center_lng = layer._latlng.lng;
                return {
                    radius: ((isNaN(radius) ? radius : Math.abs(radius)) / KMtoM).toFixed(SIG_FIGS),
                    radiusunits: "km",
                    pointlat_NS: center_lat > 0 ? "N" : "S",
                    pointlat: Math.abs(center_lat).toFixed(SIG_FIGS),
                    pointlong_EW: center_lng > 0 ? "E" : "W",
                    pointlong: Math.abs(center_lng).toFixed(SIG_FIGS),
                }
        }
    }

    function clearForm() {
        for (let id of coordaid_fields) {
            const elem = opener.document.getElementById(id);
            if(elem) {
                elem.value = "";
				var event = new Event('change');
				elem.dispatchEvent(event);
            }
        }
    }

    function saveToOpener(layer) {
        // Parse and save data
        const obj = parse_layer(layer);
        if (obj) {
            for (let key of Object.keys(obj)) {
                var elem = opener.document.getElementById(key);
                if (elem) {
                    elem.value = obj[key];
                    var event = new Event('change');
                    elem.dispatchEvent(event);
                }
            }
        }
    }

    function loadShape(map, mapMode) {
        const style = {
            color: '#000000',
            opacity: 0.85,
            fillOpacity: 0.55
        }
        switch(mapMode) {
            case "polygon":
                const geoJsonStr = opener.document.getElementById('footprintwkt')?.value
                if(geoJsonStr) {
                    const geoJSON = L.geoJSON(JSON.parse(geoJsonStr))
                    for(let layer_id of Object.keys(geoJSON._layers)) {
                       for(let polygon of geoJSON._layers[layer_id]._latlngs) {
                          L.polygon(polygon).addTo(map.symb_draw_items)
                       }
                    }
                }
                return true;
            case "rectangle":
                const rec = {}

                for(let id of ['upperlat', 'upperlat_NS', 'bottomlat','bottomlat_NS', 'leftlong','leftlong_EW','rightlong','rightlong_EW']) {
                    const elem = opener.document.getElementById(id);
                    if(!elem || !elem.value) {
                        return false;
                    }

                    rec[id] = elem.value;
                }

                L.rectangle([
                    [
                        rec.upperlat * (rec.upperlat_NS === "N"? 1: -1),
                        rec.rightlong * (rec.rightlong_EW === "E"? 1: -1)
                    ],
                    [
                        rec.bottomlat * (rec.bottomlat_NS === "N"? 1: -1),
                        rec.leftlong * (rec.leftlong_EW === "E"? 1: -1)
                    ]
                ]).addTo(map.symb_draw_items);
                return true;
            case "circle":
                const circ = {};
                for(let id of ['radius', 'pointlat', 'pointlong','radiusunits', 'pointlat_NS', 'pointlong_EW']) {
                    const elem = opener.document.getElementById(id);
                    if(!elem || !elem.value) {
                        console.log(id, elem, elem.value)
                        return false;
                    }
                    circ[id] = elem.value;
                }


                L.circle([
                    circ.pointlat * (circ.pointlat_NS === "N"? 1: -1),
                    circ.pointlong * (circ.pointlong_EW === "E"? 1: -1)
                ], (circ.radiusunits === "km" ? circ.radius: circ.radius * MILEStoKM) * KMtoM)
                .addTo(map.symb_draw_items);

                return false;
            default:
                return false;
        }
    }

    document.addEventListener('mapIntialized', function (e) {
        let map = window.maps['map'];
        let params = new URLSearchParams(window.location.search);
        let draw_options = {}

        const mode = params.get('mode');

        if (mode) {
            if (params.get('strict')) {
                draw_options.strict = true;
                draw_options = {
                    rectangle: false,
                    circle: false,
                    marker: false,
                    polygon: false,
                    polyline: false,
                }
            }

            draw_options[mode] = true;
        }

        addDrawControls(map, draw_options);
        map.on(L.Draw.Event.CREATED, function (e) {
            var type = e.layerType,
                layer = e.layer;
            layer.layerType = type;
            map.symb_draw_items.clearLayers();
            map.symb_draw_items.addLayer(layer);
        });

        loadShape(map, mode);

        window.addEventListener("beforeunload",() => {
            //Reset Parent Form
            clearForm();

            if (map.symb_draw_items && map.symb_draw_items._layers) {
                const layers = Object.values(map.symb_draw_items._layers)
                if (Array.isArray(layers) && layers.length > 0) {
                    saveToOpener(layers[0]);
                }
            }
            const leaflet_save = document.querySelector(".leaflet-draw-actions li a[title='Save changes']");
            if(leaflet_save) leaflet_save.click();
        });

        activateMode(mode);
    });
</script>
@endPushOnce
<x-layout class="p-0" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="p-4 flex items-center gap-4">
        <p>Click map to start drawing or select from the shape controls to draw bounds of that shape</p>
        <x-button onclick="self.close()">Save and Close</x-button>
    </div>
    <x-map id="map" />
</x-layout>
