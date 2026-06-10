@props([
    'collid',
    'collection' => [],
    'resourceLinks' => [],
    'contacts' => [],
    'resourceJson' => '',
    'contactJson' => '',
    'address' => [],
    'institutionOptions' => [],
    'languageCodes' => ['en'],
])

@php
    $displayValue = static fn ($value) => is_string($value)
        ? Purify::clean($value)
        : $value;
    $languageLabelMap = [
        'en' => __('misc_collmetaresources.ENGLISH'),
        'es' => __('misc_collmetaresources.SPANISH'),
        'fr' => __('misc_collmetaresources.FRENCH'),
        'pt' => __('misc_collmetaresources.PORTUGUESE'),
        'pr' => __('misc_collmetaresources.PORTUGUESE'),
    ];
    $inputBaseClass = 'max-w-full rounded border bg-base-100 px-1 py-1.5';
    $inputWideClass = 'w-[42rem] grow-0 ' . $inputBaseClass;
    $inputMediumClass = 'w-[25rem] grow-0 ' . $inputBaseClass;
    $inputShortClass = 'w-[15rem] grow-0 ' . $inputBaseClass;
    $labelClass = 'inline-flex mr-[1rem] items-center gap-1 font-bold';
@endphp

@pushOnce('js-scripts')
    <script>
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
        });
    </script>
@endPushOnce

