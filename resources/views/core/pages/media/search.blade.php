@props(['media' => [], 'creators' => []])
<x-layout class="sm:w-[90%] lg:w-[70%] m-auto" x-data="{ loading: true }">
    <h1 class="text-5xl font-bold text-primary mb-8">Multimedia Search</h1>
    <fieldset>
        <legend class="text-2xl font-bold text-primary">Search Criteria</legend>
        <form hx-get="{{ url('/media/search') }}" hx-indicator="#scroll-loader" hx-vals='{"partial": true, "start": 0}'
            hx-target="#photo-gallery" hx-indicator="#scroll-loader" x-on:htmx:before-send="loading = true"
            class="grid grid-col-1 gap-4">
            <x-taxa-search :taxa_value="request('taxa')" :taxa_type_value="request('taxa-type')"
                :use_thes_value="request('usethes')" />
            <x-select label="Creator" option-title="" option-value="" :default="0" :items="[
                    [
                        'title' => '-- Select a Creator --',
                        'value' => '',
                        'disabled' => true
                    ],
                    [
                        'title' => 'Test Creator',
                        'value' => '1',
                        'disabled' => false
                    ],
                ]"
            />

            <div>
                <label id="media-tag-label" class="text-lg">Multimedia Tags</label>
                <div class="flex gap-1">
                    <x-select labeledBy="media-tag-label" name="with" :default="0" :items="[
                        [
                            'title' => 'with',
                            'value' => '1',
                            'disabled' => false
                        ],
                        [
                            'title' => 'without',
                            'value' => '0',
                            'disabled' => false
                        ],
                    ]"
                    />
                    <x-select id="tag" name="tag" class="w-full" :default="0" :items="[
                        [
                            'title' => '-- Select a Tag --',
                            'value' => '',
                            'disabled' => true
                        ],
                    ]"
                    />
                </div>
            </div>

            <x-radio name="resource_counts" :default_value="request('resource_counts') ?? ''" label="Multimedia Tags"
                :options="[
                [ 'value' => '', 'label' => 'All Multimedia' ],
                [ 'value' => 'one_per_taxon', 'label' => 'One per taxon' ],
                [ 'value' => 'one_per_spec', 'label' => 'One per specimen' ],
            ]">
            </x-radio>

            <x-radio name="resource_type" :default_value="request('resource_type') ?? ''" label="Resource Type"
                :options="[
                [ 'value' => '', 'label' => 'All Multimedia' ],
                [ 'value' => 'one_per_taxon', 'label' => 'Specimen/Vouchered Multimedia' ],
                [ 'value' => 'one_per_spec', 'label' => 'Field Multimedia (lacking specific locality details)' ],
            ]">
            </x-radio>

            <x-radio name="media_type" :default_value="request('media_type') ?? ''" label="Multimedia Type" :options="[
                [ 'value' => '', 'label' => 'All' ],
                [ 'value' => 'image', 'label' => 'Image' ],
                [ 'value' => 'audio', 'label' => 'Audio' ],
            ]">
            </x-radio>
            <x-button type="submit">
                Load Multimedia
            </x-button>
        </form>
        <div x-show="!loading" x-on:htmx:after-swap="loading = false" id="photo-gallery"
            class="flex flex-wrap flex-row gap-3">
            <x-media.item :media="$media" />
        </div>
        <div id="scroll-loader" class="htmx-indicator">
            <div class="stroke-accent w-full h-16 flex justify-center">
                <x-icons.loading />
            </div>
        </div>
    </fieldset>
</x-layout>
