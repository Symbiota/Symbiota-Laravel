@props(['collection', 'uploadProfiles' => []])
<x-layout class="flex flex-col gap-4">
    <x-breadcrumbs :items="[
        ['title' => 'Home', 'href' => url('')],
        ['title' => 'Collection Profile', 'href' => url('collections/' . request('collid') . '/import'), ],
        ['title' => 'List of Upload Profiles', 'href' => legacy_url('/collections/admin/specuploadmanagement.php?collid=' . request('collid'))],
        ['title' => 'Specimen Uploader']
        ]" />
    <div>
        <div class="text-4xl">Data Upload Module</div>
        <div class="text-2xl">collection Name</div>
        <div>Last collection upload</div>
    </div>

    @foreach ($uploadProfiles as $profile)
        <div>
            <div>
                {{ $profile->title }}
                ({{ $profile->uploadtype }})
                (#{{ $profile->uspid}})
                <x-icons.edit />
            </div>
        </div>
    @endforeach

    <fieldset>
        <legend>Delimited Text File Import: Identify Data Source</legend>
        <form class="flex flex-col gap-4">
            <input type="file" name="file_import" />
            <x-input label="Resource Path or URL:" name="url_import" />
            <x-checkbox label="Automap Fields" name="url_import" />
            <x-button>Analyze Fields</x-button>
        </form>
    </fieldset>
</x-layout>
