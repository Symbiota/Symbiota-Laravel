@props(['disabled'])
<div class="flex flex-col gap-4">
    <div class="text-2xl font-bold">{{ __('checklists_checklistadmin.PERMREMOVECHECK') }}</div>
    <hr />
    <p>{{ __('checklists_checklistadmin.REMOVEUSERCHECK') }}</p>
    <p class="text-warning text-lg font-bold">{{ __('checklists_checklistadmin.WARNINGNOUN') }}</p>
    <x-button :disabled="$disabled"> {{ __('checklists_checklistadmin.DELETECHECK') }} </x-button>
</div>
