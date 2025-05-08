@php
use Illuminate\Support\Facades\DB;

$select = ['c.latCentroid', 'c.longCentroid', 'c.name', 'c.clid'];
$checklists = DB::table('fmchecklists as c')
    ->when(request('pid'), function($query) use ($select) {
        $select[] = 'pid';
        $query->leftJoin('fmchklstprojlink as pl', 'pl.clid', 'c.clid')
              ->where('pid', request('pid'));
    })
    ->whereNotNull('longCentroid')
    ->where('longCentroid', '!=', 0)
    ->whereNotNull('latCentroid')
    ->where('latCentroid', '!=', 0)
    ->where('access', 'public')
    ->select($select)
    ->get();
@endphp

<x-layout :hasHeader="false" :hasFooter="false" :hasNavbar="false" class="p-0">
    <div id="checklist_data" data-checklists="{{ json_encode($checklists) }}"></div>

    <template id="popup-template">
        <div class="font-bold" id="popup-title"></div>
        <x-link hx-boost="true" id="popup-link" href="{{ url('checklists') }}/">
            See Checklist
        </x-link>
    </template>

    <script>
        document.addEventListener('mapIntialized', function (e) {
            const data_container = document.getElementById('checklist_data');
            let checklist_data = [];

            try {
                let json = data_container.getAttribute('data-checklists');
                checklist_data = JSON.parse(json);
            } catch(error) {
                checklist_data = [];
            }

            const temp = document.getElementById('popup-template');
            const temp_title = temp.content.getElementById('popup-title');
            const temp_link = temp.content.getElementById('popup-link');
            const base_url = temp_link.href;

            let map = window.maps['map'];
            let markers = [];

            for(let checklist of checklist_data) {
                temp_title.innerHTML = checklist.name;
                temp_link.href = base_url + checklist.clid;

                let marker = L.marker([checklist.latCentroid, checklist.longCentroid])
                    .bindTooltip(checklist.name)
                    .bindPopup(temp.innerHTML);

                markers.push(marker);
                marker.addTo(map);
            }

            const markerGroup = L.featureGroup(markers).addTo(map);
            map.fitBounds(markerGroup.getBounds());
        })
    </script>
    <x-map id="map"/>
</x-layout>
