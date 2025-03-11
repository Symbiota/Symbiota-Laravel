@pushOnce('js-scripts')
<script type="text/javascript">
    function activateMode(mode) {
        if(!mode) return;
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
                console.warn(mode + ' Is not a support coordinate helper mode choose(rectangle, circle, or polygon, marker)')
                break;
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

        addDrawControls(map, draw_options)
        activateMode(mode);
    });
</script>
@endPushOnce
<x-layout class="p-0" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="p-4 flex items-center gap-4">
        <p>Click map to start drawing or select from the shape controls to draw bounds of that shape</p>
        <x-button>Save and Close</x-button>
    </div>
    <x-map id="map" />
</x-layout>
