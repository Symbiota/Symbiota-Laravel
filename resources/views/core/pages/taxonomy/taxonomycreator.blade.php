<x-layout class="p-10">
    <h1 class="text-5xl font-bold text-primary mb-8">Add New Taxon</h1>
    <div id="sciname-display-div">
        <h1 class="text-3xl text-primary mb-8">Sciname will be saved as:
            <span id="sciname-display" name="sciname-display"></span>
        </h1>
        <form>
            <x-fieldset label="Optional Quick Parser" class="mb-3">
                <x-input label="Paste name here for parsing: " class="mb-0" type="text" id="quickparser" name="quickparser" value="" onchange="parseName(this.form)" />
            </x-fieldset>
            <fieldset>
                <legend class="font-bold">Add New Taxon</legend>
                <x-input required label="Taxon rank: " class="mb-0" type="text" id="rank-id" name="rank-id" value="" />
            </fieldset>
        </form>
    </div>
</x-layout>