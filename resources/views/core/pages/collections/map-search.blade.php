@props(['hasNavbar' => false])
@pushOnce('js-scripts')
<script type="text/javascript">
    document.addEventListener('mapIntialized', function (e) {
        let map = window.maps['map'];

        L.marker([34, -112]).addTo(map);

        // Need to store leaflet feature groups in different structure
    });

    class MapDataToggle {
        //
    }
</script>
@endPushOnce
<x-layout :hasHeader="false" :hasFooter="false" :hasNavbar="$hasNavbar" class="p-0 relative" x-data="{ loading: false }">
    {{-- Menu --}}
    <div class="absolute w-60 h-screen bg-white z-[600]">
        <x-button @click="loading = true; setTimeout(() => loading = false, 500)">
        Can Still interact
        </x-button>
    </div>

    {{-- Loader --}}
    <div x-show="loading" class="absolute flex z-[500] w-screen h-screen bg-black bg-opacity-75 justify-center items-center">
        <div class="stroke-accent w-fit h-44 flex ">
            <x-icons.loading />
        </div>
    </div>

    {{-- Leaflet Map --}}
    <x-map :hasNavbar="false" />
</x-layout>
