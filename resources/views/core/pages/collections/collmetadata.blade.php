@php
    $collection = $collection ?? [];
    $collid = $collid ?? null;
    $fullCatArr = $fullCatArr ?? [];
    $selectedCategories = $selectedCategories ?? [];
    $resourceLinks = $resourceLinks ?? [];
    $contacts = $contacts ?? [];
    $resourceJson = $resourceJson ?? '';
    $contactJson = $contactJson ?? '';
    $address = $address ?? [];
    $institutionOptions = $institutionOptions ?? [];
    $languageCodes = $languageCodes ?? ['en'];
    $rightsTerms = $rightsTerms ?? [];
    $rightsState = $rightsState ?? ['selected' => '', 'hasOrphan' => false];
    $showGbifPublishing = $showGbifPublishing ?? false;
    $tabIndex = $tabIndex ?? 0;
    $isNewCollection = ! $collid;
    $displayValue = static fn ($value) => is_string($value)
        ? html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')
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
    $languageLabelMap = [
        'en' => __('misc_collmetaresources.ENGLISH'),
        'es' => __('misc_collmetaresources.SPANISH'),
        'fr' => __('misc_collmetaresources.FRENCH'),
        'pt' => __('misc_collmetaresources.PORTUGUESE'),
        'pr' => __('misc_collmetaresources.PORTUGUESE'),
    ];

    $tabs = [__('misc_collmetadata.COL_META_EDIT')];
    if ($collid) {
        $tabs[] = __('misc_collmetadata.CONT_RES');
    }

    $breadcrumbs = [
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('misc_collmetadata.COL_PROFS'), 'href' => url('collections')],
    ];

    if ($collid) {
        $breadcrumbs[] = ['title' => $collectionName, 'href' => url('collections/' . $collid)];
        $breadcrumbs[] = ['title' => __('misc_collmetadata.META_EDIT')];
    } else {
        $breadcrumbs[] = ['title' => __('misc_collmetadata.CREATE_COLL')];
    }

    $collmetadataAction = $collid
        ? route('collections.collmetadata.update', ['collid' => $collid])
        : route('collections.collmetadata.store');

    $infoIconClass = 'h-auto w-auto border-0 text-link-darker mt-1';
    $inputBaseClass = 'max-w-full rounded border bg-base-100 px-1 py-1.5';
    $inputWideClass = 'w-[42rem] ' . $inputBaseClass;
    $inputMediumClass = 'w-[25rem] ' . $inputBaseClass;
    $inputShortClass = 'w-[15rem] ' . $inputBaseClass;
    $labelClass = 'inline-flex mr-[1rem] items-center gap-1 font-bold';
    $fieldRowClass = 'my-2 flex flex-wrap items-center';
@endphp

