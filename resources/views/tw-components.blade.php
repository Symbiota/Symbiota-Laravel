<x-layout>
    <div class="flex flex-col w-[90%] max-w-screen-lg gap-y-4 mx-auto">
        <h1 class="text-3xl my-3 font-bold font-sans text-primary">
            Tailwind Components
        </h1>
        <div class="flex gap-x-4 items-center">
            <div class="bg-primary rounded-md w-32">
                <x-brand />
            </div>
            <x-swatch />
        </div>
        <div class="flex gap-x-4">
            <button class="btn">
                Button 1
            </button>
            <button class="btn bg-secondary hover:bg-primary text-secondary-content hover:text-primary-content">
                Button 2
            </button>
            <button class="btn bg-transparent text-base-content border-secondary border ">
                Button 3
            </button>

        </div>
        <x-input required :id="'input'" :label="'Text Input'" />
        <x-input required type="number" :id="'input'" :label="'Number Input'" />

        <h1 class="text-xl my-2 font-bold font-sans text-error">
           Todo Radio
        </h1>
        <h1 class="text-xl my-2 font-bold font-sans text-error">
           Todo Checkbox
        </h1>
        <h1 class="text-xl my-2 font-bold font-sans text-error">
           Todo Nested Checkbox
        </h1>
        <x-checkbox :id="'checkbox'" :label="'Checkbox'" />
        <x-nested-checkbox-group :id="'nested-checkbox'" :label="'Checkbox'">
            <x-checkbox :id="'nested-1'" :label="'Nested 1'" />
            <x-checkbox :id="'nested-2'" :label="'Nested 2'" />
        </x-nested-checkbox-group>
        <x-accordion :id="'Taxonomy'" :label="'Accordian'">
            <x-input required :id="'input'" :label="'Text Input'" />
            <x-input required type="number" :id="'input'" :label="'Number Input'" />
        </x-accordion>

        <x-accordion :id="'Nested'" :label="'Nested Accordian'">
            <x-accordion :id="'Collections'" :label="'Accordian'">
            <x-input required :id="'input'" :label="'Text Input'" />
            <x-input required type="number" :id="'input'" :label="'Number Input'" />
            </x-accordion>
        </x-accordion>

        <h1 class="text-xl my-2 font-bold font-sans text-error">
           Todo Autocomplete Input
        </h1>
        <h1 class="text-xl my-2 font-bold font-sans text-error">
           Todo Collection Result Card
        </h1>
        <h1 class="text-xl my-2 font-bold font-sans text-error">
           Todo Collection Result Card
        </h1>
    </div>
</x-layout>
