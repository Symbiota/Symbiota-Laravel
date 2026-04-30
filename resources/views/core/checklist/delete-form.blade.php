@props(['disabled'])
<div class="flex flex-col gap-4">
    <div class="font-bold text-2xl">
        {{ __('checklists_checklistadmin.PERMREMOVECHECK') }}
    </div>
    <hr />
    <p>{{ __('checklists_checklistadmin.REMOVEUSERCHECK') }}</p>
    <p class="font-bold text-lg text-warning">{{ __('checklists_checklistadmin.WARNINGNOUN') }}</p>
    <x-button :disabled="$disabled" >
        {{ __('checklists_checklistadmin.DELETECHECK') }}
    </x-button>
</div>