<script>
    function verifyCollectionForm(form) {
        if (form.managementType && form.managementType.value === 'Snapshot' && form.guidTarget.value === 'symbiotaUUID') {
            alert(@js(__('misc_sharedterms.CANNOT_GUID')));
            return false;
        }

        return verifyIconURL(form);
    }

    function managementTypeChanged(form) {
        const sourceUrlFields = document.querySelectorAll('.sourceurl-div');

        sourceUrlFields.forEach((field) => {
            if (form.managementType.value === 'Live Data') {
                field.classList.add('hidden');
            } else {
                field.classList.remove('hidden');
            }
        });

        checkManagementTypeGuidSource(form);
    }

    function checkManagementTypeGuidSource(form) {
        if (form.managementType.value === 'Snapshot' && form.guidTarget.value === 'symbiotaUUID') {
            alert(@js(__('misc_sharedterms.CANNOT_GUID')));
            form.guidTarget.value = '';
        } else if (form.managementType.value === 'Aggregate' && form.guidTarget.value !== '' && form.guidTarget.value !== 'occurrenceId') {
            alert(@js(__('misc_sharedterms.AGG_GUID')));
            form.guidTarget.value = 'occurrenceId';
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
        const iconFile = document.getElementById('iconFile');

        if (!iconFile || !iconFile.value) {
            return;
        }

        const lowerValue = iconFile.value.toLowerCase();

        if (!lowerValue.endsWith('.jpg') && !lowerValue.endsWith('.jpeg') && !lowerValue.endsWith('.png') && !lowerValue.endsWith('.gif')) {
            iconFile.value = '';
            alert(@js(__('misc_sharedterms.NOT_SUPPORTED')));
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            const image = new Image();

            image.onload = function () {
                if (image.width > 500 || image.height > 500) {
                    iconFile.value = '';
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

        if (!lowerValue.endsWith('.jpg') && !lowerValue.endsWith('.jpeg') && !lowerValue.endsWith('.png') && !lowerValue.endsWith('.gif')) {
            alert(@js(__('misc_sharedterms.NOT_SUPPORTED')));
            return false;
        }

        return true;
    }

    let resourceJSON = [];
    let contactJSON = [];
    const languageCodes = [
        @foreach($languageCodes as $code)
            '{{ $code }}'{{ $loop->last ? '' : ',' }}
        @endforeach
    ];

    function addLink(form) {
        const jsonObj = getLinkFormObject(form);
        if (jsonObj) {
            resourceJSON.push(jsonObj);
            submitResourceForm();
        }
    }

    function editLink(linkIndex) {
        const form = document.linkForm;
        clearLinkForm();
        form.url.value = resourceJSON[linkIndex].url;

        languageCodes.forEach((code) => {
            const titleValue = resourceJSON[linkIndex].title?.[code];
            if (titleValue !== undefined && form['title-' + code]) {
                form['title-' + code].value = titleValue;
            }
        });

        form.linkIndex.value = linkIndex;
        document.getElementById('add-link-div')?.classList.add('hidden');
        document.getElementById('edit-link-div')?.classList.remove('hidden');
    }

    function applyEdits(form) {
        const linkIndex = form.linkIndex.value;

        if (linkIndex !== '') {
            const jsonObj = getLinkFormObject(form);
            if (jsonObj) {
                resourceJSON[linkIndex] = jsonObj;
                submitResourceForm();
            }
        }
    }

    function getLinkFormObject(form) {
        if (!form.reportValidity()) {
            return null;
        }

        const jsonObj = {title: {}, url: form.url.value.trim()};

        languageCodes.forEach((code) => {
            if (form['title-' + code] && form['title-' + code].value.trim() !== '') {
                jsonObj.title[code] = form['title-' + code].value.trim();
            }
        });

        clearLinkForm();

        return jsonObj;
    }

    function deleteLink(linkIndex) {
        resourceJSON.splice(linkIndex, 1);
        submitResourceForm();
    }

    function clearLinkForm() {
        const form = document.linkForm;

        if (!form) {
            return;
        }

        form.url.value = '';
        languageCodes.forEach((code) => {
            if (form['title-' + code]) {
                form['title-' + code].value = '';
            }
        });

        form.linkIndex.value = '';
        document.getElementById('add-link-div')?.classList.remove('hidden');
        document.getElementById('edit-link-div')?.classList.add('hidden');
    }

    function submitResourceForm() {
        const form = document.resourceLinkForm;

        if (!form) {
            return;
        }

        form.resourcejson.value = JSON.stringify(resourceJSON);
        form.submit();
    }

    function editContact(contactIndex) {
        const form = document.contactEditForm;

        if (!form || !contactJSON[contactIndex]) {
            return;
        }

        form.contactIndex.value = contactIndex;
        form.firstName.value = contactJSON[contactIndex].firstName ?? '';
        form.lastName.value = contactJSON[contactIndex].lastName ?? '';
        form.role.value = contactJSON[contactIndex].role ?? '';
        form.email.value = contactJSON[contactIndex].email ?? '';
        form.centralContact.checked = !!contactJSON[contactIndex].centralContact;
        form.phone.value = contactJSON[contactIndex].phone ?? '';
        form.orcid.value = contactJSON[contactIndex].orcid ?? '';

        document.getElementById('addContact-span')?.classList.add('hidden');
        document.getElementById('editContact-span')?.classList.remove('hidden');
    }

    function resetContactForm() {
        document.getElementById('editContact-span')?.classList.add('hidden');
        document.getElementById('addContact-span')?.classList.remove('hidden');
    }

    function toggleFossilWarning() {
        const select = document.getElementById('collType');
        const warning = document.getElementById('fossilWarning');

        if (select && warning) {
            if (select.value === 'Fossil Specimens') {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const resourceElement = document.getElementById('resourceJsonInput');
        const contactDataContainer = document.getElementById('contact-json-data');

        if (resourceElement && resourceElement.value) {
            try {
                resourceJSON = JSON.parse(resourceElement.value);
            } catch (error) {
                console.error(error);
            }
        }

        if (contactDataContainer && contactDataContainer.dataset.contactJson) {
            try {
                contactJSON = JSON.parse(contactDataContainer.dataset.contactJson);
            } catch (error) {
                console.error(error);
            }
        }

        document.querySelectorAll('input[name=\"managementType\"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    managementTypeChanged(radio.form);
                }
            });
        });

        document.querySelectorAll('input[name=\"guidTarget\"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    checkManagementTypeGuidSource(radio.form);
                }
            });
        });

        toggleFossilWarning();
    });
</script>

