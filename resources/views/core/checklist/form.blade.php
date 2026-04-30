@props(['checklist', 'userChecklists'])
<form class="flex flex-col gap-4">
    <x-input :label="__('checklists_checklistadmin.CHECKNAME')" id="checklist_name" value="{{ $checklist->name }}" />
    <x-input :label="__('checklists_checklist.AUTHORS')" id="checklist_authors" value="{{ $checklist->authors }}" />
    <x-select
        class="w-full"
        id="type"
        :label="__('checklists_checklistadmin.CHECKTYPE')"
        :items="[
        ['value' => 'static', 'title' => __('checklists_checklistadmin.GENCHECK'), 'disabled' => false],
        ['value' => 'excludespp', 'title' => __('checklists_checklistadmin.EXCLUDESPP'), 'disabled' => !$userChecklists],
        ['value' => 'rarespp', 'title' =>__('checklists_checklistadmin.RARETHREAT'), 'disabled' => !Gate::check('RARE_SPP_ADMIN')]
    ]"
    />
    {{-- TODO (Logan) There is a an optional for excluding parent. Generally confusing not sure how to proceed--}}
    <x-select
        class="w-full"
        :label="__('checklists_checklistadmin.EXTSERVICE')"
        id="externalservice"
        :items="[
        ['value' => 0, 'title' => 'None', 'disabled' => false],
        ['value' => 'iNaturalist', 'title' => 'iNaturalist', 'disabled' => false]
    ]"
    />

    {{-- TODO (Logan) toggle this only when iNaturalist is selected --}}
    <x-input :label="__('checklists_checklistadmin.EXTSERVICEID')" id="externalserviceid" />
    <x-input :label="__('checklists_checklistadmin.EXTSERVICETAXON')" id="externalserviceiconictaxon" />

    <x-input :label="__('checklists_checklistadmin.LOC')" id="checklist_locality" value="{{ $checklist->locality }}" />
    <x-input
        :label="__('checklists_checklist.CITATION')"
        id="checklist_citation"
        value="{{ $checklist->publication }}"
    />
    <x-rich-editor :label="__('checklists_checklist.ABSTRACT')" id="Abstract">
        {!! Purify::clean($checklist->abstract) !!}
    </x-rich-editor>

    <x-input :label="__('projects.NOTES')" id="checklist_notes" value="{{ $checklist->notes }}" />

    {{-- uses $refClArr = $clManager->getReferenceChecklists(); $id $name--}}
    <x-select
        class="w-full"
        :label="__('checklists_checklistadminmeta.REFERENCE_CHECK')"
        :items="[
        ['value' => null, 'title' => 'None selected', 'disabled' => false]
    ]"
    />

    {{-- TODO (Logan) point radius tool --}}
    <x-input
        :label="__('checklists_checklistadmin.LATCENT')"
        id="checklist_latitude"
        value="{{ $checklist->latCentroid }}"
    />
    <x-input
        :label="__('checklists_checklistadmin.LONGCENT')"
        id="checklist_longitude"
        value="{{ $checklist->longCentroid }}"
    />
    <x-input
        :label="__('checklists_checklistadmin.POINTRAD')"
        id="checklist_point_radius"
        value="{{ $checklist->pointRadiusMeters }}"
    />

    <div>
        <x-input
            area
            :label="__('checklists_checklistadmin.POLYFOOT')"
            id="footprintwkt"
            value="{{ $checklist->footprintGeoJson }}"
        />
        <x-button class="mt-2" @click="openWindow('{{ url('tools/map/coordaid') }}?strict=1&mode=polygon')">
            {{-- TODO (Logan) translation --}}
            Polygon Tool
        </x-button>
    </div>

    <div class="flex flex-col gap-2">
        <x-checkbox
            id="dsynonyms"
            :label="__('checklists_checklist.DISPLAY_SYNONYMS')"
            :checked="$settings->dsynonyms ?? false"
        />
        <x-checkbox id="dcommon" :label="__('ident_key.DISPLAY_COMMON')" :checked="$settings->dcommon ?? false" />
        <x-checkbox
            id="dimages"
            :label="__('checklists_checklist.DISPLAYIMAGES')"
            :checked="$settings->dimages ?? false"
        />
        <x-checkbox
            id="dvoucherimages"
            :label="__('checklists_checklist.DISPLAYVOUCHERIMAGES')"
            :checked="$settings->dvoucherimages ?? false"
        />
        <x-checkbox
            id="ddetails"
            :label="__('checklists_checklistadmin.SHOWDETAILS')"
            :checked="$settings->ddetails ?? false"
        />

        {{-- Display images needs these two to be false --}}
        <x-checkbox
            id="dvouchers"
            :label="__('checklists_checklist.NOTESVOUC')"
            :checked="$settings->dvouchers ?? false"
        />
        <x-checkbox
            id="dauthors"
            :label="__('checklists_checklist.TAXONAUTHOR')"
            :checked="$settings->dauthors ?? false"
        />

        <x-checkbox id="dalpha" :label="__('checklists_checklist.TAXONABC')" :checked="$settings->dalpha ?? false" />
        <x-checkbox
            id="dsubgenera"
            :label="__('checklists_checklist.SHOWSUBGENERA')"
            :checked="$settings->dsubgenera ?? false"
        />
        <x-checkbox
            id="activatekey"
            :label="__('checklists_checklist.ACTIVATEKEY')"
            :checked="$settings->activatekey ?? false"
        />
    </div>

    <x-input
        :label="__('checklists_checklistadmin.DEFAULT_SORT')"
        id="sortsequence"
        type="number"
        value="{{ $checklist->sortSequence }}"
    />

    <x-select
        id="access"
        class="w-64"
        :label="__('projects.ACCESS')"
        defaultValue="{{ $checklist->access }}"
        :items="[
                [ 'title' => 'Private', 'value' => 'private', 'disabled' => false],
                [ 'title' => 'Can view with link', 'value' => 'view_with_link', 'disabled' => false],
                [ 'title' => 'Public', 'value' => 'public', 'disabled' => false],
            ]"
    />

    <x-button type="submit">Save Edits</x-button>
</form>
