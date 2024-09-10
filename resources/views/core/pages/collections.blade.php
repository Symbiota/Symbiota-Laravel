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
            <x-button class="w-full justify-center" onclick="toggle_all_accordions()" >
                Expand All Sections
            </x-button>
            <x-accordion label='Taxonomy'>
                <div class="grid grid-cols-2">
                    <x-input class="grid-span-1" label="Taxon" id="taxon" />
                    <select class="grid-span-1">
                        <option id="taxontype-scientific" value="2">Scientific Name</option>
                        <option id="taxontype-family" value="3">Family</option>
                        <option id="taxontype-group" value="4">Taxanomic Group</option>
                        <option id="taxontype-common" value="5">Common name</option>
                    </select>
                    <x-checkbox class="grid-span-2" id="usethes" label="Includes Synonyms" value="" />
                </div>
            </x-accordion>
            <x-accordion label='Locality'>
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Country" id="country" />
                    <x-input label="Locality/Localities" id="locality" />

                    <x-input label="Minimum Elevation" id="elevhigh" />
                    <x-input label="Maximum Elevation" id="elevlow" />

                    <x-input label="State" id="state" />
                    <x-input label="County" id="county" />
                </div>
            </x-accordion>
            <x-accordion id="lat-long-accordion" label='Latitude & Longitude'>
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
            <x-accordion label='Collection Event'>
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Collection Start Date" id="eventdate1" />
                    <x-input label="Collection End Date" id="eventdate2" />
                    <x-input label="Collector's Last Name" id="collector" />
                    <x-input label="Collector's Number" id="collnum" />
                </div>
            </x-accordion>
            <x-accordion label='Sample Properties'>
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
            <x-accordion label='Collections'>
                TODO Taxonomy Form
            </x-accordion>
        </div>

        <div class="col-span-1 px-4 flex flex-col gap-4">
            <x-radio label='Results Display' name="result-type" :options="[
                    ['label' => 'List', 'value' => 'list'],
                    ['label' => 'Table', 'value' => 'Table']
                ]" />
            <x-button class="w-full justify-center">Search</x-button>
            <x-button class="w-full justify-center" variant="neutral">Reset</x-button>
            <h3 class="text-3xl font-bold text-primary">Criteria</h3>
        </div>
    </div>
</x-layout>
