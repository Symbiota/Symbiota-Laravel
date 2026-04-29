@props(['voucherProjects'])
<div class="flex flex-col gap-2">
    <div class="font-bold text-2xl">
      {{ __('checklists_checklistadmin.ADDIMGVOUC') }}
    </div>
    <hr/>
    <p>{{ __('checklists_checklistadmin.FORMADDVOUCH') }}</p>
</div>
{{-- Note: Should action collections/editor/observationsubmit.php --}}
<form class="flex flex-col gap-4" action="{{ legacy_url('collections/editor/observationsubmit.php') }}">
    <x-select name="collid" class="w-full" default="0" :label="__('checklists_checklistadmin.SELECTVOUCPROJ')" :items="$voucherProjects" />
    <x-button>{{ __('checklists_checklistadmin.ADDIMGVOUC') }}</x-button>
</form>
