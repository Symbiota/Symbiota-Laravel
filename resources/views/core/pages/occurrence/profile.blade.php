{{-- TODO (Logan) add options to have layout without header, footer --}}
<x-layout>
    <div class="flex items-center gap-4 mb-4">
        <img class="w-16" src="https://cch2.org/portal/content/collicon/blmar.jpg">
        <div class="text-2xl font-bold">
            BLMAR - BLM Arcata Field Office Herbarium (BLMAR)
        </div>
    </div>
    <x-tabs :tabs="['Details', 'Map', 'Commments', 'Linked Resources', 'Edit History']" :active="0">
        {{-- Occurrence Details --}}
        <div class="relative">

            <div class="absolute right-3 top-0 flex gap-2">
                <div>facebook</div>
                <div>twitter</div>
            </div>
            <div>Catalog #: [Content]</div>
            <div>Occurrence ID: [Content]</div>
            <div>Secondary Catalog: [Content]</div>
            <div>Taxon: [Content]</div>
            <div>Family: [Content]</div>
            <div>Collector: [Content]</div>
            <div>Number: [Content]</div>
            <div>Date: [Content]</div>
            <div>Verbatim Date: [Content]</div>
            <div>Locality: [Content]</div>
            <div>Latiude/Longitude: [Content]</div>
            <div>Disposition: [Content]</div>
            <div>[Creative Commons]</div>
            <div>Record ID: [Content]</div>

            <div>For additional information about his specimen, please contact: [Content]</div>

            <div>Do you see an error? If so, errors can be fixed using the [Occurrence Editor link]</div>
        </div>

        {{-- Map (Only render if lat long data present)--}}
        <div>
            TODO laravel leaflet
        </div>

        {{-- Comments --}}
        <div class="grid grid-cols-1 gap-2">
            <div class="text-lg font-bold">No Comments have been submitted</div>
            <form class="grid grid-cols-1 gap-2">
                <x-input label="New Comment" id="comment-input" name="comment" />
                <x-button class="w-fit">Submit Comment</x-button>
            </form>
            <p>Messages over 500 words long may be automatically truncated. All comments are moderated</p>
        </div>

        {{-- Linked Resources --}}
        <div>
            <fieldset class="relative border border-base-300 p-4">
                <legend>Species Checklist Relationship</legend>
                <p>This Occurrence has not been designated as a voucher for a species</p>
                <i class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
            </fieldset>
            <fieldset class="relative border border-base-300 p-4">
                <legend>Dataset Linkages</legend>
                <p>Occurence is not linked to any datasets</p>
                <i class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
            </fieldset>
        </div>

        {{-- Edit History --}}
        <div>
            <div>
                Entered By: [Content]
            </div>
            <div>
                Date Entered: [Content]
            </div>
            <div>
                Date Modified: [Content]
            </div>
            <div>
                Source Date Modified: [Content]
            </div>
            <div class="my-4">
                Record has not been edited since being entered [Empty Case]
            </div>
            <div>
                Note: Edits are only viewable by collection administrators and editors [Empty Case]
            </div>
        </div>

    </x-tabs>
</x-layout>
