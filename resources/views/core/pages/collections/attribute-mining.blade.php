@props([
    'attrManager',
    'collid' => '',
    'collArr' => [],
    'fieldArr' => [],
    'fieldValues' => [],
    'fieldName' => '',
    'miningErrors',
    'stringFilter' => '',
    'taxonFilter' => '',
    'tidFilter' => 0,
    'traitArr' => [],
    'traitID' => 0,
])

@php
$currentUrl = url()->current();
$selectedCollIds = $collid && $collid !== 'all'
    ? array_values(array_filter(explode(',', $collid), 'is_numeric'))
    : [];
$traitNames = $attrManager->getTraitNames();
$traitItems = $traitNames
    ? itemize($traitNames)
    : [item(0, __('traitattr_attributemining.NO_ATTRI_AVAILABLE'), true)];
$fieldItems = itemize($fieldArr);
$statusItems = [
    item(0, '----------------------'),
    item(5, __('traitattr_attributemining.EXPERT NEEDED')),
];
$legacyTraitForm = '';
if($traitID && !empty($traitArr[$traitID])) {
    ob_start();
    $attrManager->echoFormTraits($traitID);
    $legacyTraitForm = ob_get_clean();
}

$breadcrumbs = [
    ['title' => __('header.H_HOME'), 'href' => url('')],
];

if (count($selectedCollIds) === 1) {
    $breadcrumbs[] = [
        'title' => __('traitattr_attributemining.COLLECTION_MANAGEMENT'),
        'href' => url('collections/' . $selectedCollIds[0]),
    ];
} elseif (Gate::check('COLL_EDIT_ANY')) {
    $breadcrumbs[] = [
        'title' => __('traitattr_attributemining.ADJUST_COLLECTION_SELECTION'),
        'href' => url('collections/traits/mining'),
    ];
}

$breadcrumbs[] = ['title' => __('traitattr_attributemining.ATTRI_MINING_TOOL')];
@endphp

@pushOnce('js-scripts')
    <script>
        function attributeMiningInputs(form, name) {
            const inputGroup = form?.elements[name];
            if (!inputGroup) return [];

            return inputGroup.length === undefined ? [inputGroup] : Array.from(inputGroup);
        }

        function attributeMiningRoot(elem) {
            return elem.closest("#traitdiv") || document.getElementById("traitdiv");
        }

        window.traitChanged = function traitChanged(elem) {
            const elemType = elem.getAttribute("type");
            const elemName = elem.getAttribute("name") || "";
            const traitID = elemName.substring(8, elemName.length - 2);
            const root = attributeMiningRoot(elem);

            attributeMiningInputs(elem.form, `traitid-${traitID}[]`).forEach((input) => {
                if (!input.checked) {
                    root.querySelectorAll(`input.child-${input.value}`).forEach((child) => {
                        child.checked = false;
                    });
                }
            });

            if ((elemType === "text" && elem.value.trim() !== "") || elem.checked) {
                let parent = elem.parentElement;
                while (parent && parent.id !== "traitdiv") {
                    const input = Array.from(parent.children).find((child) => child.matches?.("input"));
                    if (input) input.checked = true;
                    parent = parent.parentElement;
                }
            }

            if (!sessionStorage.attributeTree || sessionStorage.attributeTree === "0") {
                attributeMiningInputs(elem.form, `traitid-${traitID}[]`).forEach((input) => {
                    root.querySelectorAll(`div.child-${input.value}`).forEach((child) => {
                        const hasValue = elemType === "text" && elem.value.trim() !== "";
                        child.style.display = hasValue || input.checked ? "block" : "none";
                    });
                });
            }

            elem.form?.querySelectorAll('button[name="submitform"]').forEach((button) => {
                button.disabled = false;
            });
        };

        window.setAttributeTree = function setAttributeTree(triggerElem) {
            let treeOpen = sessionStorage.attributeTree === "1";
            if (triggerElem) treeOpen = !treeOpen;

            const root = triggerElem?.closest("fieldset") || document.getElementById("traitdiv");
            if (!root) return;

            root.querySelectorAll('div[class^="child"]').forEach((child) => {
                child.style.display = treeOpen ? "block" : "none";
            });
            root.querySelectorAll(".triangledown").forEach((icon) => {
                icon.style.display = treeOpen ? "inline" : "none";
            });
            root.querySelectorAll(".triangleright").forEach((icon) => {
                icon.style.display = treeOpen ? "none" : "inline";
            });

            sessionStorage.attributeTree = treeOpen ? "1" : "0";
        };

        function initAttributeMiningTree() {
            window.setAttributeTree(null);

            document.querySelectorAll("#traitdiv .trianglediv").forEach((toggle) => {
                const root = toggle.closest("fieldset");
                if (!root?.querySelector('div[class^="child"]')) {
                    toggle.style.display = "none";
                }
            });
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", initAttributeMiningTree);
        } else {
            initAttributeMiningTree();
        }
        document.addEventListener("htmx:afterSwap", initAttributeMiningTree);
    </script>
