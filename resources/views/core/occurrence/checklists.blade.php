@props(['occurrence', 'user_checklists', 'linked_checklists'])
@php
$user_checklist_options = [];
foreach($user_checklists as $checklist) {
    if(count($linked_checklists) <= 0 || $linked_checklists->search(fn ($v) => $v->clid == $checklist->clid) === false) {
        $user_checklist_options[] = item($checklist->clid, $checklist->name);
    }
}
@endphp
<div id="linked_checklists" class="relative flex flex-col gap-2" x-data="{ checklist_link_open: false }">
    <div>
        <span class="font-bold text-xl">
            {{ __('individual_linkedresources.SPCHECKREL') }}
        </span>
        <hr/>
    </div>

    @if(Gate::check('COLL_EDIT', $occurrence->collid) && count($user_checklist_options) > 0)
    <i @click="checklist_link_open = true" class="text-lg absolute top-0 right-3 fa-solid fa-plus"></i>
    <form hx-put="{{url('occurrence/' . $occurrence->occid . '/link/checklist' )}}" hx-target="#linked_checklists" x-show="checklist_link_open" class="flex flex-col gap-4">
        @csrf
        <x-select :label="__('sitemap.CHECKLIST')" name="clid" :items="$user_checklist_options" class="w-60"/>
        <x-input :label="__('projects.NOTES')" name="notes" />
        <x-input :label="__('individual_linkedresources.EDITORNOTES')" name="editor_notes" />
        <input type="hidden" name="voucher_tid" value="{{ $occurrence->tidInterpreted}}"/>
        <div class="flex gap-2">
            <x-button>{{ __('checklists_vamissingtaxa.LINK_VOUCHER') }}</x-button>
            <x-button @click="checklist_link_open=false" type="button" variant="neutral">{{ __('Cancel') }}</x-button>
        </div>
    </form>
    @endif

    <div>
        @if(count($linked_checklists))
            <ul>
            @foreach ($linked_checklists as $checklist)
                <li><x-link href="{{url('checklists') . $checklist->clid }}">{{ $checklist->name }}</x-link></li>
            @endforeach
            </ul>
        @else
            <p>{{ __('individual_linkedresources.NOTAVOUCHER') }}</p>
        @endif
    </div>
</div>
