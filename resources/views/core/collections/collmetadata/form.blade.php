@props([
    'collection' => [],
    'collid' => null,
    'fullCatArr' => [],
    'selectedCategories' => [],
    'rightsTerms' => [],
    'rightsState' => ['selected' => '', 'hasOrphan' => false],
    'showGbifPublishing' => false,
    'action' => null,
    'heading' => null,
    'submitAction' => null,
    'submitLabel' => null,
])

@php
    $rightsState = array_merge(['selected' => '', 'hasOrphan' => false], $rightsState);
    $isNewCollection = ! $collid;
    $displayValue = static fn ($value) => is_string($value)
        ? Purify::clean($value)
        : $value;

    $collectionName = old('collectionName', $displayValue($collection['collectionname'] ?? ''));
    $institutionCode = old('institutionCode', $displayValue($collection['institutioncode'] ?? ''));
    $collectionCode = old('collectionCode', $displayValue($collection['collectioncode'] ?? ''));
    $fullDescription = old('fullDescription', $displayValue($collection['fulldescription'] ?? ''));
    $latitudeDecimal = old('latitudeDecimal', $displayValue($collection['latitudedecimal'] ?? ''));
    $longitudeDecimal = old('longitudeDecimal', $displayValue($collection['longitudedecimal'] ?? ''));
    $selectedCategory = old('ccpk');
    $publicEdits = (bool) old('publicEdits', $collection['publicedits'] ?? 0);
    $rightsValue = old('rights', $rightsState['selected'] ?? '');
    $rightsHolder = old('rightsHolder', $displayValue($collection['rightsholder'] ?? ''));
    $accessRights = old('accessRights', $displayValue($collection['accessrights'] ?? ''));
    $collType = old('collType', $displayValue($collection['colltype'] ?? ''));
    $managementType = old('managementType', $displayValue($collection['managementtype'] ?? 'Snapshot'));
    $guidTarget = old('guidTarget', $displayValue($collection['guidtarget'] ?? 'occurrenceId'));
    $publishToGbif = (bool) old('publishToGbif', $collection['publishtogbif'] ?? 0);
    $individualUrl = old('individualUrl', $displayValue($collection['individualurl'] ?? ''));
    $iconUrl = old('iconUrl', $displayValue($collection['icon'] ?? ''));
    $sortSeq = old('sortSeq', $displayValue($collection['sortseq'] ?? ''));
    $collectionIdValue = old('collectionID', $displayValue($collection['collectionid'] ?? ''));
    $iconMode = $iconUrl ? 'url' : 'upload';

    $action ??= $collid
        ? route('collections.collmetadata.update', ['collid' => $collid])
        : route('collections.collmetadata.store');
    $heading ??= ($collid ? 'Edit' : 'Add New') . ' ' . __('misc_collmetadata.COL_INFO');
    $submitAction ??= $collid ? 'saveEdits' : 'newCollection';
    $submitLabel ??= $collid ? __('misc_collmetadata.SAVE_EDITS') : __('misc_collmetadata.CREATE_COLL_2');

    $infoIconClass = 'h-auto w-auto border-0 text-link-darker mt-1';
    $inputBaseClass = 'max-w-full rounded border bg-base-100 px-1 py-1.5';
    $inputWideClass = 'w-[42rem] ' . $inputBaseClass;
    $inputMediumClass = 'w-[25rem] ' . $inputBaseClass;
    $inputShortClass = 'w-[15rem] ' . $inputBaseClass;
    $labelClass = 'inline-flex mr-[1rem] items-center gap-1 font-bold';
    $fieldRowClass = 'my-2 flex flex-wrap items-center';
@endphp