<div class="space-y-4">
    <section class="bg-base-200 rounded border p-4">
        <h2 class="text-lg font-bold">{{ __('misc_collmetaresources.LINK_RESOURCE') }}</h2>

        <div class="space-y-3">
            @if($resourceLinks)
                @foreach($resourceLinks as $index => $link)
                    <div class="{{ $loop->first ? '' : 'border-t  pt-3' }}">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-bold">Link:</span>
                            <a
                                href="{{ $displayValue($link['url'] ?? '') }}"
                                target="_blank"
                                class="text-link-darker underline underline-offset-2"
                                >{{ $displayValue($link['url'] ?? '') }}</a
                            >
                            <button
                                type="button"
                                onclick="editLink({{ $index }}); return false;"
                                class="cursor-pointer"
                            >
                                <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                            </button>
                            <button
                                type="button"
                                onclick="deleteLink({{ $index }}); return false;"
                                class="cursor-pointer"
                            >
                                <i class="fas fa-trash text-base-content hover:text-base-content/50"></i>
                            </button>
                        </div>

                        @foreach(($link['title'] ?? []) as $langCode => $titleValue)
                            <div class="mt-1 ml-4 text-sm">
                                <span class="font-bold"
                                    >{{ __('misc_collmetaresources.TITLE') }} ({{ $languageLabelMap[$langCode] ?? strtoupper($langCode) }}):</span
                                >
                                {{ $displayValue($titleValue) }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div>{{ __('misc_collmetaresources.NO_LINKS') }}</div>
            @endif
        </div>

        <form
            name="resourceLinkForm"
            method="POST"
            action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}"
            class="hidden"
        >
            @csrf
            <input type="hidden" name="action" value="saveResourceLink" />
            <input
                id="resourceJsonInput"
                name="resourcejson"
                type="hidden"
                value="{{ old('resourcejson', $resourceJson) }}"
            />
        </form>

        <hr class="border-accent my-2 border" />

        <div class="bg-base-200 rounded border p-4">
            <h3 class="text-lg font-bold">{{ __('misc_collmetaresources.ADD_EDIT_LINK') }}</h3>

            <form name="linkForm" onsubmit="return false;" class="space-y-3">
                <x-input
                    id="resource-url"
                    name="url"
                    label="URL"
                    :inline="true"
                    type="text"
                    class="{{ $inputWideClass }}"
                    required
                />

                @foreach($languageCodes as $code)
                    <x-input
                        id="title-{{ $code }}"
                        name="title-{{ $code }}"
                        :label="__('misc_collmetaresources.CAPTION_OVERRIDE') . ' (' . ($languageLabelMap[$code] ?? strtoupper($code)) . ')'"
                        :inline="true"
                        type="text"
                        value="{{ $code === 'en' ? 'Homepage' : '' }}"
                        class="{{ $inputShortClass }}"
                    />
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

    <section class="bg-base-200 rounded border p-4">
        <h2 class="text-lg font-bold">{{ __('misc_collmetaresources.CONTACTS') }}</h2>

        <div id="contact-json-data" data-contact-json="{{ $contactJson }}" class="hidden"></div>

        <div class="space-y-3">
            @if($contacts)
                @foreach($contacts as $index => $contact)
                    <div class="{{ $loop->first ? '' : 'border-t  pt-3' }}">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="font-bold">
                                {{ trim($displayValue($contact['firstName'] ?? '') . ' ' . $displayValue($contact['lastName'] ?? '')) }}
                            </div>
                            <button
                                type="button"
                                onclick="editContact({{ $index }}); return false;"
                                class="cursor-pointer"
                            >
                                <i class="fas fa-edit text-base-content hover:text-base-content/50"></i>
                            </button>
                            <form
                                method="POST"
                                action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}"
                                class="inline"
                            >
                                @csrf
                                <input type="hidden" name="action" value="deleteContact" />
                                <input type="hidden" name="contactIndex" value="{{ $index }}" />
                                <button type="submit" class="cursor-pointer">
                                    <i class="fas fa-trash text-base-content hover:text-base-content/50"></i>
                                </button>
                            </form>
                        </div>

                        @if(!empty($contact['role']))
                            <div class="ml-4 text-sm">
                                <span class="font-bold">{{ __('misc_collmetaresources.ROLE') }}:</span>
                                {{ $displayValue($contact['role']) }}
                            </div>
                        @endif

                        @if(!empty($contact['email']))
                            <div class="ml-4 text-sm">
                                <span class="font-bold">{{ __('misc_collmetaresources.EMAIL') }}:</span>
                                {{ $displayValue($contact['email']) }}
                                @if(!empty($contact['centralContact']))
                                    ({{ __('misc_collmetaresources.C_CONTACT') }})
                                @endif
                            </div>
                        @endif

                        @if(!empty($contact['phone']))
                            <div class="ml-4 text-sm">
                                <span class="font-bold">{{ __('misc_collmetaresources.PHONE') }}:</span>
                                {{ $displayValue($contact['phone']) }}
                            </div>
                        @endif

                        @if(!empty($contact['orcid']))
                            <div class="ml-4 text-sm">
                                <span class="font-bold">ORCID:</span>
                                <a
                                    href="https://orcid.org/{{ $displayValue($contact['orcid']) }}"
                                    target="_blank"
                                    class="text-link-darker underline underline-offset-2"
                                    >{{ $displayValue($contact['orcid']) }}</a
                                >
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div>{{ __('misc_collmetaresources.NO_CONTACTS') }}</div>
            @endif
        </div>

        <hr class="border-accent my-2 border" />

        <div class="bg-base-200 rounded border p-4">
            <h3 class="text-lg font-bold">{{ __('misc_collmetaresources.ADD_EDIT_CONTACT') }}</h3>

            <form
                name="contactEditForm"
                method="POST"
                action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}"
                class="space-y-3"
            >
                @csrf
                <input type="hidden" name="action" value="saveContact" />
                <input type="hidden" name="contactIndex" value="" />

                <x-input
                    id="contact-first-name"
                    name="firstName"
                    :label="__('misc_collmetaresources.FIRST_NAME')"
                    :inline="true"
                    type="text"
                    class="{{ $inputShortClass }}"
                    required
                />

                <x-input
                    id="contact-last-name"
                    name="lastName"
                    :label="__('misc_collmetaresources.LAST_NAME')"
                    :inline="true"
                    type="text"
                    class="{{ $inputShortClass }}"
                    required
                />

                <x-input
                    id="contact-role"
                    name="role"
                    :label="__('misc_collmetaresources.ROLE')"
                    :inline="true"
                    type="text"
                    class="{{ $inputShortClass }}"
                />

                <x-input
                    id="contact-email"
                    name="email"
                    :label="__('misc_collmetaresources.EMAIL')"
                    :inline="true"
                    type="text"
                    class="{{ $inputShortClass }}"
                />

                <div class="flex flex-wrap items-center">
                    <span class="inline-flex min-h-9 min-w-40 items-center gap-1"></span>
                    <label class="inline-flex max-w-full items-center gap-2 font-bold">
                        <input name="centralContact" type="checkbox" value="1" />
                        <span>{{ __('misc_collmetaresources.IS_C_CONTACT') }}</span>
                    </label>
                </div>

                <x-input
                    id="contact-phone"
                    name="phone"
                    :label="__('misc_collmetaresources.PHONE')"
                    :inline="true"
                    type="text"
                    class="{{ $inputMediumClass }}"
                />

                <x-input
                    id="contact-orcid"
                    name="orcid"
                    label="ORCID"
                    :inline="true"
                    type="text"
                    class="{{ $inputMediumClass }}"
                />

                <div class="flex flex-wrap gap-2">
                    <x-button type="submit">
                        <span id="addContact-span">{{ __('misc_collmetaresources.ADD_CONTACT') }}</span>
                        <span
                            id="editContact-span"
                            class="hidden"
                            >{{ __('misc_collmetaresources.EDIT_CONTACT') }}</span
                        >
                    </x-button>

                    <x-button type="reset" variant="neutral" onclick="resetContactForm()">
                        {{ __('misc_collmetaresources.RESET_FORM') }}
                    </x-button>
                </div>
            </form>
        </div>
    </section>

    <section class="bg-base-200 rounded border p-4">
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

                    <form
                        method="POST"
                        action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}"
                        class="inline"
                    >
                        @csrf
                        <input type="hidden" name="action" value="removeAddress" />
                        <input type="hidden" name="removeiid" value="{{ $address['iid'] ?? '' }}" />
                        <button
                            type="submit"
                            class="cursor-pointer"
                            title="{{ __('misc_sharedterms.UNLINK_ADDRESS') }}"
                        >
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
                    <div>
                        {{ trim($displayValue($address['city'] ?? '') . ', ' . $displayValue($address['stateprovince'] ?? '') . ' ' . $displayValue($address['postalcode'] ?? '')) }}
                    </div>
                @endif

                @foreach(['country', 'phone', 'contact', 'email', 'notes'] as $field)
                    @if(!empty($address[$field]))
                        <div>{{ $displayValue($address[$field]) }}</div>
                    @endif
                @endforeach

                @if(!empty($address['url']))
                    <div>
                        <a
                            href="{{ $displayValue($address['url']) }}"
                            target="_blank"
                            class="text-link-darker underline underline-offset-2"
                            >{{ $displayValue($address['url']) }}</a
                        >
                    </div>
                @endif
            </div>
        @else
            <div class="mb-4 font-bold">{{ __('misc_sharedterms.NO_ADDRESS') }}</div>
            <form
                method="POST"
                action="{{ route('collections.collmetadata.update', ['collid' => $collid]) }}"
                class="space-y-3"
            >
                @csrf
                <input type="hidden" name="action" value="linkAddress" />

                <div class="flex flex-wrap items-center">
                    <div class="{{ $labelClass }}">
                        <x-form-label :label="__('misc_sharedterms.SEL_ADDRESS')" for="iid" :required="true" inline />
                    </div>
                    <select id="iid" name="iid" class="{{ $inputMediumClass }}" required>
                        <option value="">{{ __('misc_sharedterms.SEL_ADDRESS') }}</option>
                        <option value="">------------------------------------</option>
                        @foreach($institutionOptions as $iid => $name)
                            <option value="{{ $iid }}">{{ $displayValue($name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-button type="submit"> {{ __('misc_sharedterms.LINK_ADDRESS') }} </x-button>

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