<x-margin-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>

    <div class="collmetadata-page">
        @if(session('status'))
            <div class="rounded border px-4 py-3 {{ session('statusType') === 'success' ? 'text-success' : 'text-error' }}">
                {!! Purify::clean(session('status')) !!}
            </div>
        @endif

        @if($collid)
            <h1 class="text-2xl font-bold">
                {{ __('misc_collmetadata.EDIT_METADATA') }}: {{ $collectionName }}
                @if($institutionCode !== '')
                    ({{ $institutionCode }})
                @endif
            </h1>
        @else
            <h1 class="text-2xl font-bold">{{ __('misc_collmetadata.CREATE_COLL') }}</h1>
        @endif

        <x-tabs :tabs="$tabs" :active="$tabIndex">
            <div>
                <section class="rounded border  bg-base-100 p-4">
                    <h2 class="text-lg font-bold">{{ ($collid ? 'Edit' : 'Add New') . ' ' . __('misc_collmetadata.COL_INFO') }}</h2>

                    <form
                        method="POST"
                        action="{{ $collmetadataAction }}"
                        enctype="multipart/form-data"
                        onsubmit="return verifyCollectionForm(this)"
                    >
                        @csrf

                        {{-- Keep the main form close to the legacy field order so the old manager can handle the payload. --}}
                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="institutionCode">{{ __('misc_collmetadata.INST_CODE') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <input id="institutionCode" name="institutionCode" type="text" value="{{ $institutionCode }}" required class="{{ $inputMediumClass }}" />
                                <x-popover class="w-[26rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INST_CODE') }}" aria-label="{{ __('misc_collmetadata.MORE_INST_CODE') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.NAME_ONE')) !!}
                                        <x-link href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode" target="_blank">{{ __('misc_collmetadata.DWC_DEF') }}</x-link>.
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="collectionCode">{{ __('misc_collmetadata.COLL_CODE') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <input id="collectionCode" name="collectionCode" type="text" value="{{ $collectionCode }}" class="{{ $inputMediumClass }}" />
                                <x-popover class="w-[26rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_COLL_CODE') }}" aria-label="{{ __('misc_collmetadata.MORE_COLL_CODE') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.NAME_ACRO')) !!}
                                        <x-link href="http://rs.tdwg.org/dwc/terms/index.htm#collectionCode" target="_blank">{{ __('misc_collmetadata.DWC_DEF') }}</x-link>.
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        <div class="my-3">
                            <label for="collectionName" class="block font-bold">{{ __('misc_collmetadata.COLL_NAME') }}:</label>
                            <input id="collectionName" name="collectionName" type="text" value="{{ $collectionName }}" required class="{{ $inputWideClass }}" />
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <label for="full-description" class="{{ $labelClass }}">{{ __('misc_collmetadata.DESC') }}:</label>
                            <x-rich-editor id="full-description" name="fullDescription" class="{{ $inputWideClass }} min-h-36">{!! Purify::clean($fullDescription) !!}</x-rich-editor>
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <label for="latitudeDecimal" class="{{ $labelClass }}">{{ __('misc_collmetadata.LAT') }}:</label>
                            <div class="max-w-full flex items-start gap-2">
                                <input id="latitudeDecimal" name="latitudeDecimal" type="text" value="{{ $latitudeDecimal }}" class="{{ $inputShortClass }}" />
                                <a href="{{ legacy_url('/collections/tools/mappointaid.php?errmode=0') }}" target="_blank" class="{{ $infoIconClass }}">
                                    <i class="fas fa-globe"></i>
                                </a>
                            </div>
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <label for="longitudeDecimal" class="{{ $labelClass }}">{{ __('misc_collmetadata.LONG') }}:</label>
                            <input id="longitudeDecimal" name="longitudeDecimal" type="text" value="{{ $longitudeDecimal }}" class="{{ $inputShortClass }}" />
                        </div>

                        @if($fullCatArr)
                            <div class="{{ $fieldRowClass }}">
                                <label for="ccpk" class="{{ $labelClass }}">{{ __('misc_collmetadata.CATEGORY') }}:</label>
                                <select id="ccpk" name="ccpk" class="{{ $inputMediumClass }}">
                                    <option value="">{{ __('misc_collmetadata.NO_CATEGORY') }}</option>
                                    <option value="">-------------------------------------------</option>
                                    @foreach($fullCatArr as $ccpk => $category)
                                        <option value="{{ $ccpk }}" @selected($selectedCategory !== null ? (string) $selectedCategory === (string) $ccpk : array_key_exists($ccpk, $selectedCategories))>{{ $displayValue($category) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="publicEdits">{{ __('misc_collmetadata.ALLOW_PUBLIC_EDITS') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <label class="inline-flex items-center gap-2">
                                    <input id="publicEdits" type="checkbox" name="publicEdits" value="1" @checked($publicEdits) />
                                </label>
                                <x-popover class="w-[28rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_PUB_EDITS') }}" aria-label="{{ __('misc_collmetadata.MORE_PUB_EDITS') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.EXPLAIN_PUBLIC')) !!}</div>
                                </x-popover>
                            </div>
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="rights">{{ __('misc_collmetadata.LICENSE') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                @if($rightsTerms)
                                    <select id="rights" name="rights" class="{{ $inputWideClass }}">
                                        @foreach($rightsTerms as $label => $value)
                                            <option value="{{ $value }}" @selected($rightsValue === $value)>{{ $label }}</option>
                                        @endforeach
                                        @if($rightsState['hasOrphan'] && !empty($rightsState['selected']))
                                            <option value="{{ $rightsState['selected'] }}" selected>{{ $rightsState['selected'] }} [{{ __('misc_collmetadata.ORPHANED') }}]</option>
                                        @endif
                                    </select>
                                @else
                                    <input id="rights" name="rights" type="text" value="{{ $rightsValue }}" class="{{ $inputWideClass }}" />
                                @endif
                                <x-popover class="w-[28rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_RIGHTS') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_RIGHTS') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.LEGAL_DOC')) !!}
                                        <x-link href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:license" target="_blank">{{ __('misc_collmetadata.DWC_DEF') }}</x-link>.
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="rightsHolder">{{ __('misc_collmetadata.RIGHTS_HOLDER') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <input id="rightsHolder" name="rightsHolder" type="text" value="{{ $rightsHolder }}" class="{{ $inputWideClass }}" />
                                <x-popover class="w-[28rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_RIGHTS_H') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_RIGHTS_H') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.HOLDER_DEF')) !!}
                                        <x-link href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:rightsHolder" target="_blank">{{ __('misc_collmetadata.DWC_DEF') }}</x-link>.
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="accessRights">{{ __('misc_collmetadata.ACCESS_RIGHTS') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <input id="accessRights" name="accessRights" type="text" value="{{ $accessRights }}" class="{{ $inputWideClass }}" />
                                <x-popover class="w-[28rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_ACCESS_RIGHTS') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_ACCESS_RIGHTS') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.ACCESS_DEF')) !!}
                                        <x-link href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:accessRights" target="_blank">{{ __('misc_collmetadata.DWC_DEF') }}</x-link>.
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        @can('SUPER_ADMIN')
                            <div class="{{ $fieldRowClass }}">
                                <div class="{{ $labelClass }}">
                                    <label for="collType">{{ __('misc_collmetadata.DATASET_TYPE') }}:</label>
                                </div>
                                <div class="max-w-full flex flex-wrap items-start gap-2">
                                    <select id="collType" name="collType" onchange="toggleFossilWarning()" class="{{ $inputMediumClass }}">
                                        <option value="">{{ __('misc_collmetadata.SELECT_DATASET_TYPE') }}</option>
                                        <option value="Preserved Specimens" @selected($collType === 'Preserved Specimens')>{{ __('misc_collmetadata.PRES_SPECS') }}</option>
                                        <option value="Fossil Specimens" @selected($collType === 'Fossil Specimens')>{{ __('misc_collmetadata.FOSSIL_SPECS') }}</option>
                                        <option value="Observations" @selected($collType === 'Observations')>{{ __('misc_collmetadata.OBSERVATIONS') }}</option>
                                        <option value="General Observations" @selected($collType === 'General Observations')>{{ __('misc_collmetadata.PERS_OBS_MAN') }}</option>
                                    </select>
                                    <x-popover class="w-[28rem] text-sm">
                                        <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_COL_TYPE') }}" aria-label="{{ __('misc_collmetadata.MORE_COL_TYPE') }}">
                                            <i class="fa-regular fa-circle-question"></i>
                                        </x-slot>
                                        <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.COL_TYPE_DEF')) !!}</div>
                                    </x-popover>
                                    <div id="fossilWarning" class="hidden basis-full text-sm text-error">
                                        {{ __('misc_collmetadata.FOSSIL_WARN_1') }}
                                        <a href="https://dwc.tdwg.org/terms/#dwc:basisOfRecord" target="_blank" class="underline underline-offset-2">dwc:basisOfRecord</a>.
                                        {{ __('misc_collmetadata.FOSSIL_WARN_2') }} {{ __('misc_collmetadata.FOSSIL_WARN_3') }}
                                    </div>
                                </div>
                            </div>

                            <div class="my-3 rounded border  bg-base-200 p-4">
                                <div class="flex items-start gap-1">
                                    <div class="font-bold">{{ __('misc_collmetadata.MANAGEMENT') }}:</div>
                                    <x-popover class="w-[28rem] text-sm">
                                        <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_TYPE') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_TYPE') }}">
                                            <i class="fa-regular fa-circle-question"></i>
                                        </x-slot>
                                        <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.SNAPSHOT_DEF')) !!}</div>
                                    </x-popover>
                                </div>
                                <div class="mt-2">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="managementType" value="Snapshot" @checked($managementType === 'Snapshot') />
                                        <span>{{ __('misc_collmetadata.SNAPSHOT') }}</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="managementType" value="Live Data" @checked($managementType === 'Live Data') />
                                        <span>{{ __('misc_collmetadata.LIVE_DATA') }}</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="managementType" value="Aggregate" @checked($managementType === 'Aggregate') />
                                        <span>{{ __('misc_collmetadata.AGGREGATE') }}</span>
                                    </label>
                                </div>
                            </div>
                        @endcan

                        <div class="my-3 rounded border  bg-base-200 p-4">
                            <div class="flex items-start gap-1">
                                <div class="font-bold">{{ __('misc_collmetadata.GUID_SOURCE') }}:</div>
                                <x-popover class="w-[28rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_GUID') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_GUID') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.OCCID_DEF_1')) !!}
                                        <x-link href="http://rs.tdwg.org/dwc/terms/index.htm#occurrenceID" target="_blank">{{ __('misc_collmetadata.OCCURRENCEID') }}</x-link>
                                        {!! Purify::clean(__('misc_collmetadata.OCCID_DEF_2')) !!}
                                    </div>
                                </x-popover>
                            </div>  
                            <div class="mt-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="guidTarget" value="occurrenceId" @checked($guidTarget === 'occurrenceId') />
                                    <span>{{ __('misc_collmetadata.OCCURRENCEID') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="guidTarget" value="catalogNumber" @checked($guidTarget === 'catalogNumber') />
                                    <span>{{ __('misc_sharedterms.CAT_NUM') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="guidTarget" value="symbiotaUUID" @checked($guidTarget === 'symbiotaUUID') />
                                    <span>{{ __('misc_collmetadata.SYMB_GUID') }}</span>
                                </label>
                            </div>
                        </div>

                        @if($showGbifPublishing)
                            <div class="{{ $fieldRowClass }}">
                                <div class="{{ $labelClass }}">
                                    <label for="publishToGbif">{{ __('misc_collmetadata.PUBLISH_TO_AGGS') }}:</label>
                                </div>
                                <div class="max-w-full flex flex-wrap items-start gap-2">
                                    <label class="inline-flex items-center gap-2">
                                        <span>GBIF</span>
                                        <input id="publishToGbif" type="checkbox" name="publishToGbif" value="1" onchange="checkGuidSource(this.form)" @checked($publishToGbif) />
                                    </label>
                                    <x-popover class="w-[28rem] text-sm">
                                        <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_AGGREGATORS') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_AGGREGATORS') }}">
                                            <i class="fa-regular fa-circle-question"></i>
                                        </x-slot>
                                        <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.ACTIVATE_GBIF')) !!}.</div>
                                    </x-popover>
                                </div>
                            </div>
                        @endif

                        <div class="{{ $fieldRowClass }} sourceurl-div {{ $managementType === 'Live Data' ? 'hidden' : '' }}">
                            <div class="{{ $labelClass }}">
                                <label for="individualUrl">{{ __('misc_collmetadata.SOURCE_REC_URL') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <input id="individualUrl" name="individualUrl" type="text" value="{{ $individualUrl }}" class="{{ $inputWideClass }}" />
                                <x-popover class="w-[32rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_INFO_SOURCE') }}" aria-label="{{ __('misc_collmetadata.MORE_INFO_SOURCE') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.ADVANCE_SETTING')) !!}:http://swbiodiversity.org/seinet/collections/individual/index.php?occid=--DBPK--"
                                        {!! Purify::clean(__('misc_collmetadata.ADVANCE_SETTING_2')) !!}
                                        "http://www.inaturalist.org/observations/--DBPK--"
                                        {!! Purify::clean(__('misc_collmetadata.ADVANCE_SETTING_3')) !!}
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        <div class="{{ $fieldRowClass }}" x-data="{ iconMode: '{{ $iconMode }}' }">
                            <div class="{{ $labelClass }}">
                                <label for="iconFile">{{ __('misc_collmetadata.ICON_URL') }}:</label>
                            </div>

                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <div x-cloak x-show="iconMode === 'upload'">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
                                    <input id="iconFile" name="iconFile" type="file" onchange="verifyIconImage()" class="{{ $inputWideClass }}" />
                                </div>

                                <div x-cloak x-show="iconMode === 'url'">
                                    <input id="iconUrl" name="iconUrl" type="text" value="{{ $iconUrl }}" onchange="verifyIconURL(this.form)" class="{{ $inputWideClass }}" />
                                </div>
                                <x-popover class="w-[28rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.WHAT_ICON') }}" aria-label="{{ __('misc_collmetadata.WHAT_ICON') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.UPLOAD_ICON')) !!}</div>
                                </x-popover>

                                <button type="button" @click="iconMode = iconMode === 'upload' ? 'url' : 'upload'" class="cursor-pointer pt-1.5 text-link-darker underline underline-offset-2">
                                    <span x-show="iconMode === 'upload'">{{ __('misc_collmetadata.ENTER_URL') }}</span>
                                    <span x-show="iconMode === 'url'">{{ __('misc_collmetadata.UPLOAD_LOCAL') }}</span>
                                </button>
                            </div>
                        </div>

                        @can('SUPER_ADMIN')
                            <div class="{{ $fieldRowClass }}">
                                <div class="{{ $labelClass }}">
                                    <label for="sortSeq">{{ __('misc_collmetadata.SORT_SEQUENCE') }}:</label>
                                </div>
                                <div class="max-w-full flex flex-wrap items-start gap-2">
                                    <input id="sortSeq" name="sortSeq" type="text" value="{{ $sortSeq }}" class="{{ $inputShortClass }}" />
                                    <x-popover class="w-[28rem] text-sm">
                                        <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.MORE_SORTING') }}" aria-label="{{ __('misc_collmetadata.MORE_SORTING') }}">
                                            <i class="fa-regular fa-circle-question"></i>
                                        </x-slot>
                                        <div class="text-base-content">{!! Purify::clean(__('misc_collmetadata.LEAVE_IF_ALPHABET')) !!}</div>
                                    </x-popover>
                                </div>
                            </div>
                        @endcan

                        <div class="{{ $fieldRowClass }}">
                            <div class="{{ $labelClass }}">
                                <label for="collectionID">{{ __('misc_collmetadata.COLLECTION_ID') }}:</label>
                            </div>
                            <div class="max-w-full flex flex-wrap items-start gap-2">
                                <input id="collectionID" name="collectionID" type="text" value="{{ $collectionIdValue }}" class="{{ $inputMediumClass }}" />
                                <x-popover class="w-[32rem] text-sm">
                                    <x-slot name="icon" class="{{ $infoIconClass }}" title="{{ __('misc_collmetadata.COLLECTION_ID') }}" aria-label="{{ __('misc_collmetadata.COLLECTION_ID') }}">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </x-slot>
                                    <div class="text-base-content">
                                        {!! Purify::clean(__('misc_collmetadata.EXPLAIN_COLLID')) !!}
                                        <x-link href="https://dwc.tdwg.org/terms/#dwc:collectionID" target="_blank">{{ __('misc_collmetadata.DWC_COLLID') }}</x-link>):
                                        {!! Purify::clean(__('misc_collmetadata.EXPLAIN_COLLID_2')) !!}
                                    </div>
                                </x-popover>
                            </div>
                        </div>

                        @if($collid)
                            <div class="{{ $fieldRowClass }}">
                                <div class="{{ $labelClass }}">{{ __('misc_collmetadata.SECURITY_KEY') }}:</div>
                                <div class="max-w-full min-h-9 text-base-content/70">{{ $collection['securitykey'] ?? '' }}</div>
                            </div>

                            <div class="{{ $fieldRowClass }}">
                                <div class="{{ $labelClass }}">{{ __('misc_collmetadata.RECORDID') }}:</div>
                                <div class="max-w-full min-h-9 text-base-content/70">{{ $collection['recordid'] ?? '' }}</div>
                            </div>
                        @endif

                        <div class="pt-2">
                            @if($collid)
                                <input type="hidden" name="securityKey" value="{{ $collection['securitykey'] ?? '' }}" />
                                <input type="hidden" name="recordID" value="{{ $collection['recordid'] ?? '' }}" />
                                <x-button type="submit" name="action" value="saveEdits">
                                    {{ __('misc_collmetadata.SAVE_EDITS') }}
                                </x-button>
                            @else
                                <x-button type="submit" name="action" value="newCollection">
                                    {{ __('misc_collmetadata.CREATE_COLL_2') }}
                                </x-button>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            @if($collid)
                <div class="space-y-4">
                    <section class="rounded border  bg-base-200 p-4">
                        <h2 class="text-lg font-bold">{{ __('misc_collmetaresources.LINK_RESOURCE') }}</h2>

                        <div class="space-y-3">
                            @if($resourceLinks)
                                @foreach($resourceLinks as $index => $link)
                                    <div class="{{ $loop->first ? '' : 'border-t  pt-3' }}">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="font-bold">Link:</span>
                                            <a href="{{ $displayValue($link['url'] ?? '') }}" target="_blank" class="text-link-darker underline underline-offset-2">{{ $displayValue($link['url'] ?? '') }}</a>
                                            <button type="button" onclick="editLink({{ $index }}); return false;" class="cursor-pointer">
                                                <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                                            </button>
                                            <button type="button" onclick="deleteLink({{ $index }}); return false;" class="cursor-pointer">
                                                <i class="fas fa-trash text-base-content hover:text-base-content/50"></i>
                                            </button>
                                        </div>

                                        @foreach(($link['title'] ?? []) as $langCode => $titleValue)
                                            <div class="ml-4 mt-1 text-sm">
                                                <span class="font-bold">{{ __('misc_collmetaresources.TITLE') }} ({{ $languageLabelMap[$langCode] ?? strtoupper($langCode) }}):</span>
                                                {{ $displayValue($titleValue) }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div>{{ __('misc_collmetaresources.NO_LINKS') }}</div>
                            @endif
                        </div>

                        <form name="resourceLinkForm" method="POST" action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}" class="hidden">
                            @csrf
                            <input type="hidden" name="action" value="saveResourceLink" />
                            <input id="resourceJsonInput" name="resourcejson" type="hidden" value="{{ old('resourcejson', $resourceJson) }}" />
                        </form>

                        <hr class="border border-accent my-2" />

                        <div class="rounded border  bg-base-200 p-4">
                            <h3 class="text-lg font-bold">{{ __('misc_collmetaresources.ADD_EDIT_LINK') }}</h3>

                            <form name="linkForm" onsubmit="return false;" class="space-y-3">
                                <div class="flex flex-wrap items-center">
                                    <label for="resource-url" class="{{ $labelClass }}">URL:</label>
                                    <input id="resource-url" name="url" type="text" class="{{ $inputWideClass }}" required />
                                </div>

                                @foreach($languageCodes as $code)
                                    <div class="flex flex-wrap items-center">
                                        <label class="{{ $labelClass }}">{{ __('misc_collmetaresources.CAPTION_OVERRIDE') }} ({{ $languageLabelMap[$code] ?? strtoupper($code) }}):</label>
                                        <input name="title-{{ $code }}" type="text" value="{{ $code === 'en' ? 'Homepage' : '' }}" class="{{ $inputShortClass }}" />
                                    </div>
                                @endforeach

                                <div id="add-link-div">
                                    <x-button type="button" onclick="addLink(this.form)">
                                        {{ __('misc_collmetaresources.ADD_LINK') }}
                                    </x-button>
                                </div>

                                <div id="edit-link-div" class="hidden">
                                    <input name="linkIndex" type="hidden" />
                                    <x-button type="button" onclick="applyEdits(this.form)">
                                        {{ __('misc_collmetaresources.APPLY_EDITS') }}
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <section class="rounded border  bg-base-200 p-4">
                        <h2 class="text-lg font-bold">{{ __('misc_collmetaresources.CONTACTS') }}</h2>

                        <div id="contact-json-data" data-contact-json="{{ $contactJson }}" class="hidden"></div>

                        <div class="space-y-3">
                            @if($contacts)
                                @foreach($contacts as $index => $contact)
                                    <div class="{{ $loop->first ? '' : 'border-t  pt-3' }}">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <div class="font-bold">{{ trim($displayValue($contact['firstName'] ?? '') . ' ' . $displayValue($contact['lastName'] ?? '')) }}</div>
                                            <button type="button" onclick="editContact({{ $index }}); return false;" class="cursor-pointer">
                                                <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                                            </button>
                                            <form method="POST" action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="deleteContact" />
                                                <input type="hidden" name="contactIndex" value="{{ $index }}" />
                                                <button type="submit" class="cursor-pointer">
                                                    <i class="fas fa-trash text-base-content hover:text-base-content/50"></i>
                                                </button>
                                            </form>
                                        </div>

                                        @if(!empty($contact['role']))
                                            <div class="ml-4 text-sm"><span class="font-bold">{{ __('misc_collmetaresources.ROLE') }}:</span> {{ $displayValue($contact['role']) }}</div>
                                        @endif

                                        @if(!empty($contact['email']))
                                            <div class="ml-4 text-sm">
                                                <span class="font-bold">{{ __('misc_collmetaresources.EMAIL') }}:</span> {{ $displayValue($contact['email']) }}
                                                @if(!empty($contact['centralContact']))
                                                    ({{ __('misc_collmetaresources.C_CONTACT') }})
                                                @endif
                                            </div>
                                        @endif

                                        @if(!empty($contact['phone']))
                                            <div class="ml-4 text-sm"><span class="font-bold">{{ __('misc_collmetaresources.PHONE') }}:</span> {{ $displayValue($contact['phone']) }}</div>
                                        @endif

                                        @if(!empty($contact['orcid']))
                                            <div class="ml-4 text-sm">
                                                <span class="font-bold">ORCID:</span>
                                                <a href="https://orcid.org/{{ $displayValue($contact['orcid']) }}" target="_blank" class="text-link-darker underline underline-offset-2">{{ $displayValue($contact['orcid']) }}</a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div>{{ __('misc_collmetaresources.NO_CONTACTS') }}</div>
                            @endif
                        </div>

                        <hr class="border border-accent my-2" />

                        <div class="rounded border  bg-base-200 p-4">
                            <h3 class="text-lg font-bold">{{ __('misc_collmetaresources.ADD_EDIT_CONTACT') }}</h3>

                            <form name="contactEditForm" method="POST" action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="action" value="saveContact" />
                                <input type="hidden" name="contactIndex" value="" />

                                <div class="flex flex-wrap items-center">
                                    <label for="contact-first-name" class="{{ $labelClass }}">{{ __('misc_collmetaresources.FIRST_NAME') }}:</label>
                                    <input id="contact-first-name" name="firstName" type="text" class="{{ $inputShortClass }}" required />
                                </div>

                                <div class="flex flex-wrap items-center">
                                    <label for="contact-last-name" class="{{ $labelClass }}">{{ __('misc_collmetaresources.LAST_NAME') }}:</label>
                                    <input id="contact-last-name" name="lastName" type="text" class="{{ $inputShortClass }}" required />
                                </div>

                                <div class="flex flex-wrap items-center">
                                    <label for="contact-role" class="{{ $labelClass }}">{{ __('misc_collmetaresources.ROLE') }}:</label>
                                    <input id="contact-role" name="role" type="text" class="{{ $inputShortClass }}" />
                                </div>

                                <div class="flex flex-wrap items-center">
                                    <label for="contact-email" class="{{ $labelClass }}">{{ __('misc_collmetaresources.EMAIL') }}:</label>
                                    <input id="contact-email" name="email" type="text" class="{{ $inputShortClass }}" />
                                </div>

                                <div class="flex flex-wrap items-center">
                                    <span class="inline-flex min-h-9 min-w-40 items-center gap-1"></span>
                                    <label class="max-w-full inline-flex items-center gap-2 font-bold">
                                        <input name="centralContact" type="checkbox" value="1" />
                                        <span>{{ __('misc_collmetaresources.IS_C_CONTACT') }}</span>
                                    </label>
                                </div>

                                <div class="flex flex-wrap items-center">
                                    <label for="contact-phone" class="{{ $labelClass }}">{{ __('misc_collmetaresources.PHONE') }}:</label>
                                    <input id="contact-phone" name="phone" type="text" class="{{ $inputMediumClass }}" />
                                </div>

                                <div class="flex flex-wrap items-center">
                                    <label for="contact-orcid" class="{{ $labelClass }}">ORCID:</label>
                                    <input id="contact-orcid" name="orcid" type="text" class="{{ $inputMediumClass }}" />
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <x-button type="submit">
                                        <span id="addContact-span">{{ __('misc_collmetaresources.ADD_CONTACT') }}</span>
                                        <span id="editContact-span" class="hidden">{{ __('misc_collmetaresources.EDIT_CONTACT') }}</span>
                                    </x-button>

                                    <x-button type="reset" variant="neutral" onclick="resetContactForm()">
                                        {{ __('misc_collmetaresources.RESET_FORM') }}
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <section class="rounded border  bg-base-200 p-4">
                        <h2 class="text-lg font-bold">{{ __('misc_sharedterms.MAILING_ADD') }}</h2>

                        @if($address)
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="font-bold">
                                        {{ $displayValue($address['institutionname'] ?? '') }}
                                        @if(!empty($address['institutioncode']))
                                            ({{ $displayValue($address['institutioncode']) }})
                                        @endif
                                    </div>

                                    <a
                                        href="{{ legacy_url('/collections/misc/institutioneditor.php') . '?' . http_build_query(['emode' => 1, 'targetcollid' => $collid, 'iid' => $address['iid'] ?? null]) }}"
                                        class="cursor-pointer"
                                        title="{{ __('misc_sharedterms.EDIT_ADDRESS') }}"
                                    >
                                        <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                                    </a>

                                    <form method="POST" action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="removeAddress" />
                                        <input type="hidden" name="removeiid" value="{{ $address['iid'] ?? '' }}" />
                                        <button type="submit" class="cursor-pointer" title="{{ __('misc_sharedterms.UNLINK_ADDRESS') }}">
                                            <i class="fas fa-unlink text-base-content hover:text-base-content/50"></i>
                                        </button>
                                    </form>
                                </div>

                                @foreach(['address1', 'address2'] as $field)
                                    @if(!empty($address[$field]))
                                        <div>{{ $displayValue($address[$field]) }}</div>
                                    @endif
                                @endforeach

                                @if(!empty($address['city']) || !empty($address['stateprovince']))
                                    <div>{{ trim($displayValue($address['city'] ?? '') . ', ' . $displayValue($address['stateprovince'] ?? '') . ' ' . $displayValue($address['postalcode'] ?? '')) }}</div>
                                @endif

                                @foreach(['country', 'phone', 'contact', 'email', 'notes'] as $field)
                                    @if(!empty($address[$field]))
                                        <div>{{ $displayValue($address[$field]) }}</div>
                                    @endif
                                @endforeach

                                @if(!empty($address['url']))
                                    <div>
                                        <a href="{{ $displayValue($address['url']) }}" target="_blank" class="text-link-darker underline underline-offset-2">{{ $displayValue($address['url']) }}</a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mb-4 font-bold">{{ __('misc_sharedterms.NO_ADDRESS') }}</div>

                            <form method="POST" action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="action" value="linkAddress" />

                                <div class="flex flex-wrap items-center">
                                    <label for="iid" class="{{ $labelClass }}">{{ __('misc_sharedterms.SEL_ADDRESS') }}:</label>
                                    <select id="iid" name="iid" class="{{ $inputMediumClass }}" required>
                                        <option value="">{{ __('misc_sharedterms.SEL_ADDRESS') }}</option>
                                        <option value="">------------------------------------</option>
                                        @foreach($institutionOptions as $iid => $name)
                                            <option value="{{ $iid }}">{{ $displayValue($name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <x-button type="submit">
                                        {{ __('misc_sharedterms.LINK_ADDRESS') }}
                                    </x-button>

                                    <x-button
                                        href="{{ legacy_url('/collections/misc/institutioneditor.php') . '?' . http_build_query(['emode' => 1, 'instcode' => $collection['institutioncode'] ?? '', 'targetcollid' => $collid]) }}"
                                        variant="neutral"
                                    >
                                        {{ __('misc_sharedterms.ADD_INST') }}
                                    </x-button>
                                </div>
                            </form>
                        @endif
                    </section>
                </div>
            @endif
        </x-tabs>
    </div>
</x-margin-layout>