@endPushOnce

<x-margin-layout>
    <x-breadcrumbs :items="$breadcrumbs" />
    <x-page-title>{{ __('traitattr_attributemining.OCC_ATTRI_MINING_TOOL') }}</x-page-title>

    <x-errors :errors="$miningErrors" />

    @if($collid)
        @if($collid === 'all')
            <h2 class="text-xl font-bold">{{ __('traitattr_attributemining.SEARCH_ALL_COLLECTION') }}</h2>
        @elseif(count($selectedCollIds) === 1)
            <h2 class="text-xl font-bold">{{ $collArr[$selectedCollIds[0]] ?? '' }}</h2>
        @else
            <fieldset class="border-base-300 max-w-[700px] rounded-md border p-4" x-data="{ showCollections: false }">
                <legend class="px-1 text-lg font-bold">
                    <button type="button" class="cursor-pointer" @click="showCollections = !showCollections">
                        {{ __('traitattr_attributemining.SEARCHING') }} {{ count($selectedCollIds) }} Collections
                    </button>
                </legend>
                <div class="space-y-1 p-2" x-show="showCollections" x-cloak>
                    @foreach($selectedCollIds as $id)
                        <div>{{ $collArr[$id] ?? '' }}</div>
                    @endforeach
                </div>
                <button
                    type="button"
                    class="text-primary hover:text-primary-lighter cursor-pointer"
                    x-show="!showCollections"
                    @click="showCollections = true"
                >
                    {{ __('traitattr_attributemining.CLICK_DISPLAY_COLLEC_LIST') }}
                </button>
            </fieldset>
        @endif
        <div class="max-w-[700px] space-y-4">
            <div x-data="{ showDetails: false }">
                <p>
                    {{ __('traitattr_attributemining.OCC_TRAITS_MAPPING') }}
                    <button
                        type="button"
                        class="text-primary hover:text-primary-lighter cursor-pointer"
                        x-show="!showDetails"
                        @click="showDetails = true"
                    >
                        .. {{ __('taxa.MORE') }}
                    </button>
                </p>
                <p x-show="showDetails" x-cloak>
                    {{ __('traitattr_attributemining.PHENOLOGY_TRAIT_MAPPING') }}
                    <x-link
                        href="https://tools.gbif.org/dwca-validator/extension.do?id=http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact"
                        target="_blank"
                    >
                        {{ __('traitattr_attributemining.MEASUREMENT_OR_FACT') }}
                    </x-link>
                    {{ __('traitattr_attributemining.DWC_EXTENSION_FILE') }}
                </p>
            </div>

            <fieldset class="border-base-300 rounded-md border p-4">
                <legend class="px-1 text-lg font-bold">{{ __('traitattr_attributemining.HARVESTING_FILTER') }}</legend>
                <form method="POST" action="{{ $currentUrl }}" class="space-y-3">
                    @csrf
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="w-48">{{ __('traitattr_attributemining.OCC_TRAIT') }}</span>
                        <x-select
                            class="min-w-72"
                            id="traitid"
                            name="traitid"
                            :defaultValue="$traitID ?: null"
                            :items="$traitItems"
                            :select_text="__('traitattr_attributemining.SELECT_TARGET_TRAIT')"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="w-48">{{ __('traitattr_attributemining.VERBATIM_TEXT_SOURCE') }}</span>
                        <x-select
                            class="min-w-72"
                            id="fieldname"
                            name="fieldname"
                            :defaultValue="$fieldName ?: null"
                            :items="$fieldItems"
                            :select_text="__('traitattr_attributemining.SELECT_SOURCE_FIELD')"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <label
                            class="w-48"
                            for="stringfilter"
                            >{{ __('traitattr_attributemining.FILTER_BY_TEXT') }}</label
                        >
                        <x-input
                            class="w-72 grow-0"
                            id="stringfilter"
                            name="stringfilter"
                            type="text"
                            :value="$stringFilter"
                            inline
                        />
                    </div>

                    <div class="flex flex-wrap items-start gap-2">
                        <span class="w-48 pt-1">{{ __('traitattr_attributemining.FILTER_BY_TAXON') }}</span>
                        <x-taxa-search
                            id="taxonfilter"
                            name="taxonfilter"
                            tidName="tidfilter"
                            :taxa_value="$taxonFilter"
                            :tid_value="$tidFilter"
                            :hide_selector="true"
                            :hide_synonyms_checkbox="true"
                            label=""
                        />
                    </div>

                    <div class="flex justify-end">
                        <input name="collid" type="hidden" value="{{ $collid }}" />
                        <x-button id="filtersubmit" name="submitform" type="submit" value="Get Field Values">
                            {{ __('traitattr_attributemining.GET_FEILD_VALUE') }}
                        </x-button>
                    </div>
                </form>
            </fieldset>
        </div>
        @if($traitID && $fieldName)
            <div id="traitdiv" class="max-w-[700px]">
                <fieldset class="border-base-300 rounded-md border p-4">
                    <legend class="px-1 text-lg font-bold">{{ $fieldArr[$fieldName] }}</legend>
                    <form method="POST" action="{{ $currentUrl }}" class="space-y-4">
                        @csrf
                        <div>
                            <div class="font-bold">
                                {{ __('traitattr_attributemining.SELECT_SOURCE_FIELD_VALUES') }}
                                <span
                                    class="font-normal"
                                    >{{ __('traitattr_attributemining.HOLD_DOWN_BUTTONS_TO_SELECT') }}</span
                                >
                            </div>
                            <div class="border-base-content m-1 h-52 resize overflow-auto border-2">
                                <select name="fieldvalue[]" multiple class="h-full w-full cursor-pointer">
                                    @foreach($fieldValues as $value)
                                        @if($value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($legacyTraitForm)
                            <div class="flex items-start gap-4">
                                <div>{!! $legacyTraitForm !!}</div>
                                <div class="trianglediv ml-5">
                                    <button
                                        type="button"
                                        class="cursor-pointer"
                                        onclick="setAttributeTree(this)"
                                        title="{{ __('traitattr_attributemining.TOGGLE_ATTRI_TREE') }}"
                                    >
                                        <img
                                            class="triangleright inline w-[1.4em]"
                                            src="{{ legacy_url('/images/tochild.png') }}"
                                            alt="{{ __('traitattr_attributemining.TOGGLE_ATTRI_TREE') }}"
                                        />
                                        <img
                                            class="triangledown hidden w-[1.4em]"
                                            src="{{ legacy_url('/images/toparent.png') }}"
                                            alt="{{ __('traitattr_attributemining.TOGGLE_ATTRI_TREE') }}"
                                        />
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-2">
                            <label class="w-24" for="notes">{{ __('projects.NOTES') }}:</label>
                            <x-input class="w-64 grow-0" id="notes" name="notes" type="text" inline />
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="w-24">{{ __('taxonomy_batchloader.STATUS') }}:</span>
                            <x-select id="reviewstatus" name="reviewstatus" :default="0" :items="$statusItems" />
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <input name="stringfilter" type="hidden" value="{{ $stringFilter }}" />
                            <input name="taxonfilter" type="hidden" value="{{ $taxonFilter }}" />
                            <input name="tidfilter" type="hidden" value="{{ $tidFilter }}" />
                            <input name="traitid" type="hidden" value="{{ $traitID }}" />
                            <input name="fieldname" type="hidden" value="{{ $fieldName }}" />
                            <input name="collid" type="hidden" value="{{ $collid }}" />
                            <x-button name="submitform" type="submit" value="Batch Assign State(s)">
                                {{ __('traitattr_attributemining.BATCH_ASSIGN_STATE') }}
                            </x-button>
                            <x-button type="reset" variant="secondary">
                                {{ __('misc_collmetaresources.RESET_FORM') }}
                            </x-button>
                        </div>
                    </form>
                </fieldset>
            </div>
        @endif
    @else
        <div class="font-bold">{{ __('traitattr_attributemining.SELECT_COLLECTIONS') }}</div>
        <form
            method="POST"
            action="{{ url('collections/traits/mining') }}"
            class="m-4 space-y-2"
            x-data="{ selectAll: false }"
        >
            @csrf
            <label class="flex w-fit cursor-pointer items-center gap-2 font-bold">
                <input
                    name="selectall"
                    type="checkbox"
                    value="1"
                    class="accent-accent h-5 w-5 cursor-pointer"
                    x-model="selectAll"
                />
                {{ __('traitattr_attributemining.SELECT_DESELECT_ALL') }}
            </label>

            @foreach($collArr as $id => $collName)
                <label class="flex w-fit cursor-pointer items-center gap-2">
                    <input
                        name="collids[]"
                        type="checkbox"
                        value="{{ $id }}"
                        class="accent-accent h-5 w-5 cursor-pointer"
                        x-bind:checked="selectAll"
                        @change="if (!$el.checked) selectAll = false;"
                    />
                    {{ $collName }}
                </label>
            @endforeach

            <div class="pt-4">
                <x-button name="submitform" type="submit" value="Harvest from Collections">
                    {{ __('traitattr_attributemining.HARVEST_COLLECTIONS') }}
                </x-button>
            </div>
        </form>
    @endif
</x-margin-layout>