@pushOnce('js-scripts')
    <script>
        function verifyCollectionForm(form) {
            if (form.managementType && form.managementType.value === "Snapshot" && form.guidTarget.value === "symbiotaUUID") {
                alert(@js(__('misc_sharedterms.CANNOT_GUID')));
                return false;
            }

            return verifyIconURL(form);
        }

        function managementTypeChanged(form) {
            const sourceUrlFields = document.querySelectorAll(".sourceurl-div");

            sourceUrlFields.forEach((field) => {
                if (form.managementType.value === "Live Data") {
                    field.classList.add("hidden");
                } else {
                    field.classList.remove("hidden");
                }
            });

            checkManagementTypeGuidSource(form);
        }

        function checkManagementTypeGuidSource(form) {
            if (form.managementType.value === "Snapshot" && form.guidTarget.value === "symbiotaUUID") {
                alert(@js(__('misc_sharedterms.CANNOT_GUID')));
                form.guidTarget.value = "";
            } else if (
                form.managementType.value === "Aggregate" &&
                form.guidTarget.value !== "" &&
                form.guidTarget.value !== "occurrenceId"
            ) {
                alert(@js(__('misc_sharedterms.AGG_GUID')));
                form.guidTarget.value = "occurrenceId";
            }

            if (!form.guidTarget.value && form.publishToGbif) {
                form.publishToGbif.checked = false;
            }
        }

        function checkGuidSource(form) {
            if (form.publishToGbif && form.publishToGbif.checked && !form.guidTarget.value) {
                alert(@js(__('misc_sharedterms.NEED_GUID')));
                form.publishToGbif.checked = false;
            }
        }

        function verifyIconImage() {
            const iconFile = document.getElementById("iconFile");

            if (!iconFile || !iconFile.value) {
                return;
            }

            const lowerValue = iconFile.value.toLowerCase();

            if (
                !lowerValue.endsWith(".jpg") &&
                !lowerValue.endsWith(".jpeg") &&
                !lowerValue.endsWith(".png") &&
                !lowerValue.endsWith(".gif")
            ) {
                iconFile.value = "";
                alert(@js(__('misc_sharedterms.NOT_SUPPORTED')));
                return;
            }

            const reader = new FileReader();
            reader.onload = function () {
                const image = new Image();

                image.onload = function () {
                    if (image.width > 500 || image.height > 500) {
                        iconFile.value = "";
                        alert(@js(__('misc_sharedterms.MUST_SMALL')));
                    }
                };

                image.src = reader.result;
            };

            reader.readAsDataURL(iconFile.files[0]);
        }

        function verifyIconURL(form) {
            if (!form.iconUrl || !form.iconUrl.value) {
                return true;
            }

            const lowerValue = form.iconUrl.value.toLowerCase();

            if (
                !lowerValue.endsWith(".jpg") &&
                !lowerValue.endsWith(".jpeg") &&
                !lowerValue.endsWith(".png") &&
                !lowerValue.endsWith(".gif")
            ) {
                alert(@js(__('misc_sharedterms.NOT_SUPPORTED')));
                return false;
            }

            return true;
        }

        function toggleFossilWarning() {
            const select = document.getElementById("collType");
            const warning = document.getElementById("fossilWarning");

            if (select && warning) {
                if (select.value === "Fossil Specimens") {
                    warning.classList.remove("hidden");
                } else {
                    warning.classList.add("hidden");
                }
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('input[name="managementType"]').forEach((radio) => {
                radio.addEventListener("change", () => {
                    if (radio.checked) {
                        managementTypeChanged(radio.form);
                    }
                });
            });

            document.querySelectorAll('input[name="guidTarget"]').forEach((radio) => {
                radio.addEventListener("change", () => {
                    if (radio.checked) {
                        checkManagementTypeGuidSource(radio.form);
                    }
                });
            });

            toggleFossilWarning();
        });
    </script>
@endPushOnce

