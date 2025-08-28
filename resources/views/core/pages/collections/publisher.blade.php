<x-layout class="flex flex-col gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('')],
        ['title' => 'Sitemap', 'href' => url('/sitemap')],
        ['title' => 'Darwin Core Archive Publisher']
        ]" />
    <div class="text-4xl font-bold">Darwin Core Archive Publishing</div>
    <p>
        The following downloads are occurrence data packages from collections that have chosen to publish their complete
        dataset as a <x-link target="_blank" href="https://en.wikipedia.org/wiki/Darwin_Core_Archive">Darwin Core
            Archive (DwC-A)</x-link> file. DwC-A files are a single compressed ZIP file that contains one to several
        data files along with a meta.xml document that describes the content. Archives published through this portal
        contain three comma separated (CSV) files containing occurrences, identifications (determinations), and image
        metadata. Fields within the occurrences.csv file are defined by the <x-link
            href="http://rs.tdwg.org/dwc/terms/">Darwin Core</x-link> exchange standard. The identification and image
        files follow the DwC extensions for those data types.
    </p>

    <div>
        <h2 class="text-2xl font-bold">Data Usage Policy</h2>
        <p>Use of these datasets requires agreement with the terms and conditions in our <x-link
                href="{{ url('usagepolicy') }}">Data Usage Policy</x-link>. Locality details for rare, threatened, or
            sensitive records have been redacted from these data files.
            One must contact the collections directly to obtain access to sensitive locality data.</p>
    </div>

    <div>
        <div>
            RSS Feed
        </div>
        <!-- TODO (Logan) Rss feed part of this thing -->
    </div>
</x-layout>
