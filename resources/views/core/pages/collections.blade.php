@props(['lang'])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function toggle_all_accordions() {
        const node_list = document.querySelectorAll('[data-blade-accordion]');
        for(let accordion of node_list) {
            accordion._x_dataStack[0].open = !accordion._x_dataStack[0].open;
        }
    }
</script>
@endPushOnce
<x-layout class="p-10">
    <h1 class="text-5xl font-bold text-primary mb-8">Record Search</h1>
    <div class="grid grid-cols-4" x-data="{ show_all: false, toggle: () => show_all = true }">
        <div class="col-span-3 flex flex-col gap-4">
            <x-button class="w-full justify-center uppercase" onclick="toggle_all_accordions()" >
                Expand All Sections
            </x-button>
            <x-accordion label='TAXONOMY' variant="clear-primary">
                <x-taxa-search/>
            </x-accordion>
            <x-accordion label='LOCALITY' variant="clear-primary">
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Country" id="country" />
                    <x-input label="Locality/Localities" id="locality" />

                    <x-input label="Minimum Elevation" id="elevhigh" />
                    <x-input label="Maximum Elevation" id="elevlow" />

                    <x-input label="State" id="state" />
                    <x-input label="County" id="county" />
                </div>
            </x-accordion>
            <x-accordion id="lat-long-accordion" label='LATITUDE & LONGITUDE' variant="clear-primary">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <h3 class="text-xl text-primary font-bold">Bounding Box</h3>
                        <x-button class="text-base w-full">Map Bounding Box</x-button>
                        <x-input id="upperlat" label="Minimum Elevation"/>
                        select N/S
                        <x-input id="bottomlat" label="Southern Latitude"/>
                        select N/S
                        <x-input id="leftlong" label="Western Longitude"/>
                        select W/E
                        <x-input id="rightlong" label="Eastern Longitude"/>
                        select W/E
                    </div>
                    <div>
                        <h3 class="text-xl text-primary font-bold">Polygon</h3>
                        <x-button class="text-base w-full">Map Polygon</x-button>
                        <x-input id="polygonwkt" label="Polygon"/>
                    </div>
                    <div>
                        <h3 class="text-xl text-primary font-bold">Point-Radius</h3>
                        <x-button class="text-base w-full">Map Point Radius</x-button>
                        <x-input id="pointlat" label="Longitude"/>
                        select N/S
                        <x-input id="pointlong" label="Latitude"/>
                        select W/E
                        <x-input id="radius" label="Radius"/>
                        select Units
                    </div>
                </div>
            </x-accordion>
            <x-accordion label='COLLECTION EVENT' variant="clear-primary">
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Collection Start Date" id="eventdate1" />
                    <x-input label="Collection End Date" id="eventdate2" />
                    <x-input label="Collector's Last Name" id="collector" />
                    <x-input label="Collector's Number" id="collnum" />
                </div>
            </x-accordion>
            <x-accordion label='SAMPLE PROPERTIES' variant="clear-primary">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-checkbox label="Include other catalog numbers and GUIDs" checked id="includeothercatnum" />
                        <x-input label="Catalog Nubmer" id="catnum" />
                    </div>
                    <div class="flex flex-col gap-2">

                        <x-checkbox label="Limit to Type Specimens" id="typestatus" />
                        <x-checkbox label="Limit to specimens with images" id="hasimages" />
                        <x-checkbox label="Limit to specimens with audio" id="hasaudio" />
                        <x-checkbox label="Limit to specimens with genetic data" id="hasgenetic" />
                        <x-checkbox label="Limit to specimens with Geocoordinates" id="hascoords" />
                        <x-checkbox label="Include cultivated" id="includecult" />
                    </div>
                </div>
            </x-accordion>
            <x-accordion label='COLLECTIONS' variant="clear-primary">
                TODO Taxonomy Form
            </x-accordion>
        </div>

        <div class="col-span-1 px-4 flex flex-col gap-4">
            <x-radio label='Results Display' default_value="list" name="result-type" :options="[
                    ['label' => 'List', 'value' => 'list'],
                    ['label' => 'Table', 'value' => 'Table']
                ]" />
            <x-button class="w-full justify-center text-base">Search</x-button>
            <x-button class="w-full justify-center text-base" variant="neutral">Reset</x-button>
            <h3 class="text-3xl font-bold text-primary">Criteria</h3>
        </div>
    </div>
</x-layout>