<section class="bg-base-100 rounded border p-4">
    <h2 class="text-lg font-bold">{{ $heading }}</h2>

    <form
        method="POST"
        action="{{ $action }}"
        enctype="multipart/form-data"
        onsubmit="return verifyCollectionForm(this);"
    >
        @csrf

        {{-- Keep the main form close to the legacy field order so the old manager can handle the payload. --}}
        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label
                    :label="__('misc_collmetadata.INST_CODE')"
                    for="institutionCode"
                    :required="true"
                    inline
                />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <input
                    id="institutionCode"
                    name="institutionCode"
                    type="text"
                    value="{{ $institutionCode }}"
                    required
                    class="{{ $inputMediumClass }}"
                />
                <x-popover class="w-[26rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_INST_CODE') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_INST_CODE') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.NAME_ONE')) !!}
                        <x-link
                            href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode"
                            target="_blank"
                            >{{ __('misc_collmetadata.DWC_DEF') }}</x-link
                        >.
                    </div>
                </x-popover>
            </div>
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.COLL_CODE')" for="collectionCode" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <input
                    id="collectionCode"
                    name="collectionCode"
                    type="text"
                    value="{{ $collectionCode }}"
                    class="{{ $inputMediumClass }}"
                />
                <x-popover class="w-[26rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_COLL_CODE') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_COLL_CODE') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.NAME_ACRO')) !!}
                        <x-link
                            href="http://rs.tdwg.org/dwc/terms/index.htm#collectionCode"
                            target="_blank"
                            >{{ __('misc_collmetadata.DWC_DEF') }}</x-link
                        >.
                    </div>
                </x-popover>
            </div>
        </div>

        <div class="my-3">
            <x-form-label :label="__('misc_collmetadata.COLL_NAME')" for="collectionName" :required="true" inline />
            <input
                id="collectionName"
                name="collectionName"
                type="text"
                value="{{ $collectionName }}"
                required
                class="{{ $inputWideClass }}"
            />
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.DESC')" for="full-description" inline />
            </div>
            <x-rich-editor
                id="full-description"
                name="fullDescription"
                class="{{ $inputWideClass }} min-h-36"
                >{!! Purify::clean($fullDescription) !!}</x-rich-editor
            >
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.LAT')" for="latitudeDecimal" inline />
            </div>
            <div class="flex max-w-full items-start gap-2">
                <input
                    id="latitudeDecimal"
                    name="latitudeDecimal"
                    type="text"
                    value="{{ $latitudeDecimal }}"
                    class="{{ $inputShortClass }}"
                />
                <a
                    href="{{ legacy_url('/collections/tools/mappointaid.php?errmode=0') }}"
                    target="_blank"
                    class="{{ $infoIconClass }}"
                >
                    <i class="fas fa-globe"></i>
                </a>
            </div>
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.LONG')" for="longitudeDecimal" inline />
            </div>
            <input
                id="longitudeDecimal"
                name="longitudeDecimal"
                type="text"
                value="{{ $longitudeDecimal }}"
                class="{{ $inputShortClass }}"
            />
        </div>

        @if($fullCatArr)
            <div class="{{ $fieldRowClass }}">
                <div class="{{ $labelClass }}">
                    <x-form-label :label="__('misc_collmetadata.CATEGORY')" for="ccpk" inline />
                </div>
                <select id="ccpk" name="ccpk" class="{{ $inputMediumClass }}">
                    <option value="">{{ __('misc_collmetadata.NO_CATEGORY') }}</option>
                    <option value="">-------------------------------------------</option>
                    @foreach($fullCatArr as $ccpk => $category)
                        <option
                            value="{{ $ccpk }}"
                            @selected($selectedCategory !== null ? (string) $selectedCategory === (string) $ccpk : array_key_exists($ccpk, $selectedCategories))
                            >{{ $displayValue($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.ALLOW_PUBLIC_EDITS')" for="publicEdits" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <label class="inline-flex items-center gap-2">
                    <input id="publicEdits" type="checkbox" name="publicEdits" value="1" @checked($publicEdits) />
                </label>
                <x-popover class="w-[28rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_PUB_EDITS') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_PUB_EDITS') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.EXPLAIN_PUBLIC')) !!}</div>
                </x-popover>
            </div>
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.LICENSE')" for="rights" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                @if($rightsTerms)
                    <select id="rights" name="rights" class="{{ $inputWideClass }}">
                        @foreach($rightsTerms as $label => $value)
                            <option value="{{ $value }}" @selected($rightsValue === $value)>{{ $label }}</option>
                        @endforeach
                        @if($rightsState['hasOrphan'] && !empty($rightsState['selected']))
                            <option value="{{ $rightsState['selected'] }}" selected>
                                {{ $rightsState['selected'] }} [{{ __('misc_collmetadata.ORPHANED') }}]
                            </option>
                        @endif
                    </select>
                @else
                    <input
                        id="rights"
                        name="rights"
                        type="text"
                        value="{{ $rightsValue }}"
                        class="{{ $inputWideClass }}"
                    />
                @endif
                <x-popover class="w-[28rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_INFO_RIGHTS') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_INFO_RIGHTS') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.LEGAL_DOC')) !!}
                        <x-link
                            href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:license"
                            target="_blank"
                            >{{ __('misc_collmetadata.DWC_DEF') }}</x-link
                        >.
                    </div>
                </x-popover>
            </div>
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.RIGHTS_HOLDER')" for="rightsHolder" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <input
                    id="rightsHolder"
                    name="rightsHolder"
                    type="text"
                    value="{{ $rightsHolder }}"
                    class="{{ $inputWideClass }}"
                />
                <x-popover class="w-[28rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_INFO_RIGHTS_H') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_INFO_RIGHTS_H') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.HOLDER_DEF')) !!}
                        <x-link
                            href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:rightsHolder"
                            target="_blank"
                            >{{ __('misc_collmetadata.DWC_DEF') }}</x-link
                        >.
                    </div>
                </x-popover>
            </div>
        </div>

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.ACCESS_RIGHTS')" for="accessRights" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <input
                    id="accessRights"
                    name="accessRights"
                    type="text"
                    value="{{ $accessRights }}"
                    class="{{ $inputWideClass }}"
                />
                <x-popover class="w-[28rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_INFO_ACCESS_RIGHTS') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_INFO_ACCESS_RIGHTS') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.ACCESS_DEF')) !!}
                        <x-link
                            href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:accessRights"
                            target="_blank"
                            >{{ __('misc_collmetadata.DWC_DEF') }}</x-link
                        >.
                    </div>
                </x-popover>
            </div>
        </div>

        @can('SUPER_ADMIN')
            <div class="{{ $fieldRowClass }}">
                <div class="{{ $labelClass }}">
                    <x-form-label
                        :label="__('misc_collmetadata.DATASET_TYPE')"
                        for="collType"
                        :required="$isNewCollection"
                        inline
                    />
                </div>
                <div class="flex max-w-full flex-wrap items-start gap-2">
                    <select
                        id="collType"
                        name="collType"
                        onchange="toggleFossilWarning()"
                        class="{{ $inputMediumClass }}"
                    >
                        <option value="">{{ __('misc_collmetadata.SELECT_DATASET_TYPE') }}</option>
                        <option value="Preserved Specimens" @selected($collType === 'Preserved Specimens')
                            >{{ __('misc_collmetadata.PRES_SPECS') }}
                        </option>
                        <option value="Fossil Specimens" @selected($collType === 'Fossil Specimens')
                            >{{ __('misc_collmetadata.FOSSIL_SPECS') }}
                        </option>
                        <option value="Observations" @selected($collType === 'Observations')
                            >{{ __('misc_collmetadata.OBSERVATIONS') }}
                        </option>
                        <option value="General Observations" @selected($collType === 'General Observations')
                            >{{ __('misc_collmetadata.PERS_OBS_MAN') }}
                        </option>
                    </select>
                    <x-popover class="w-[28rem] text-sm">
                        <x-slot
                            name="icon"
                            class="{{ $infoIconClass }}"
                            title="{{ __('misc_collmetadata.MORE_COL_TYPE') }}"
                            aria-label="{{ __('misc_collmetadata.MORE_COL_TYPE') }}"
                        >
                            <i class="fa-regular fa-circle-question"></i>
                        </x-slot>
                        <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.COL_TYPE_DEF')) !!}</div>
                    </x-popover>
                    <div id="fossilWarning" class="text-error hidden basis-full text-sm">
                        {{ __('misc_collmetadata.FOSSIL_WARN_1') }}
                        <a
                            href="https://dwc.tdwg.org/terms/#dwc:basisOfRecord"
                            target="_blank"
                            class="underline underline-offset-2"
                            >dwc:basisOfRecord</a
                        >. {{ __('misc_collmetadata.FOSSIL_WARN_2') }} {{ __('misc_collmetadata.FOSSIL_WARN_3') }}
                    </div>
                </div>
            </div>
            <div class="bg-base-200 my-3 rounded border p-4">
                <div class="flex items-start gap-1">
                    <div class="font-bold">{{ __('misc_collmetadata.MANAGEMENT') }}:</div>
                    <x-popover class="w-[28rem] text-sm">
                        <x-slot
                            name="icon"
                            class="{{ $infoIconClass }}"
                            title="{{ __('misc_collmetadata.MORE_INFO_TYPE') }}"
                            aria-label="{{ __('misc_collmetadata.MORE_INFO_TYPE') }}"
                        >
                            <i class="fa-regular fa-circle-question"></i>
                        </x-slot>
                        <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.SNAPSHOT_DEF')) !!}</div>
                    </x-popover>
                </div>
                <div class="mt-2">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="managementType"
                            value="Snapshot"
                            @checked($managementType === 'Snapshot')
                        />
                        <span>{{ __('misc_collmetadata.SNAPSHOT') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="managementType"
                            value="Live Data"
                            @checked($managementType === 'Live Data')
                        />
                        <span>{{ __('misc_collmetadata.LIVE_DATA') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="managementType"
                            value="Aggregate"
                            @checked($managementType === 'Aggregate')
                        />
                        <span>{{ __('misc_collmetadata.AGGREGATE') }}</span>
                    </label>
                </div>
            </div>
        @endcan

        <div class="bg-base-200 my-3 rounded border p-4">
            <div class="flex items-start gap-1">
                <div class="font-bold">{{ __('misc_collmetadata.GUID_SOURCE') }}:</div>
                <x-popover class="w-[28rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_INFO_GUID') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_INFO_GUID') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.OCCID_DEF_1')) !!}
                        <x-link
                            href="http://rs.tdwg.org/dwc/terms/index.htm#occurrenceID"
                            target="_blank"
                            >{{ __('misc_collmetadata.OCCURRENCEID') }}</x-link
                        >
                        {!! Purify::clean(__('misc_collmetadata.OCCID_DEF_2')) !!}
                    </div>
                </x-popover>
            </div>
            <div class="mt-2">
                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="guidTarget"
                        value="occurrenceId"
                        @checked($guidTarget === 'occurrenceId')
                    />
                    <span>{{ __('misc_collmetadata.OCCURRENCEID') }}</span>
                </label>
                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="guidTarget"
                        value="catalogNumber"
                        @checked($guidTarget === 'catalogNumber')
                    />
                    <span>{{ __('misc_sharedterms.CAT_NUM') }}</span>
                </label>
                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="guidTarget"
                        value="symbiotaUUID"
                        @checked($guidTarget === 'symbiotaUUID')
                    />
                    <span>{{ __('misc_collmetadata.SYMB_GUID') }}</span>
                </label>
            </div>
        </div>

        @if($showGbifPublishing)
            <div class="{{ $fieldRowClass }}">
                <div class="{{ $labelClass }}">
                    <x-form-label :label="__('misc_collmetadata.PUBLISH_TO_AGGS')" for="publishToGbif" inline />
                </div>
                <div class="flex max-w-full flex-wrap items-start gap-2">
                    <label class="inline-flex items-center gap-2">
                        <span>GBIF</span>
                        <input
                            id="publishToGbif"
                            type="checkbox"
                            name="publishToGbif"
                            value="1"
                            onchange="checkGuidSource(this.form)"
                            @checked($publishToGbif)
                        />
                    </label>
                    <x-popover class="w-[28rem] text-sm">
                        <x-slot
                            name="icon"
                            class="{{ $infoIconClass }}"
                            title="{{ __('misc_collmetadata.MORE_INFO_AGGREGATORS') }}"
                            aria-label="{{ __('misc_collmetadata.MORE_INFO_AGGREGATORS') }}"
                        >
                            <i class="fa-regular fa-circle-question"></i>
                        </x-slot>
                        <div class="text-base-content">
                            {!! Purify::clean(__('misc_collmetadata.ACTIVATE_GBIF')) !!}.
                        </div>
                    </x-popover>
                </div>
            </div>
        @endif

        <div class="{{ $fieldRowClass }} sourceurl-div {{ $managementType === 'Live Data' ? 'hidden' : '' }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.SOURCE_REC_URL')" for="individualUrl" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <input
                    id="individualUrl"
                    name="individualUrl"
                    type="text"
                    value="{{ $individualUrl }}"
                    class="{{ $inputWideClass }}"
                />
                <x-popover class="w-[32rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.MORE_INFO_SOURCE') }}"
                        aria-label="{{ __('misc_collmetadata.MORE_INFO_SOURCE') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.ADVANCE_SETTING')) !!}:http://swbiodiversity.org/seinet/collections/individual/index.php?occid=--DBPK--" {!! Purify::clean(__('misc_collmetadata.ADVANCE_SETTING_2')) !!} "http://www.inaturalist.org/observations/--DBPK--" {!! Purify::clean(__('misc_collmetadata.ADVANCE_SETTING_3')) !!}
                    </div>
                </x-popover>
            </div>
        </div>

        <div class="{{ $fieldRowClass }}" x-data="{ iconMode: '{{ $iconMode }}' }">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.ICON_URL')" for="iconFile" inline />
            </div>

            <div class="flex max-w-full flex-wrap items-start gap-2">
                <div x-cloak x-show="iconMode === 'upload'">
                    <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
                    <input
                        id="iconFile"
                        name="iconFile"
                        type="file"
                        onchange="verifyIconImage()"
                        class="{{ $inputWideClass }}"
                    />
                </div>

                <div x-cloak x-show="iconMode === 'url'">
                    <input
                        id="iconUrl"
                        name="iconUrl"
                        type="text"
                        value="{{ $iconUrl }}"
                        onchange="verifyIconURL(this.form)"
                        class="{{ $inputWideClass }}"
                    />
                </div>
                <x-popover class="w-[28rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.WHAT_ICON') }}"
                        aria-label="{{ __('misc_collmetadata.WHAT_ICON') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.UPLOAD_ICON')) !!}</div>
                </x-popover>

                <button
                    type="button"
                    @click="iconMode = iconMode === 'upload' ? 'url' : 'upload'"
                    class="text-link-darker cursor-pointer pt-1.5 underline underline-offset-2"
                >
                    <span x-show="iconMode === 'upload'">{{ __('misc_collmetadata.ENTER_URL') }}</span>
                    <span x-show="iconMode === 'url'">{{ __('misc_collmetadata.UPLOAD_LOCAL') }}</span>
                </button>
            </div>
        </div>

        @can('SUPER_ADMIN')
            <div class="{{ $fieldRowClass }}">
                <div class="{{ $labelClass }}">
                    <x-form-label :label="__('misc_collmetadata.SORT_SEQUENCE')" for="sortSeq" inline />
                </div>
                <div class="flex max-w-full flex-wrap items-start gap-2">
                    <input
                        id="sortSeq"
                        name="sortSeq"
                        type="text"
                        value="{{ $sortSeq }}"
                        class="{{ $inputShortClass }}"
                    />
                    <x-popover class="w-[28rem] text-sm">
                        <x-slot
                            name="icon"
                            class="{{ $infoIconClass }}"
                            title="{{ __('misc_collmetadata.MORE_SORTING') }}"
                            aria-label="{{ __('misc_collmetadata.MORE_SORTING') }}"
                        >
                            <i class="fa-regular fa-circle-question"></i>
                        </x-slot>
                        <div class="text-base-content">
                            {!! Purify::clean(__('misc_collmetadata.LEAVE_IF_ALPHABET')) !!}
                        </div>
                    </x-popover>
                </div>
            </div>
        @endcan

        <div class="{{ $fieldRowClass }}">
            <div class="{{ $labelClass }}">
                <x-form-label :label="__('misc_collmetadata.COLLECTION_ID')" for="collectionID" inline />
            </div>
            <div class="flex max-w-full flex-wrap items-start gap-2">
                <input
                    id="collectionID"
                    name="collectionID"
                    type="text"
                    value="{{ $collectionIdValue }}"
                    class="{{ $inputMediumClass }}"
                />
                <x-popover class="w-[32rem] text-sm">
                    <x-slot
                        name="icon"
                        class="{{ $infoIconClass }}"
                        title="{{ __('misc_collmetadata.COLLECTION_ID') }}"
                        aria-label="{{ __('misc_collmetadata.COLLECTION_ID') }}"
                    >
                        <i class="fa-regular fa-circle-question"></i>
                    </x-slot>
                    <div class="text-base-content">
                        {!! Purify::clean(__('misc_collmetadata.EXPLAIN_COLLID')) !!}
                        <x-link
                            href="https://dwc.tdwg.org/terms/#dwc:collectionID"
                            target="_blank"
                            >{{ __('misc_collmetadata.DWC_COLLID') }}</x-link
                        >): {!! Purify::clean(__('misc_collmetadata.EXPLAIN_COLLID_2')) !!}
                    </div>
                </x-popover>
            </div>
        </div>

        @if($collid)
            <div class="{{ $fieldRowClass }}">
                <div class="{{ $labelClass }}">{{ __('misc_collmetadata.SECURITY_KEY') }}:</div>
                <div class="text-base-content/70 min-h-9 max-w-full">{{ $collection['securitykey'] ?? '' }}</div>
            </div>
            <div class="{{ $fieldRowClass }}">
                <div class="{{ $labelClass }}">{{ __('misc_collmetadata.RECORDID') }}:</div>
                <div class="text-base-content/70 min-h-9 max-w-full">{{ $collection['recordid'] ?? '' }}</div>
            </div>
        @endif

        <div class="pt-2">
            @if($collid)
                <input type="hidden" name="securityKey" value="{{ $collection['securitykey'] ?? '' }}" />
                <input type="hidden" name="recordID" value="{{ $collection['recordid'] ?? '' }}" />
            @endif

            <x-button type="submit" name="action" value="{{ $submitAction }}"> {{ $submitLabel }} </x-button>
        </div>
    </form>
</section>
