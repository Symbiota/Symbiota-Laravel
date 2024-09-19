@props(['media' => []])
<x-layout class="sm:w-[90%] lg:w-[70%] m-auto" x-data="{ loading: true }">
    <h1 class="text-5xl font-bold text-primary mb-8">Multimedia Search</h1>
    <fieldset>
        <legend class="text-2xl font-bold text-primary">Search Criteria</legend>
        <form
            hx-get="{{ url('/media/search') }}"
            hx-indicator="#scroll-loader"
            hx-vals='{"partial": true, "start": 0}'
            hx-target="#photo-gallery"
            hx-indicator="#scroll-loader"
            x-on:htmx:before-send="loading = true"
            class="grid grid-col-1 gap-4">
            <x-taxa-search
                :taxa_value="request('taxa')"
                :taxa_type_value="request('taxa-type')"
                :use_thes_value="request('usethes')"
            />
            <x-select label="Creator">
                <option value="1">Dummy Creator 1</option>
                <option value="2">Dummy Creator 2</option>
            </x-select>

            <div class="grid grid-cols-2 grid-row-1">
                <div>
                    <x-select label="Multimedia Tags">
                        <option value="1">with</option>
                        <option value="2">without</option>
                    </x-select>
                </div>
                <div class="align-bottom mt-auto">
                    <x-select>
                        <option value="1">Tag 1</option>
                        <option value="2">Tag 2</option>
                    </x-select>
                </div>
            </div>

            <x-radio name="resource_counts" :default_value="request('resource_counts') ?? 'all_multimedia'" label="Multimedia Tags" :options="[
                [ 'value' => 'all_multimedia', 'label' => 'All Multimedia' ],
                [ 'value' => 'one_per_taxon', 'label' => 'One per taxon' ],
                [ 'value' => 'one_per_spec', 'label' => 'One per specimen' ],
            ]">
            </x-radio>

            <x-radio name="resource_type" :default_value="request('resource_type') ?? 'all_multimedia'" label="Resource Type" :options="[
                [ 'value' => 'all_multimedia', 'label' => 'All Multimedia' ],
                [ 'value' => 'one_per_taxon', 'label' => 'Specimen/Vouchered Multimedia' ],
                [ 'value' => 'one_per_spec', 'label' => 'Field Multimedia (lacking specific locality details)' ],
            ]">
            </x-radio>

            <x-radio name="media_type" :default_value="request('media_type') ?? 'all'" label="Multimedia Type" :options="[
                [ 'value' => 'all', 'label' => 'All' ],
                [ 'value' => 'image', 'label' => 'Image' ],
                [ 'value' => 'audio', 'label' => 'Audio' ],
            ]">
            </x-radio>

            <x-select label="Page Count">
                <option value="200">200</option>
                <option value="400">400</option>
                <option value="600">600</option>
                <option value="800">800</option>
                <option value="1000">1000</option>
            </x-select>
            <x-button type="submit">
                Load Multimedia
            </x-button>
        </form>
        <div x-show="!loading" x-on:htmx:after-swap="loading = false" id="photo-gallery" class="flex flex-wrap flex-row gap-3">
            <x-media.item :media="$media" />
        </div>
        <div id="scroll-loader" class="htmx-indicator">
            <div class="stroke-accent w-full h-16 flex justify-center">
                <x-icons.loading/>
            </div>
        </div>
    </fieldset>
</x-layout>
