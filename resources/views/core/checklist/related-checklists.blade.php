<div class="flex flex-col gap-2">
    <div class="flex">
        <span class="font-bold text-2xl">
           {{ $LANG['CHILD_CHECKLIST'] }}
        </span>

        <span class="flex flex-grow justify-end">
            <x-modal>
                <x-slot name="button">
                    {{ $LANG['ADD_CHILD'] }}
                </x-slot>
                <x-slot name="title" class="text-2xl">
                    {{ $LANG['ADD_CHILD'] }}
                </x-slot>
                <x-slot name="body">
                    <form method="post" class="flex flex-col gap-4">
                        <x-select id="clidadd" class="w-full" label="Checklist" :items="$childChecklistsItems" />
                        <input type="hidden" name="submitaction" value="addChildChecklist" aria-label="{{ $LANG['ADD_CHILD'] }}" />

                        <div class="flex align-items gap-2">
                            <x-button type="submit">Add</x-button>
                            <x-button variant="error" type="button">Cancel</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        </span>
    </div>
    <hr/>
    <p>{{ $LANG['CHILD_DESCRIBE'] }}</p>
    @if($childChecklists)
        @foreach ($childChecklists as $child_clid => $child)
        <div class="flex items-center gap-2 border p-2 border-base-300 bg-base-200 rounded-md">
            <span class="flex-grow">
                <x-link target="_blank" href="{{ url('checklists/' . $child_clid) }}">
                    {{ $child['name']}}
                </x-link>
            </span>
            <form method="post">
                @csrf
                <input name="cliddel" type="hidden" value="{{ $child_clid }}" />
                <input name="submitaction" type="hidden" value="delchild" />
                <button type="submit">
                    <x-icons.delete class="cursor-pointer" />
                <button>
            </form>
        </div>
        @endforeach
    @else
    <p>{{ $LANG['NO_CHILDREN'] }}</p>
    @endif

    <x-link href="{{ legacy_url('/profile/viewprofile.php?excludeparent=' . $clid) }}">
        {{ $LANG['CREATE_EXCLUSION_LIST'] }}
    </x-link>
</div>

<div class="flex flex-col gap-2">
    <div class="font-bold text-2xl">
        {{ $LANG['PARENTS'] }}
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
        <p>{{ $LANG['NO_PARENTS'] }}</p>
    @endif
</div>

<div class="flex flex-col gap-2">
    <div class="font-bold text-2xl">
       {{ $LANG['BATCH_PARSE_SP_LIST'] }}
    </div>
    <hr/>
    <p>{{ $LANG['BATCH_PARSE_DESCRIBE'] }}</p>
    <form class="flex flex-col gap-4">
        <div class="flex gap-4">
            {{-- TODO (Logan) replace with taxon search? --}}
            <x-input required id="taxon" :label="$LANG['TAXONOMICNODE']"/>
            <x-input required id="parsetid" :label="$LANG['PARSETID']"/>
        </div>
        <x-select id="targetclid" class="w-full" label="Target Checklist" :items="$userChecklists" />
        <x-select id="parentclid" class="w-full" label="Parent Checklist" :items="$userChecklists" />
        <x-select id="targetpid"  class="w-full" label="Add to project" :items="$userProjects" />
        <x-radio id="transmethod" :defaultValue="$transferMethod" name="transmethod" label="Transfer method" :options="[
            ['label' => $LANG['TRANSFERTAXA'], 'value' => 0],
            ['label' => $LANG['COPYTAXA'], 'value' => 1],
        ]" />
        <x-checkbox id="parentclid" :label="$LANG['COPYPERMISSIONANDGENERAL']" :checked="$copyAttributes"/>
        <input name="submitaction" type="hidden" value="parseChecklist" />
        <x-button>{{ $LANG['PARSE_CHECKLIST'] }}</x-button>
        <x-link target="_blank" href="{{ legacy_url('/taxa/taxonomy/taxonomydisplay.php') }}">{{ $LANG['OPEN_TAX_THES_EXPLORE'] }}</x-link>
    </form>
</div>
