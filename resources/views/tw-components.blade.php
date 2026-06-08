<x-layout :hasToaster="true">
    <div class="mx-auto flex w-[90%] max-w-screen-lg flex-col gap-y-4">
        <h1 class="text-primary my-3 font-sans text-4xl font-bold">Tailwind Components</h1>
        <h1 class="font-sans text-4xl font-bold">H1</h1>
        <h2 class="font-sans text-3xl font-bold">H2</h2>
        <h3 class="font-sans text-2xl font-bold">H3</h3>
        <h4 class="font-sans text-lg font-bold">Subheading</h4>
        <p class="font-sans">Paragraph</p>
        <span class="font-sans text-sm">Small Text</span>

        <div class="flex items-center gap-x-4">
            <div class="bg-primary w-32 rounded-md">
                <x-brand />
            </div>
            <x-swatch />
        </div>
        <div class="flex gap-x-4">
            <x-button variant="primary">Primary</x-button>
            <x-button variant="secondary">Secondary</x-button>
            <x-button variant="neutral">Neutral</x-button>
            <x-button variant="accent">Accent</x-button>
        </div>

        {{-- Note you need to be in alpine context to use @click otherwise just use onclick --}}
        {{-- Note you must enable the toaster within the layout for any of these to function --}}
        <div class="flex gap-4">
            <x-button onclick="toast('Success Title', { description: 'Plain description' })"> Toast Plain </x-button>
            <x-button onclick="toast('Success Title', { type: 'success', description: 'Success description' })">
                Toast Success
            </x-button>
            <x-button onclick="toast('Error Title', { type: 'danger', description: 'Error description' })">
                Toast Error
            </x-button>
            <x-button onclick="toast('Error Title', { type: 'info', description: 'Info description' })">
                Toast Info
            </x-button>

            {{-- Example of working alpine context @click with toast --}}
            <x-button
                x-init=""
                @click="toast('Warning Title', { type: 'warning', description: 'Warning description' })"
            >
                Toast Warning
            </x-button>
        </div>

        <x-input required :id="'input'" :label="'Text Input'" />
        <x-input required type="number" :id="'input'" :label="'Number Input'" />
        <x-radio
            :default_value="2"
            :options="[ ['label' => 'Option 1', 'value' => '2'], ['label' => 'Option 2', 'value' => 1]]"
            label="Symb Radio"
            name="radio_options"
        />
        <x-checkbox :id="'checkbox'" :label="'Checkbox'" />
        <x-nested-checkbox-group :id="'nested-checkbox'" :label="'All Options'">
            <x-checkbox :id="'optional-1'" :label="'Optional 1'" />
            <x-checkbox :id="'optional-2'" :label="'Optional 2'" />
        </x-nested-checkbox-group>
        <x-accordion :id="'taxonomy'" :label="'accordian'">
            <x-input required :id="'input'" :label="'Text input'" />
            <x-input required type="number" :id="'input'" :label="'Number Input'" />
        </x-accordion>

        <x-accordion :id="'nested'" :label="'nested accordian'">
            <x-accordion :id="'collections'" :label="'accordian'">
                <x-input required :id="'input'" :label="'Text Input'" />
                <x-input required type="number" :id="'input'" :label="'Number Input'" />
            </x-accordion>
        </x-accordion>

        <x-autocomplete-input name="taxa" :label="'Auto Complete Input'" id="test-search" search="/api/taxa/search">
            <x-slot:input class="w-full"></x-slot:input>
            <x-slot:menu>
                Menu
            </x-slot:menu>
        </x-autocomplete-input>
        <x-taxa-search />

        @php
        class OccurrenceStub {
        public $sciname = "Pinus albicaulis";
        public $scientificNameAuthorship= "Engelm.";
        public $catalogNumber= "9973";
        public $family= "Pinaceae";
        public $recordedBy= "Lupin Praug";
        public $recordNumber= "1";
        public $eventDate = "1999/10/24";
        public $locality= "United States, California";
        public $institutionCode = "BLMAR";
        public $collectionCode = "";
        public $decimalLatitude= 90;
        public $decimalLongititude = 90;
        public $minimumElevationInMeters = "3322m";
        public $maximumElevationInMeters = "3383m";
        public $tidInterpreted= 1;
        public $occid = 1;
        public $image_cnt = 1;
        public $audio_cnt = 1;
        }
        @endphp
        <x-collections.list.item :occurrence="new OccurrenceStub" />

        <div class="w-fit">
            <x-popover>
                <div class="flex flex-col gap-4">
                    <div class="text-xl">Title</div>
                    <x-input label="Height" id="Something" />
                    <x-input label="Width" id="Other thing" />
                </div>
            </x-popover>
        </div>
        <div class="w-fit">
            <x-breadcrumbs
                :items="[
                ['title' => 'Home', 'href' => '#_'],
                ['title' => 'Collections', 'href' => '#_'],
                ['title' => 'Collection Profile', 'href' => '#_'],
            ]"
            />
        </div>

        <x-context-menu> Right Click Here </x-context-menu>

        <x-modal>
            <x-slot name="button">
                Open Modal
            </x-slot>
            <x-slot name="title" class="text-2xl">
                Title
            </x-slot>
            <x-slot name="body">
                <form class="flex flex-col gap-2">
                    <x-input name="testOne" label="Test 1" />
                    <x-input name="testTwo" label="Test 2" />
                    <x-button type="submit">Submit</x-button>
                </form>
            </x-slot>
        </x-modal>

        <x-tooltip text="Tooltip"> Tooltip </x-tooltip>

        <x-select
            label="Groceries"
            :default="1"
            :items="[
            [
                'title' => 'Milk',
                'value' => 'milk',
                'disabled' => false
            ],
            [
                'title' => 'Eggs',
                'value' => 'eggs',
                'disabled' => false
            ],
            [
                'title' => 'Cheese',
                'value' => 'cheese',
                'disabled' => false
            ],
            [
                'title' => 'Bread',
                'value' => 'bread',
                'disabled' => false
            ],
            [
                'title' => 'Apples',
                'value' => 'apple',
                'disabled' => false
            ],
            [
                'title' => 'Bananas',
                'value' => 'bananas',
                'disabled' => false
            ],
            [
                'title' => 'Yogurt',
                'value' => 'yogurt',
                'disabled' => false
            ],
            [
                'title' => 'Sugar',
                'value' => 'sugar',
                'disabled' => false
            ],
            [
                'title' => 'Salt',
                'value' => 'salt',
                'disabled' => false
            ],
        ]"
        />
        <x-image-card
            src="https://collections.nmnh.si.edu/media/?i=10333969&width=300"
            title="Pinus albicaulis Engelm."
        />
        <x-slide-tab-container :tabs="['Tab 1', 'Tab 2', 'Tab 3']">
            <x-slide-tab class="bg-card text-base-content rounded-lg border p-4 shadow-sm"> Tab 1 </x-slide-tab>

            <x-slide-tab class="bg-card text-base-content rounded-lg border p-4 shadow-sm"> Tab 2 </x-slide-tab>

            <x-slide-tab class="bg-card text-base-content rounded-lg border p-4 shadow-sm"> Tab 3 </x-slide-tab>
        </x-slide-tab-container>
        <x-rich-editor id="rich-editor" label="Rich Text Description">
            {!! Purify::clean('<p><em>Hello</em>, <span style="text-decoration: underline;"><strong>World!</strong></span><script>alert("bad")</script></p>') !!}
        </x-rich-editor>
    </div>
</x-layout>
