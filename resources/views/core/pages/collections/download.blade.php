@pushOnce('js-scripts')
<script type="text/javascript">
    /* Intercepts HTMX-get and forwards parameters to a download link. use htmx beforeRequest event */
    function downloadHtmxRequest(event) {
        event.preventDefault();

        if(event.detail.requestConfig.verb !== 'get') {
            console.error('downloadHtmxRequest only supports the "hx-get"')
        }

        const download_link = document.createElement('a');
        document.body.appendChild(download_link)

        const params = new URLSearchParams(event.detail.requestConfig.parameters)

        download_link.href = event.detail.requestConfig.path + '?' + params.toString();
        download_link.click();
    }
</script>
@endPushOnce
<x-layout class="flex flex-col gap-4" :hasHeader="false" :hasFooter="false" :hasNavbar="false">
    <h1 class="text-4xl font-bold">Data Usage Guidelines</h1>
    <p>
        By downloading data, the user confirms that he/she has read and agrees with the general <x-link
            class="text-base" href="{{ url('usagepolicy') }}">data usage terms</x-link>. Note that additional terms of
        use specific to the individual collections may be distributed with the data download. When present, the terms
        supplied by the owning institution should take precedence over the general terms posted on the website.
    </p>

    <form
        hx-get="{{ url('collections/download/file') }}"
        x-data="{ allow_extensions: true }"
        x-on:htmx:before-request="downloadHtmxRequest(event)"
        hx-vals="{{ json_encode(request()->all()) }}"
        class="flex flex-col gap-4"
        >
        <x-radio label="Structure" name="schema" :default_value="'symbiota'"
            :options="[ ['label' => 'Symbiota Native', 'value' => 'symbiota'], ['label' => 'Darwin Core', 'value' => 'dwc']]" />
        <input type="hidden" name="download" value="1">

        <fieldset class="flex flex-col gap-2">
            <legend class="text-xl">Data Extensions</legend>
            <div class="flex flex-col gap-2">
                <x-checkbox
                    label="Include Determinations History"
                    x-bind:checked="allow_extensions"
                    x-bind:disabled="!allow_extensions"
                    :checked="true"
                    name="include_determination_history"
                />
                <x-checkbox
                    label="Include Media Records"
                    x-bind:checked="allow_extensions"
                    x-bind:disabled="!allow_extensions"
                    :checked="true"
                    name="include_media"
                />
                <x-checkbox
                    label="Include Occurence Trait Attributes"
                    x-bind:checked="allow_extensions"
                    x-bind:disabled="!allow_extensions"
                    :checked="true"
                    name="include_occurrence_trait_attributes"
                />
                <x-checkbox
                    label="Include Alternative Identifiers"
                    x-bind:checked="allow_extensions"
                    x-bind:disabled="!allow_extensions"
                    :checked="true"
                    name="include_alternative_identifers"
                />
            </div>
            <div class="bg-error text-error-content p-2 w-fit rounded-md" x-show="!allow_extensions" x-cloak>
                Extensions require output to be compressed
            </div>
        </fieldset>

        <x-radio label="File Format" name="file_format" :default_value="'csv'"
            :options="[ ['label' => 'Comma Delimited (CSV)', 'value' => 'csv'], ['label' => 'Tab Deltimited', 'value' => 'tsv']]" />

        <x-radio label="Character Set" name="charset" :default_value="'ISO-8859-1'"
            :options="[ ['label' => 'ISO-8859-1 (Western)', 'value' => 'ISO-8859-1'], ['label' => 'UTF-8 (unicode)', 'value' => 'UTF-8']]" />

        <fieldset class="flex flex-col gap-2">
            <legend class="text-xl">Compression</legend>
            <x-checkbox label="Compressed ZIP file" :checked="true" name="compressed" x-on:change="allow_extensions = !allow_extensions"/>
        </fieldset>

        <x-button>Download Data</x-button>

        @if(count($errors) > 0)
        <div class="mb-4">
            @foreach ($errors->all() as $error)
            <div class="bg-error text-error-content rounded-md p-4">
                {{ $error }}
            </div>
            @endforeach
        </div>
        @endif
        * * There is a 1,000,000 record limit to occurrence downloads
    </form>
</x-layout>
