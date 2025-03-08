<x-layout class="p-0" :hasHeader="false" :hasNavbar="false" :hasFooter="false">
    <div class="p-4">
        <p>TODO (Logan) grab the help text</p>
        <div class="flex items-center gap-2">
            <x-input name="lat" label="lat" />
            <x-input name="lng" label="lng" />
            <x-input name="erradius" label="erradius" />
            <x-button>Save</x-button>
        </div>
    </div>
    <x-map />
</x-layout>
