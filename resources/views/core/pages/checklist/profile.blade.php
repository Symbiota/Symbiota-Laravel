@props([
    'checklist',
    'clManager',

    'taxaList' => [],
    'voucherArr' => [],
    'parent' => [],
    'children' => [],
    'exclusions' => [],

    'show_synonyms' => false,
    'show_common' => false,
    'show_notes_vouchers' => false,
    'show_taxa_authors' => false,
    'show_images' => false,
    'show_taxa_alphabetically' => false,
    'limit_voucher_images' => false,
    'show_subgenera' => false,
    'activate_key' => false,
])
@php global $SERVER_ROOT;
include_once(legacy_path('/classes/ChecklistManager.php'));

$isClAdmin = Gate::check('CL_ADMIN', $checklist->clid);
$statusStr = false;

if($isClAdmin){
	if(request('formsubmit') === 'AddSpecies'){
		$statusStr = $clManager->addNewSpecies(request()->except('_token'));
	}
}

//Handling Dynamic Breadcrumbs
$breadcrumbs = [
    ['title' => 'header.H_HOME', 'href' => url('') ],
];

if($checklist->projname && $checklist->pid) {
    $breadcrumbs[] = [
        'title' => $checklist->projname,
        'href' => legacy_url('/projects/index.php?pid='. $checklist->pid)
    ];
}

$breadcrumbs[] = $checklist->name;

