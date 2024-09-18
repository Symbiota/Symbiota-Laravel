@props(['id' => uniqid()])
@push('js-scripts')
<script type="text/javascript">
    document.addEvent
</script>
@endpush
<div>
    <label class="text-lg" for="{{ $id }}">Search Taxa</label>
    <div class="flex items-center group">
        <x-select name="taxa-type" id="taxa-type-{{$id}}" class="rounded-r-none w-48">
            <option value="Any Name">Any Name</option>
            <option value="Scientific Name">Scientific Name</option>
            <option value="Family">Family</option>
            <option value="Taxonomy Group">Taxonomy Group</option>
            <option value="Common">Common</option>
        </x-select>
        <x-autocomplete-input
            name="taxa"
            :id="$id"
            placeholder="Type to search..."
            search="/api/taxa/search"
            include="#usethes-{{$id}}, #taxa-type-{{$id}}"
            >
            <x-slot:input class="peer-input p-1 z-10 bg-base-200 rounded-l-none border-l-0"></x-slot>
                <x-slot:menu></x-slot>
        </x-autocomplete-input>
    </div>
    <x-checkbox :id="'usethes-' . $id" class="mt-2" name="usethes" label="Include Synonyms" :default_value="request('usethes')" />
</div>
