@props(['hasNavbar' => false])
@pushOnce('js-scripts')
    <script type="text/javascript">
        document.addEventListener("mapIntialized", function (e) {
            let map = window.maps["map"];

            L.marker([34, -112]).addTo(map);

            // Need to store leaflet feature groups in different structure
        });

        class MapDataToggle {
            //
        }
    </script>
@endPushOnce
<x-layout
    :hasHeader="false"
    :hasFooter="false"
    :hasNavbar="$hasNavbar"
    class="relative p-0"
    x-data="{ loading: false }"
>
    {{-- Menu --}}
    <div class="absolute z-[600] h-screen w-60 bg-white">
        <x-button
            @click="
                loading = true;
                setTimeout(() => (loading = false), 500);
            "
        >
            Can Still interact
        </x-button>
    </div>

    {{-- Loader --}}
    <div x-show="loading" class="absolute z-[500] flex h-screen w-screen items-center justify-center bg-black/70">
        <div class="stroke-accent flex h-44 w-fit">
            <x-icons.loading />
        </div>
    </div>

    {{-- Leaflet Map --}}
    <x-map :hasNavbar="false" />
</x-layout>
