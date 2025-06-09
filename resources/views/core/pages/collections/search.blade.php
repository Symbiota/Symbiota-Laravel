@props(['lang', 'collections'])
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    function toggle_all_accordions() {
        const node_list = document.querySelectorAll('[data-blade-accordion]');
        for (let accordion of node_list) {
            accordion._x_dataStack[0].open = !accordion._x_dataStack[0].open;
        }
    }

    function saveSearchToSession(form) {
        const formData = new FormData(event.target);
        let searchJson = {};

        for(let data of formData.entries()) {
            searchJson[data[0]] = data[1];
        }
        window.sessionStorage.collectionSearch = JSON.stringify(searchJson);
    }
</script>
@endPushOnce
@php
function getCoordAidLink($mode) {
    return url('/tools/map/coordaid') . '?mode=' . $mode;
}
$northSouth = [
['title' => 'N', 'value' => 'N', 'disabled' => false],
['title' => 'S', 'value' => 'S', 'disabled' => false]
];

$eastWest= [
['title' => 'W', 'value' => 'W', 'disabled' => false],
['title' => 'E', 'value' => 'E', 'disabled' => false]
];
@endphp
<x-layout class="p-10">
    <h1 class="text-5xl font-bold text-primary mb-8">Record Search</h1>
    <form
        hx-get="{{ url('/collections/list') }}"
        hx-target="body"
        hx-push-url="true"
        x-on:change="addChip(values, event)"
        onsubmit="saveSearchToSession()"
        id="search-form"
        hx-boost
        class="grid grid-cols-4"
        x-init="
            const session = JSON.parse(window.sessionStorage.collectionSearch)
            for(let id of Object.keys(session)) {
                const elem = document.getElementById(id);
                if(elem) {
                    elem.value = session[id];
                    addChip(values, { target: elem });
                };
            }
        "
        x-data="{
            show_all: false,
            toggle: () => show_all = true,
            values: [],
            removeChip: (values, id) => {
                const el = document.getElementById(id);
                const idx = values.map(v => v.id).indexOf(id);
                if(idx >= 0) {
                    values = values.splice(idx, 1);
                }

                if(el) {
                    if(el.type === 'checkbox') {
                        el.checked = false;
                    } else {
                        el.value = ''
                    }
                }
            },
            addChip: (values, e) => {
                const checkbox = e.target.checked;
                const text = e.target.type !== 'checkbox' && e.target.value;

                if(text || checkbox) {
                    const value = {
                        id: e.target.id,
                        title: e.target.name,
                        value: e.target.value
                    };

                    const idx = values.map(v => v.id).indexOf(value.id);
                    if(idx >= 0) {
                        values = values.splice(idx, 1, value);
                    } else {
                        values.push(value);
                    }
                } else {
                    $data.removeChip(values, e.target.id);
                }
            }
        }"
    >
        <div class="col-span-3 flex flex-col gap-4">
            <x-button type="button" class="w-full justify-center uppercase" onclick="toggle_all_accordions()">
                Expand All Sections
            </x-button>
            <x-accordion label='TAXONOMY' variant="clear-primary" :open="true">
                <x-taxa-search id="taxa" />
            </x-accordion>
            <x-accordion label='LOCALITY' variant="clear-primary">
                <div class="grid grid-cols-2 gap-4">
                    <x-autocomplete-input
                        request_config='{"alias": { "country": "geoterm" }}'
                        name="country"
                        vals='{"geolevel": 50}'
                        include=""
                        label="Country"
                        id="country"
                        search="/api/geographic/search">
                    </x-autocomplete-input>
                    <x-input label="Locality/Localities" id="locality" />

                    <x-input label="Minimum Elevation" id="elevhigh" />
                    <x-input label="Maximum Elevation" id="elevlow" />

                    <x-autocomplete-input
                        request_config='{"alias": {"stateProvince": "geoterm", "country": "parent"}}'
                        vals='{"geolevel": 60}'
                        include='#country'
                        name="stateProvince"
                        label="State/Province"
                        id="stateProvince"
                        search="/api/geographic/search">
                    </x-autocomplete-input>
                    <x-autocomplete-input
                        request_config='{"alias": {"county": "geoterm", "stateProvince": "parent"}}'
                        vals='{"geolevel": 70}'
                        include='#stateProvince'
                        name="county"
                        label="County"
                        id="county"
                        search="/api/geographic/search">
                    </x-autocomplete-input>
                </div>
            </x-accordion>
            <x-accordion id="lat-long-accordion" class:body="p-0" label='LATITUDE & LONGITUDE' variant="clear-primary">
                <x-tabs :tabs="['Bounding Box', 'Polygon', 'Point Radius']" class:body="border-x-0 border-b-0">
                    <div>
                        <x-button type="button" class="text-base w-full"
                            onclick="openWindow('{{ getCoordAidLink('rectangle') }}', 'Rectangle')">
                            Map Bounding Box
                        </x-button>
                        <div class="flex items-end gap-1 pt-1">
                            <x-input id="upperlat" label="Minimum Elevation" />
                            <x-select :items="$northSouth" id="upperlat_NS"/>
                        </div>

                        <div class="flex items-end gap-1">
                            <x-input id="bottomlat" label="Southern Latitude" />
                            <x-select :items="$northSouth" id="bottomlat_NS"/>
                        </div>
                        <div class="flex items-end gap-1">
                            <x-input id="leftlong" label="Western Longitude" />
                            <x-select :items="$eastWest" id="leftlong_EW"/>
                        </div>

                        <div class="flex items-end gap-1">
                            <x-input id="rightlong" label="Eastern Longitude" />
                            <x-select :items="$eastWest" id="rightlong_EW"/>
                        </div>
                    </div>
                    <div>
                        <x-button type="button" class="text-base w-full"
                            onclick="openWindow('{{ getCoordAidLink('polygon') }}', 'Polygon')">
                            Map Polygon
                        </x-button>
                        {{-- id="polygonwkt" (May need to change with geojson changes)--}}
                        <x-input id="footprintwkt" label="Polygon" :area="true" rows="4" />
                    </div>
                    <div>
                        <x-button type="button" class="text-base w-full"
                            onclick="openWindow('{{ getCoordAidLink('circle') }}', 'Circle')">
                            Map Point Radius
                        </x-button>
                        <div class="flex items-end gap-1">
                            <x-input id="pointlat" label="Longitude" />
                            <x-select :items="$northSouth" id="pointlat_NS"/>
                        </div>
                        <div class="flex items-end gap-1">
                            <x-input id="pointlong" label="Latitude" />
                            <x-select :items="$eastWest" id="pointlong_EW"/>
                        </div>
                        <div class="flex items-end gap-1">
                            <x-input id="radius" label="Radius" />
                            <x-select id="radiusunits" :items="[
                            ['title' => 'Kilometers', 'value' => 'km', 'disabled' => false],
                            ['title' => 'Miles', 'value' => 'mi', 'disabled' => false]
                        ]" />
                        </div>
                    </div>
                </x-tabs>
            </x-accordion>
            <x-accordion label='COLLECTION EVENT' variant="clear-primary">
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Collection Start Date" id="eventDate1" />
                    <x-input label="Collection End Date" id="eventDate2" />
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
                <div class="flex flex-col gap-4">
                <x-nested-checkbox-group id="collections-group" label="All Collections">
                @foreach ($collections as $collection)
                    @php
                        $collIds = request('collId');
                        if(!is_array($collIds)) $collIds = [ $collIds ];
                    @endphp
                    <span class="inline-flex items-center gap-2">
                        <x-checkbox name="collid[]" :value="$collection->collID" :checked="in_array($collection->collID, $collIds)" :label="$collection->collectionName"/>
                        <x-link class="text-sm" href="{{ url('collections/' . $collection->collID) }}">See More</x-link>
                    </span>
                @endforeach
                </x-nested-checkbox-group>
                </div>
            </x-accordion>
        </div>

        <div class="col-span-1 px-4 flex flex-col gap-4">
            <x-radio
                onchange="htmx.find('#search-form').setAttribute('hx-get', `{{ url('collections') }}/${event.target.value}`); htmx.process('#search-form')"
                label='Results Display'
                default_value="list"
                name="result-type"
                :options="[
                    ['label' => 'List', 'value' => 'list'],
                    ['label' => 'Table', 'value' => 'table']
                ]" />
            <x-button type="submit" class="w-full justify-center text-base">Search</x-button>
            <x-button type="button" class="w-full justify-center text-base" variant="neutral">Reset</x-button>
            <h3 class="text-3xl font-bold text-primary">Criteria</h3>
            <div id="chips" class="grid grid-cols-1 gap-4">
                <template x-for="value in values">
                    <div class="bg-base-100 rounded-md border border-base-300">
                        <div class="bg-base-300 px-2 py-1 rounded-t-md border-b border-base-300 rounded-b-0 font-bold flex items-center">
                            <div x-text="value.title"></div>
                            <div class="grow">
                                <x-button @click="removeChip(values, value.id)" type="button" class="ml-auto rounded-full h-6 w-6 p-0" variant="neutral">
                                    <i class="mx-auto cursor-pointer fa-solid fa-xmark"></i>
                                </x-button>
                            </div>
                        </div>
                        <div class="px-2 py-1" x-text="value.value"></div>
                    </div>
                </template>
            </div>
        </div>
    </form>
</x-layout>
