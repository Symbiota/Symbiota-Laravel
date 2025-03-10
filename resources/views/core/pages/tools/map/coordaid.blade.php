@pushOnce('js-scripts')
<script type="text/javascript">
    document.addEventListener('mapIntialized', function (e) {
        let map = window.maps['map'];
        L.marker([34, -112]).addTo(map);
    });
</script>
@endPushOnce
<x-layout class="p-0" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="p-4">
        <p>TODO (Logan) grab the help text</p>
        <div class="flex items-center gap-2">
            <x-input name="lat" label="lat" />
            <x-input name="lng" label="lng" />
            <x-input name="erradius" label="erradius" />
            <x-button>Save</x-button>
        </div>
    </div>
    <x-map id="map"/>
</x-layout>
