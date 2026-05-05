@props(['synonyms'=>[], 'mode'=>'edit', 'taxonInfo'=>null])
<form
    id="taxonomic-status-edit-form"
    method="POST"
    action="{{ route('taxon.update', ['tid' => $taxonInfo->tid ?? '']) }}"
>
    @csrf
    <x-input type="hidden" name="mode" id="mode" :value="$mode" />
    <x-input type="hidden" name="edit-type" id="edit-type" value="synonymedits" />
    <x-fieldset>
        <legend class="text-lg font-bold">Taxonomic Placement</legend>
        <span>Status</span>
    <div class="flex items-center gap-4">
        <h1 class="w-fit text-2xl font-bold">
            Edit Taxonomic Status for: <i>{{ $taxonInfo->sciName ?? ' name missing' }}</i>
        </h1>
        <x-button type="submit">Save</x-button>
    </div>
    </x-fieldset>
</form>
