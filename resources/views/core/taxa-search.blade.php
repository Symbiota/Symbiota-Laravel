<div>
    <label class="text-lg" for="test-search-2">Search Taxa</label>
    <div class="flex items-center group">
        <x-select class="rounded-r-none w-48">
            <option value="Any Name">Any Name</option>
            <option value="Scientific Name">Scientific Name</option>
            <option value="Family">Family</option>
            <option value="Taxonomy Group">Taxonomy Group</option>
            <option value="Common">Common</option>
        </x-select>
        <x-autocomplete-input id="test-search-2" placeholder="Type to search..." search="/api/taxa/search">
            <x-slot:input class="peer-input p-1 z-10 bg-base-200 rounded-l-none border-l-0"></x-slot>
                <x-slot:menu></x-slot>
        </x-autocomplete-input>
    </div>
    <x-checkbox class="mt-2" name="usethes" label="Include Synonyms" :default_value="request('usethes')" />
</div>
