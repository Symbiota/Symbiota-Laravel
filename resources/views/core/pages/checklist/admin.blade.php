<x-layout>
    <x-horizontal-nav.container default_active_tab="Non-Vouchered Taxa" :items="[
        ['label' => 'Admin', 'icon' => 'fa-solid fa-user'],
        ['label' => 'Description', 'icon' => 'fa-solid fa-list'],
        ['label' => 'Related Checklists', 'icon' => 'fa-solid fa-jar'],
        ['label' => 'Add Image Voucher', 'icon' => 'fa-solid fa-database'],
        ['label' => 'Non-Vouchered Taxa', 'icon' => 'fa-solid fa-database'],
        ['label' => 'Missing Taxa', 'icon' => 'fa-solid fa-database'],
        ['label' => 'Reports', 'icon' => 'fa-solid fa-database'],
    ]">
        {{-- ADMIN START--}}
        <x-horizontal-nav.tab name="Admin" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex">
                    <span class="font-bold text-2xl">
                        Current Editors
                    </span>

                    <span class="flex flex-grow justify-end">
                        <x-button>Add Editor</x-button>
                    </span>
                </div>
                <hr />

                <div>
                    @foreach (['Example Editor'] as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                    Inventory Project Assignments
                </div>
                <hr />
            </div>

            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                    Permanently Remove Checklist
                </div>
                <hr />
                <p>
                    Before a checklist can be deleted, all editors (except yourself) and inventory project assignments
                    must be removed. Inventory project assignments can only be removed by active managers of the project
                    or a system administrator.
                </p>
                <p class="font-bold text-lg text-warning">WARNING: Action cannot be undone</p>
                <x-button disabled>
                    Delete Checklist
                </x-button>
            </div>
        </x-horizontal-nav.tab>
        {{-- ADMIN END --}}

        {{-- DESCRIPTION START--}}
        <x-horizontal-nav.tab name="Description">
            <div class="font-bold text-2xl mb-2">
                Edit Checklist Details
            </div>
            <hr class="mb-2" />
            <form class="flex flex-col gap-2">
                <x-input label="Checklist Name" id="checklist_name" />
                <x-input label="Authors" id="checklist_authors" />

                <x-input label="External Project ID" id="external_project_id" />
                <x-input label="Locality" id="checklist_locality" />
                <x-input label="Citation" id="checklist_citation" />
                <x-input area label="Abstract" id="Abstract" />

                <x-input label="Notes" id="checklist_notes" />

                <x-select label="More Inclusive Reference Checklist" />

                <x-input label="Latitude" id="checklist_latitude" />
                <x-input label="Longitude" id="checklist_longitude" />
                <x-input label="Point Radius" id="checklist_point_radius" />

                <div>
                    <x-input area label="Polygon Footprint" id="footprintwkt" />
                    <x-button @click="openWindow('{{ url('tools/map/coordaid') }}?strict=1&mode=polygon')">
                        Polygon Tool
                    </x-button>
                </div>

                <div class="flex flex-col gap-2">
                    <x-checkbox label="Display Synonyms" />
                    <x-checkbox label="Common Names" />
                    <x-checkbox label="Display as images" />
                    <x-checkbox label="Use voucher images as the preferred image" />
                    <x-checkbox label="Show Details" />
                    <x-checkbox label="Notes & Vouchers" />
                    <x-checkbox label="Taxon Authors" />
                    <x-checkbox label="Show Alphabetically" />
                    <x-checkbox label="Show subgeneric ranking within scientific name" />
                    <x-checkbox label="Activate Identification Key" />
                </div>

                <x-input label="Default Sort Sequence" id="sort_sequence" type="number" />

                <x-select class="w-64" label="Access" default="0" :items="[
                            [ 'title' => 'Private', 'value' => 'private', 'disabled' => false],
                            [ 'title' => 'Can view with link', 'value' => 'view_with_link', 'disabled' => false],
                            [ 'title' => 'Public', 'value' => 'public', 'disabled' => false],
                        ]" />

                <x-button type="submit">Save Edits</x-button>
            </form>
        </x-horizontal-nav.tab>
        {{-- DESCRIPTION END --}}

        {{-- RELATED CHECKLISTS START--}}
        <x-horizontal-nav.tab name="Related Checklists">
            <div class="flex">
                <span class="font-bold text-2xl">
                   Children Checklists
                </span>

                <span class="flex flex-grow justify-end">
                    <x-button>Add Checklist</x-button>
                </span>
            </div>
            <hr/>

            <p>
            There are no Children checklists
            </p>

            <div class="font-bold text-2xl">
                Parent Checklists
            </div>
            <hr/>
            <p>
            There are no Parent checklists
            </p>

            <div class="font-bold text-2xl">
               Batch Parse Species List
            </div>
            <hr/>
            <p>Use the following tool to parse a list into multiple children checklists based on taxonomic nodes (Liliopsida, Eudicots, Pinopsida, etc)</p>
            <form>
                <x-input id="sciname" label="Sci Name"/>
                <x-input id="taxonomic_id" label="Taxonomic id"/>
                <x-select label="Target Checklist" :items="[]" />
                <x-radio id="transfer_method" label="Transfer method" :items="[]" />

                <x-select label="Parent Checklist" :items="[]" />

                <x-select label="Add to project" :items="[]" />
                <x-checkbox label="Copy over permissions and general attributes"/>
                <x-button>Parse Checklist</x-button>
                <x-link target="_blank" href="{{ url(config('portal.name') . '/taxa/taxonomy/taxonomydisplay.php') }}">Open Taxonomic Thesaurus Explorer</x-link>
            </form>
        </x-horizontal-nav.tab>
        {{-- RELATED CHECKLISTS END --}}

        {{-- ADD IMAGE VOUCHER START--}}
        <x-horizontal-nav.tab name="Add Image Voucher">
            <div class="font-bold text-2xl">
              Add Image Voucher and Link to Checklist
            </div>
            <hr/>
            <p>This form will allow you to add an image voucher linked to this checklist. If not already present, Scientific name will be added to checklist.</p>
            <form>
                <x-select label="Voucher Project" :items="[]" />
                <x-button>Add Image Voucher and Link to Checklist</x-button>
            </form>
        </x-horizontal-nav.tab>
        {{-- ADD IMAGE VOUCHER END --}}

        {{-- NON-VOUCHERED TAXA START--}}
        <x-horizontal-nav.tab name="Non-Vouchered Taxa">
            <div class="font-bold text-2xl">
              Taxa without Vouchers: 0 <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
            </div>
            <hr/>
            <p> Listed below are species from the checklist that do not have linked specimen vouchers. Click on name to use the search statement above to dynamically query the occurrence dataset for possible voucher specimens. Use the pulldown to the right to display the specimens in a table format. </p>
            <x-select label="Display Mode"/>

            <div class="font-bold text-xl">
                All Taxa Contain Voucher Links
            </div>
        </x-horizontal-nav.tab>
        {{-- NON-VOUCHERED TAXA END --}}

        {{-- MISSING TAXA START--}}
        <x-horizontal-nav.tab name="Missing Taxa">
        </x-horizontal-nav.tab>
        {{-- MISSING TAXA END --}}

        {{-- REPORTS START--}}
        <x-horizontal-nav.tab name="Reports">
        </x-horizontal-nav.tab>
        {{-- REPORTS END --}}
    </x-horizontal-nav.container>
</x-layout>
