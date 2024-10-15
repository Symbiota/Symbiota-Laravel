<x-layout>
    <div class="flex flex-col w-[90%] max-w-screen-lg gap-y-4 mx-auto">
        <h1 class="text-4xl my-3 font-bold font-sans text-primary">
            Tailwind Components
        </h1>
        <h1 class="text-4xl font-bold font-sans">H1</h1>
        <h2 class="text-3xl font-bold font-sans">H2</h2>
        <h3 class="text-2xl font-bold font-sans">H3</h3>
        <h4 class="text-lg font-bold font-sans">Subheading</h4>
        <p class="font-sans">Paragraph</p>
        <span class="text-sm font-sans">Small Text</span>

        <div class="flex gap-x-4 items-center">
            <div class="bg-primary rounded-md w-32">
                <x-brand />
            </div>
            <x-swatch />
        </div>
        <div class="flex gap-x-4">
            <x-button variant="primary">Primary</x-button>
            <x-button variant="secondary">Secondary</x-button>
            <x-button variant="neutral">Neutral</x-button>
            <x-button variant="accent">Accent</x-button>
            <x-button variant="neutral">
                <x-slot:icon>
                    <div class="stroke-accent w-7 h-7">
                        <x-icons.loading />
                    </div>
                    </x-slot>
                    Async
            </x-button>
        </div>
        <x-input required :id="'input'" :label="'Text Input'" />
        <x-input required type="number" :id="'input'" :label="'Number Input'" />
        <x-radio :default_value="2"
            :options="[ ['label' => 'Option 1', 'value' => '2'], ['label' => 'Option 2', 'value' => 1]]"
            label="Symb Radio" name="radio_options" />
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
            <x-slot:input class="w-full"></x-slot>
                <x-slot:menu>Menu</x-slot>
        </x-autocomplete-input>
        <x-taxa-search/>

        <x-collections.list.item />

        <div class="w-fit">
            <x-popover>
                <div class="flex flex-col gap-4">

                    <div class="text-xl">Title</div>
                    <x-input label="Height" id="Something"/>
                    <x-input label="Width" id="Other thing"/>
                </div>
            </x-popover>


        </div>

        <x-select label="Groceries" :default="1" :items="[
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
        ]" />
    </div>
</x-layout>
