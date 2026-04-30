@props(['clManager'])
<div class="flex flex-col gap-2">
    <div class="font-bold text-2xl">
        {{ __('checklists_checklistadminchildren.PARENTS') }}
    </div>
    <hr/>
    @if($parents = $clManager->getParentChecklists())
    <div class="pl-4">
        @foreach($parents as $parent_clid => $name)
        <li>
            <x-link target="_blank" href="{{ url('checklists/' . $parent_clid) }}">
                {{ $name }}
            </x-link>
        </li>
        @endforeach
    </div>
    @else
        <p>{{ __('checklists_checklistadminchildren.NO_PARENTS') }}</p>
    @endif
</div>
