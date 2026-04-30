@props(['userChecklists', 'userProjects', 'transferMethod', 'copyAttributes'])
<div class="flex flex-col gap-2">
    <div class="font-bold text-2xl">
       {{ __('checklists_checklistadminchildren.BATCH_PARSE_SP_LIST') }}
    </div>
    <hr/>
    <p>{{ __('checklists_checklistadminchildren.BATCH_PARSE_DESCRIBE') }}</p>
    <form class="flex flex-col gap-4">
        <div class="flex gap-4">
            {{-- TODO (Logan) replace with taxon search? --}}
            <x-input required id="taxon" :label="__('checklists_checklistadminchildren.TAXONOMICNODE')"/>
            <x-input required id="parsetid" :label="__('checklists_checklistadminchildren.PARSETID')"/>
        </div>
        <x-select id="targetclid" class="w-full" label="Target Checklist" :items="$userChecklists" />
        <x-select id="parentclid" class="w-full" label="Parent Checklist" :items="$userChecklists" />
        <x-select id="targetpid"  class="w-full" label="Add to project" :items="$userProjects" />
        <x-radio id="transmethod" :defaultValue="$transferMethod" name="transmethod" label="Transfer method" :options="[
            ['label' => __('checklists_checklistadminchildren.TRANSFERTAXA'), 'value' => 0],
            ['label' => __('checklists_checklistadminchildren.COPYTAXA'), 'value' => 1],
        ]" />
        <x-checkbox id="parentclid" :label="__('checklists_checklistadminchildren.COPYPERMISSIONANDGENERAL')" :checked="$copyAttributes"/>
        <input name="submitaction" type="hidden" value="parseChecklist" />
        <x-button>{{ __('checklists_checklistadminchildren.PARSE_CHECKLIST') }}</x-button>
        <x-link target="_blank" href="{{ legacy_url('/taxa/taxonomy/taxonomydisplay.php') }}">{{ __('checklists_checklistadminchildren.OPEN_TAX_THES_EXPLORE') }}</x-link>
    </form>
</div>
