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
<x-layout class="flex flex-col gap-4">
    <h1 class="text-4xl font-bold">Data Usage Guidelines</h1>
    <p>
        By downloading data, the user confirms that he/she has read and agrees with the general <x-link
            class="text-base" href="{{ url('usagepolicy') }}">data usage terms</x-link>. Note that additional terms of
        use specific to the individual collections may be distributed with the data download. When present, the terms
        supplied by the owning institution should take precedence over the general terms posted on the website.
    </p>

    <form
        hx-get="{{ url('collections/download/file') }}"
        x-data="{}"
        x-on:htmx:before-request="downloadHtmxRequest(event)"
        hx-vals="{{ json_encode(request()->all()) }}"
        class="flex flex-col gap-4"
        >
        <x-radio label="Structure" name="schema" :default_value="'symbiota'"
            :options="[ ['label' => 'Symbiota Native', 'value' => 'symbiota'], ['label' => 'Darwin Core', 'value' => 'dwc']]" />
        <input type="hidden" name="download" value="1">

        <fieldset class="flex flex-col gap-2">
            <legend class="text-xl">Data Extensions</legend>
            <x-checkbox label="Include Determinations History" default_value="1" name="include_determination_history" />
            <x-checkbox label="Include Media Records" default_value="1" name="include_media" />
            <x-checkbox label="Include Occurence Trait Attributes" default_value="1" name="include_occurrence_trait_attributes" />
            <x-checkbox label="Include Alternative Identifiers" default_value="1" name="include_alternative_identifers" />
            * Output must be a compressed archive
        </fieldset>

        <x-radio label="File Format" name="file_format" :default_value="'csv'"
            :options="[ ['label' => 'Comma Delimited (CSV)', 'value' => 'csv'], ['label' => 'Tab Deltimited', 'value' => 'tsv']]" />

        <x-radio label="Character Set" name="charset" :default_value="'ISO-8859-1'"
            :options="[ ['label' => 'ISO-8859-1 (Western)', 'value' => 'ISO-8859-1'], ['label' => 'UTF-8 (unicode)', 'value' => 'UTF-8']]" />

        <fieldset class="flex flex-col gap-2">
            <legend class="text-xl">Compression</legend>
            <x-checkbox label="Compressed ZIP file" default_value="1" name="compressed" />
        </fieldset>

        <x-button>Download Data</x-button>
        * * There is a 1,000,000 record limit to occurrence downloads
    </form>
</x-layout>