@endphp
<x-margin-layout x-data="{ sppEditToggle: false }">
    <div>
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>
    <div class="flex items-center">
        <h1 class="text-4xl font-bold">{{ $checklist->name }}</h1>
        <div class="flex flex-grow justify-end gap-2">
            @can('CL_ADMIN', $checklist->clid)
                <x-button
                    href="{{ legacy_url('/checklists/checklistadmin.php?clid=' . $checklist->clid) }}"
                    aria-label="{{ __('checklists_checklist.CHECKLIST_ADMIN') }}"
                    title="{{ __('checklists_checklist.CHECKLIST_ADMIN') }}"
                >
                    <i class="flex-end fas fa-edit"></i> A
                </x-button>
                <x-button
                    href="{{ legacy_url('/checklists/voucheradmin.php?clid=' . $checklist->clid) }}"
                    aria-label="{{ __('checklists_checklist.MANAGE_VOUCHERS') }}"
                    title="{{ __('checklists_checklist.MANAGE_VOUCHERS') }}"
                >
                    <i class="flex-end fas fa-edit"></i> V
                </x-button>
                <x-button
                    @click="sppEditToggle = !sppEditToggle"
                    aria-label="{{ __('checklists_checklist.EDIT_LIST') }}"
                    title="{{ __('checklists_checklist.EDIT_LIST') }}"
                >
                    <i class="flex-end fas fa-edit"></i>Spp
                    <span x-show="sppEditToggle">- ON</span>
                </x-button>
            @endcan
        </div>
    </div>

    @if($checklist->abstract || $checklist->authors || $checklist->locality || ($checklist->latCentroid && $checklist->longCentroid) || $checklist->notes || count($exclusions) || count($children) || count($parent))
        <x-accordion :label="__('checklists_dynamicmap.MORE_DETAILS')" variant="clear-primary">
            <div class="flex flex-col gap-2">
                <x-checklist.metadata
                    :checklist="$checklist"
                    :parent="$parent"
                    :children="$children"
                    :exclusions="$exclusions"
                />
            </div>
        </x-accordion>
    @endif

    <x-errors :errors="$statusStr? message_bag([$statusStr]): []" />

    <div class="flex items-center gap-2">
        <div class="flex w-fit">
            <x-popover class="w-[500px]">
                <form
                    x-data="{ form: $el, show_images: {{ $show_images? 'true': 'false' }}}"
                    target="_blank"
                    id="checklist-display-form"
                    class="flex flex-col gap-2"
                >
                    <x-taxa-search />
                    @if($activate_key)
                        <x-link href="{{ legacy_url('/ident/key.php') }}?dynclid={{ 0 }}&clid={{ $checklist->clid }}">
                            {{ __('checklists_checklist.OPEN_KEY') }}
                        </x-link>
                    @endif
                    <x-link
                        href="{{ legacy_url('/games/flashcards.php') }}?dynclid={{ 0 }}&listname={{ $checklist->name }}&clid={{ $checklist->clid }}"
                    >
                        {{ __('checklists_checklist.FLASH') }}
                    </x-link>
                    <x-link
                        href="{{ legacy_url('/games/namegame.php') }}?dynclid={{ 0 }}&listname={{ $checklist->name }}&clid={{ $checklist->clid }}"
                    >
                        {{ __('checklists_checklist.NAMEGAME') }}
                    </x-link>

                    <x-select
                        class="w-64"
                        default="0"
                        :items="[
                        item('Original Checklist', __('checklists_checklist.OGCHECK')),
                        item('Central Thesaurus', __('taxonomy_taxonomydisplay.CENTRAL_THESAURUS'))
                    ]"
                    />

                    <input type="hidden" name="clid" value="{{ $checklist->clid }}" />
                    <div class="text-lg font-bold">{{ __('checklists_checklist.TAXONOMIC_FILTER') }}</div>
                    <x-checkbox
                        :label="__('checklists_checklist.DISPLAY_SYNONYMS')"
                        :checked="$show_synonyms"
                        x-show="!show_images"
                        x-bind:disabled="show_images"
                        name="show_synonyms"
                    />
                    <x-checkbox
                        :label="__('profile_tpeditor.COMMON_NAMES')"
                        :checked="$show_common"
                        name="show_common"
                    />
                    <x-checkbox
                        :label="__('checklists_checklist.DISPLAYIMAGES')"
                        :checked="$show_images"
                        x-on:change="show_images = $event.target.checked"
                        name="show_images"
                    />
                    <x-checkbox
                        :label="__('checklists_checklist.LINKED_IMG')"
                        :checked="$limit_voucher_images"
                        x-show="show_images"
                        x-bind:disabled="!show_images"
                        name="limit_voucher_images"
                    />
                    <x-checkbox
                        :label="__('checklists_checklist.NOTESVOUC')"
                        :checked="$show_notes_vouchers"
                        x-show="!show_images"
                        x-bind:disabled="show_images"
                        name="show_notes_vouchers"
                    />
                    <x-checkbox
                        :label="__('checklists_checklist.TAXONAUTHOR')"
                        :checked="$show_taxa_authors"
                        x-show="!show_images"
                        x-bind:disabled="show_images"
                        name="show_taxa_authors"
                    />
                    <x-checkbox
                        :label="__('checklists_checklist.TAXONABC')"
                        :checked="$show_taxa_alphabetically"
                        name="show_taxa_alphabetically"
                    />
                    <input type="hidden" name="partial" value="taxa-list" />
                    <div class="flex items-center">
                        <x-button
                            type="button"
                            x-on:click="popoverOpen = false"
                            hx-target="#taxa-list"
                            hx-include="#checklist-display-form"
                            hx-swap="outerHTML"
                            hx-get="{{ url()->current() }}?partial=taxa-list"
                            hx-indicator="#checklist-loader"
                        >
                            {{ __('checklists_checklist.BUILD_LIST') }}
                        </x-button>
                        <div class="flex flex-grow items-center justify-end gap-4 text-xl">
                            <button
                                name="dllist_x"
                                type="submit"
                                data-action="{{ legacy_url('checklists/checklist.php') }}"
                                x-on:click="
                                    form.action = $el.getAttribute('data-action');
                                    target = '_blank';
                                    form.method = 'post';
                                "
                            >
                                <x-icons.download />
                            </button>
                            <button
                                type="submit"
                                data-action="{{ url('checklists/' . $checklist->clid. '/pdf') }}"
                                x-on:click="
                                    form.action = $el.getAttribute('data-action');
                                    form.target = '_blank';
                                    form.method = 'get';
                                "
                            >
                                <x-icons.print />
                            </button>
                            <button
                                x-show="!show_images"
                                name="exportdoc"
                                type="submit"
                                data-action="{{ legacy_url('checklists/mswordexport.php') }}"
                                x-on:click="
                                    form.action = $el.getAttribute('data-action');
                                    form.target = '_blank';
                                    form.method = 'post';
                                "
                            >
                                <x-icons.word />
                            </button>
                        </div>
                    </div>
                </form>
            </x-popover>
        </div>
        <div class="flex w-full items-center gap-2">
            @foreach([
                __('checklists_checklist.FAMILIES') => $clManager->getFamilyCount(),
                __('checklists_checklist.GENERA') => $clManager->getGenusCount(),
                __('checklists_checklist.SPECIES') => $clManager->getSpeciesCount(),
                __('checklists_checklist.TOTAL_TAXA') => $clManager->getTaxaCount(),
            ] as $label => $value)
                <div><span class="font-bold">{{ $label }}: </span>{{ $value }}</div>
            @endforeach

            <div class="flex-grow">
                <x-button
                    class="ml-auto"
                    href="{{ legacy_url('/collections/map/index.php') }}?db=all&type=1&reset=1&taxonfilter&cltype=vouchers&clid={{ $checklist->clid }}"
                >
                    <i class="fas fa-earth-americas"></i> {{ __('header.H_MAP') }}
                </x-button>
            </div>

            @if($isClAdmin)
                <x-modal>
                    <x-slot name="button">
                        {{ __('checklists_checklist.ADD_SPECIES') }}
                    </x-slot>
                    <x-slot name="title" class="text-2xl">
                        {{ __('checklists_checklist.ADD_SPECIES') }}
                    </x-slot>
                    <x-slot name="body">
                        <form method="post" class="flex flex-col gap-4">
                            @csrf
                            <input type="hidden" name="partial" value="taxa-list" />
                            <input type="hidden" name="formsubmit" value="AddSpecies" />
                            <input type="hidden" name="clid" value="{{ $checklist->clid }}" />
                            <x-taxa-search :label="__('ident_key.TAXON')" />
                            {{-- <x-input :label="__('checklists_checklist.MORPHOSPECIES')" id="morphospecies" /> --}}
                            <x-input :label="__('checklists_checklist.FAMILYOVERRIDE')" id="familyOverride" />
                            <x-input :label="__('checklists_checklist.HABITAT')" id="habitat" />
                            <x-input :label="__('checklists_checklist.ABUNDANCE')" id="abundance" />
                            <x-input :label="__('projects.NOTES')" id="notes" />
                            <x-input :label="__('checklists_checklist.INTNOTES')" id="internalNotes" />
                            <x-input :label="__('checklists_checklist.SOURCE')" id="source" />
                            <x-button>{{ __('checklists_checklist.ADD_SPECIES') }}</x-button>
                            <x-link
                                href="{{ legacy_url('checklists/tools/checklistloader.php?clid=' . $checklist->clid .'&pid=' . $checklist->pid) }}"
                                >{{ __('checklists_checklist.BATCH_LOAD_SPREADSHEET') }}</x-link
                            >
                        </form>
                    </x-slot>
                </x-modal>
            @endif
        </div>
    </div>

    <div class="relative pt-4">
        <div id="checklist-loader" class="htmx-indicator">
            <div class="bg-base-100 absolute h-full w-full opacity-70"></div>
            <div class="stroke-accent absolute top-30 right-0 left-0 mx-auto h-20 w-20 opacity-100 lg:h-30 lg:w-30">
                <x-icons.loading />
            </div>
        </div>
        @fragment('taxa-list')
            @if($show_images)
                <div class="flex flex-wrap justify-center gap-4" id="taxa-list">
                    @foreach($taxaList as $tid => $taxon)
                        <div class="bg-base-200 flex w-48 flex-col">
                            <img
                                class="h-72 w-48 object-cover"
                                loading="lazy"
                                src="{{ $taxon['tnurl'] ?? $taxon['url'] ?? '' }}"
                            />
                            <div class="text-neutral-content bg-neutral w-full grow-1 p-2 text-sm">
                                <x-link
                                    class="text-neutral-content hover:text-neutral-content/50"
                                    href="{{ url('taxon/' . $tid) }}"
                                >
                                    {{ $taxon['sciname'] ?? 'unknown' }}
                                </x-link>

                                @if($show_common && isset($taxon['vern']))
                                    <div class="font-bold">{{ $taxon['vern'] }}</div>
                                @endif

                                @if(isset($taxon['clid']))
                                    @foreach(explode(',',$taxon['clid']) as $id)
                                        @php
                    $editTitle = array_key_exists($id, $children)? $children[$id]: $checklist->name;
                    @endphp
                                        <a
                                            x-cloak
                                            x-show="sppEditToggle"
                                            target="_blank"
                                            href="{{ legacy_url('checklists/clsppeditor.php?tid=' . $tid . '&clid='. $id) }}"
                                            title="{{ __('checklists_checklist.EDIT_DETAILS') . ': ' . $editTitle }}"
                                        >
                                            <x-icons.edit class="text-neutral-content hover:text-neutral-content/50" />
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-checklist.taxa-list
                    :checklist="$checklist"
                    :taxa="$taxaList"
                    :taxa_vouchers="$voucherArr"
                    :children="$children"
                    :show_synonyms="$show_synonyms"
                    :show_common="$show_common"
                    :show_notes_vouchers="$show_notes_vouchers"
                    :show_taxa_authors="$show_taxa_authors"
                    :show_taxa_alphabetically="$show_taxa_alphabetically"
                    sppEditToggle="sppEditToggle"
                />
            @endif
        @endfragment
    </div>
</x-margin-layout>
