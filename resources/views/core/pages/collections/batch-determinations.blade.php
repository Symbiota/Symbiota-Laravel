@props(['collid', 'collectionName'])

@php
$batchDeterminationConfig = [
    'recordsUrl' => url('collections/' . $collid . '/batchdeterminations/records'),
    'verifyUrl' => url('collections/' . $collid . '/batchdeterminations/verify-taxon'),
    'occurrenceBaseUrl' => url('occurrence'),
    'csrf' => csrf_token(),
    'messages' => [
        'noRecords' => __('editor_batchdeterminations.NO_RECORDS'),
        'recordExists' => __('editor_batchdeterminations.RECORD_EXISTS'),
        'clearForm' => __('editor_batchdeterminations.CLEAR_FORM_RESETS'),
        'selectOne' => __('editor_batchdeterminations.SELECT_ONE'),
        'scinameRequired' => __('editor_batchdeterminations.SCINAME_NEEDS_VALUE'),
        'determinerRequired' => __('editor_batchdeterminations.DETERMINER_NEEDS_VALUE'),
        'dateRequired' => __('editor_batchdeterminations.DET_DATE_NEEDS_VALUE'),
        'warningTaxonNotFound' => __('editor_batchdeterminations.WARNING_TAXON_NOT_FOUND'),
    ],
];
@endphp

@pushOnce('js-scripts')
    <script>
        function batchDeterminations(config) {
            return {
                records: [],
                loading: false,
                annotationType: "id",

                init() {
                    const detInput = this.$el.querySelector("#det-sciname");
                    if (detInput) {
                        detInput.addEventListener("change", () => this.verifyDetSciName());
                        detInput.addEventListener("blur", () => this.verifyDetSciName());
                    }
                },

                addRecords(form) {
                    const formData = new FormData(form);
                    this.loading = true;

                    fetch(config.recordsUrl, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": config.csrf,
                            Accept: "application/json",
                        },
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            const entries = Object.entries(data || {});
                            if (!entries.length) {
                                alert(config.messages.noRecords);
                                return;
                            }

                            entries.forEach(([occid, record]) => {
                                if (this.records.some((existing) => existing.occid === occid)) {
                                    if (form.catalognumber.value.trim()) alert(config.messages.recordExists);
                                    return;
                                }

                                this.records.unshift({
                                    occid,
                                    selected: true,
                                    cn: record.cn || "",
                                    sn: record.sn || "",
                                    coll: record.coll || "",
                                    loc: record.loc || "",
                                });
                            });

                            if (form.catalognumber.value.trim()) {
                                form.catalognumber.value = "";
                                form.catalognumber.focus();
                            }
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },

                clearRecords(form) {
                    if (!confirm(config.messages.clearForm)) return;

                    this.records = [];
                    form.reset();
                },

                selectAll(checked) {
                    this.records.forEach((record) => {
                        record.selected = checked;
                    });
                },

                validateSelectForm(form) {
                    if (!this.records.some((record) => record.selected)) {
                        alert(config.messages.selectOne);
                        return false;
                    }

                    if (!form.sciname.value.trim()) {
                        alert(config.messages.scinameRequired);
                        return false;
                    }

                    if (!form.identifiedby.value.trim()) {
                        alert(config.messages.determinerRequired);
                        return false;
                    }

                    if (!form.dateidentified.value.trim()) {
                        alert(config.messages.dateRequired);
                        return false;
                    }

                    return true;
                },

                annotationTypeChanged() {
                    const form = this.$refs.detForm;
                    if (!form) return;

                    if (this.annotationType === "na") {
                        form.identificationqualifier.value = "";
                        form.confidenceranking.value = "";
                        form.identifiedby.value = "Nomenclatural Adjustment";
                        form.identifiedby.readOnly = true;
                        form.makecurrent.checked = true;
                        const today = new Date();
                        let month = today.getMonth() + 1;
                        let day = today.getDate();
                        const year = today.getFullYear();
                        if (month < 10) month = `0${month}`;
                        if (day < 10) day = `0${day}`;
                        form.dateidentified.value = [year, month, day].join("-");
                    } else {
                        form.confidenceranking.value = 5;
                        form.identifiedby.value = "";
                        form.identifiedby.readOnly = false;
                        form.dateidentified.value = "";
                        form.makecurrent.checked = false;
                    }
                },

                verifyDetSciName() {
                    const form = this.$refs.detForm;
                    if (!form || !form.sciname.value.trim()) {
                        this.clearTaxonVerification();
                        return;
                    }

                    const formData = new FormData();
                    formData.append("term", form.sciname.value.trim());

                    fetch(config.verifyUrl, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": config.csrf,
                            Accept: "application/json",
                        },
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data) {
                                form.scientificnameauthorship.value = data.author || "";
                                form.family.value = data.family || "";
                                form.tidtoadd.value = data.tid || "";
                            } else {
                                alert(config.messages.warningTaxonNotFound);
                                this.clearTaxonVerification();
                            }
                        });
                },

                clearTaxonVerification() {
                    const form = this.$refs.detForm;
                    if (!form) return;

                    form.scientificnameauthorship.value = "";
                    form.family.value = "";
                    form.tidtoadd.value = "";
                },

                occurrenceUrl(occid) {
                    return `${config.occurrenceBaseUrl}/${occid}`;
                },
            };
        }
    </script>
@endPushOnce

<x-margin-layout>
    <div x-data="batchDeterminations(@js($batchDeterminationConfig))">
        <x-breadcrumbs
            :items="[
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('datasets_datapublisher.COL_MANAGEMENT'), 'href' => url('/collections/' . $collid)],
        ['title' => __('editor_batchdeterminations.BATCH_DETERS')],
    ]"
        />

        <div class="space-y-6">
            <div>
                <h1 class="text-4xl">{{ __('editor_batchdeterminations.BATCH_DETERS') }}</h1>
                <h2 class="text-2xl">{!! Purify::clean($collectionName) !!}</h2>
            </div>

            @if(session('status'))
                <div
                    class="rounded border px-4 py-3 {{ session('statusType') === 'success' ? 'text-success' : 'text-error' }}"
                >
                    {{ session('status') }}
                    @if(session('statusType') === 'success')
                        <div class="mt-2">
                            <x-link
                                href="{{ legacy_url('/collections/reports/annotationmanager.php?collid=' . $collid) }}"
                                target="_blank"
                            >
                                {{ __('editor_batchdeterminations.DISPLAY_QUEUE') }}
                            </x-link>
                        </div>
                    @endif
                </div>
            @endif

            <section class="border-base-300 space-y-4 rounded border p-4">
                <div>
                    <h2 class="text-2xl">{{ __('editor_batchdeterminations.DEFINE_RECORDSET') }}</h2>
                    <hr />
                </div>

                <p class="whitespace-pre-line">{{ __('editor_batchdeterminations.RECORDSET_EXPLAIN') }}</p>

                <form
                    action="{{ url('collections/' . $collid . '/batchdeterminations/records') }}"
                    method="post"
                    class="space-y-4"
                    @submit.prevent="addRecords($event.target)"
                    x-ref="recordSearchForm"
                >
                    @csrf
                    <div class="flex flex-wrap items-center gap-4">
                        <x-input
                            inline
                            id="catalognumber"
                            name="catalognumber"
                            :label="__('editor_batchdeterminations.CATNUM')"
                            class="w-52"
                        />
                        <x-checkbox
                            id="allcatnum"
                            name="allcatnum"
                            :label="__('editor_batchdeterminations.TARGET_ALL')"
                            checked
                        />
                    </div>

                    <div class="flex max-w-lg flex-wrap items-center gap-2">
                        <x-form-label :label="__('editor_batchdeterminations.TAXON')" for="nomsciname" inline />
                        <div class="min-w-72 flex-1">
                            <x-autocomplete-input
                                id="nomsciname"
                                name="sciname"
                                search="{{ url('/api/taxa/search') }}"
                                request_config='{"alias":{"sciname":"taxa"}}'
                            >
                                <x-slot name="input" class="w-full"></x-slot>
                                <x-slot name="menu"></x-slot>
                            </x-autocomplete-input>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-button name="addrecord" type="submit">
                            {{ __('editor_batchdeterminations.ADD_RECORDS') }}
                        </x-button>
                        <x-button type="button" @click="clearRecords($refs.recordSearchForm)">
                            {{ __('editor_batchdeterminations.CLEAR_LIST') }}
                        </x-button>
                        <input name="collid" type="hidden" value="{{ $collid }}" />
                    </div>
                </form>

                <p>* {{ __('editor_batchdeterminations.LIST_LIMIT') }}</p>
            </section>

            <section x-show="records.length" x-cloak class="space-y-4">
                <form
                    id="accselectform"
                    action="{{ url('collections/' . $collid . '/batchdeterminations') }}"
                    method="post"
                    class="space-y-4"
                    x-ref="detForm"
                    @submit="if (!validateSelectForm($event.target)) $event.preventDefault();"
                >
                    @csrf
                    <div class="flex items-center gap-2">
                        <input
                            class="accent-accent cursor-pointer"
                            id="accselectall"
                            name="accselectall"
                            type="checkbox"
                            checked
                            @change="selectAll($event.target.checked)"
                        />
                        <label
                            class="font-bold"
                            for="accselectall"
                            >{{ __('editor_batchdeterminations.SELECT_DESELECT') }}</label
                        >
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="border-base-300 border-b">
                                    <th class="w-8 p-2"></th>
                                    <th class="p-2">{{ __('editor_batchdeterminations.CATNUM') }}</th>
                                    <th class="p-2">{{ __('editor_batchdeterminations.SCINAME') }}</th>
                                    <th class="p-2">{{ __('editor_batchdeterminations.COLLECTOR_LOCALITY') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="record in records" :key="record.occid">
                                    <tr class="border-base-300 border-b align-top">
                                        <td class="p-2">
                                            <input
                                                class="accent-accent cursor-pointer"
                                                name="occid[]"
                                                type="checkbox"
                                                x-model="record.selected"
                                                :value="record.occid"
                                            />
                                        </td>
                                        <td class="p-2">
                                            <x-link x-bind:href="occurrenceUrl(record.occid)" target="_blank">
                                                <span x-text="record.cn || '[no catalog number]'"></span>
                                            </x-link>
                                        </td>
                                        <td class="p-2" x-text="record.sn"></td>
                                        <td class="p-2">
                                            <span x-text="[record.coll, record.loc].filter(Boolean).join('; ')"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <fieldset class="border-base-300 space-y-3 rounded border p-4">
                        <legend class="px-1">{{ __('editor_batchdeterminations.NEW_DET_DETAILS') }}</legend>

                        <div class="flex flex-wrap gap-4">
                            <span class="font-bold">{{ __('editor_batchdeterminations.ANNOTATION_TYPE') }}:</span>
                            <div class="flex items-center gap-1">
                                <input
                                    class="accent-accent cursor-pointer"
                                    id="annotype-id"
                                    name="annotype"
                                    type="radio"
                                    value="id"
                                    x-model="annotationType"
                                    @change="annotationTypeChanged()"
                                    checked
                                />
                                <label for="annotype-id">{{ __('editor_batchdeterminations.ID_ADJUST') }}</label>
                            </div>
                            <div class="flex items-center gap-1">
                                <input
                                    class="accent-accent cursor-pointer"
                                    id="annotype-na"
                                    name="annotype"
                                    type="radio"
                                    value="na"
                                    x-model="annotationType"
                                    @change="annotationTypeChanged()"
                                />
                                <label for="annotype-na">{{ __('editor_batchdeterminations.NOM_ADJUST') }}</label>
                            </div>
                        </div>

                        <hr />

                        <div class="space-y-3">
                            <x-input
                                inline
                                id="identificationqualifier"
                                name="identificationqualifier"
                                :label="__('editor_batchdeterminations.ID_QUALIFIER')"
                                class="w-40"
                                x-show="annotationType === 'id'"
                            />

                            <div class="flex max-w-2xl flex-wrap items-center gap-2">
                                <x-form-label
                                    :label="__('editor_batchdeterminations.SCINAME')"
                                    for="det-sciname"
                                    :required="true"
                                    inline
                                />
                                <div class="min-w-80 flex-1">
                                    <x-autocomplete-input
                                        id="det-sciname"
                                        name="sciname"
                                        search="{{ url('/api/taxa/search') }}"
                                        request_config='{"alias":{"sciname":"taxa"}}'
                                    >
                                        <x-slot
                                            name="input"
                                            @auto_input_select="verifyDetSciName()"
                                            class="w-full"
                                        ></x-slot>
                                        <x-slot name="menu"></x-slot>
                                    </x-autocomplete-input>
                                </div>
                                <input id="daftidtoadd" name="tidtoadd" type="hidden" value="" />
                                <input name="family" type="hidden" value="" />
                            </div>

                            <x-input
                                inline
                                id="scientificnameauthorship"
                                name="scientificnameauthorship"
                                :label="__('editor_batchdeterminations.AUTHOR')"
                                class="w-52"
                            />

                            <div class="flex flex-wrap items-center gap-2" x-show="annotationType === 'id'">
                                <x-form-label
                                    :label="__('editor_batchdeterminations.CONFIDENCE')"
                                    for="confidenceranking"
                                    inline
                                />
                                <select
                                    class="border-base-300 rounded border px-1 py-0.25"
                                    id="confidenceranking"
                                    name="confidenceranking"
                                >
                                    <option value="8">{{ __('editor_batchdeterminations.HIGH') }}</option>
                                    <option value="5" selected>{{ __('editor_batchdeterminations.MEDIUM') }}</option>
                                    <option value="2">{{ __('editor_batchdeterminations.LOW') }}</option>
                                </select>
                            </div>

                            <x-input
                                inline
                                id="identifiedby"
                                name="identifiedby"
                                required
                                :label="__('editor_batchdeterminations.DETERMINER')"
                                class="w-52"
                            />

                            <x-input
                                inline
                                id="dateidentified"
                                name="dateidentified"
                                required
                                :label="__('editor_batchdeterminations.DATE')"
                                class="w-52"
                            />

                            <x-input
                                inline
                                id="identificationreferences"
                                name="identificationreferences"
                                :label="__('editor_batchdeterminations.REFERENCE')"
                                class="w-96"
                            />

                            <x-input
                                inline
                                id="identificationremarks"
                                name="identificationremarks"
                                :label="__('editor_batchdeterminations.NOTES')"
                                class="w-96"
                            />

                            <x-checkbox
                                id="makecurrent"
                                name="makecurrent"
                                :label="__('editor_batchdeterminations.MAKE_CURRENT')"
                                checked
                            />

                            <div class="flex flex-wrap items-center gap-2">
                                <x-checkbox
                                    id="printqueue"
                                    name="printqueue"
                                    :label="__('editor_batchdeterminations.ADD_PRINT_QUEUE')"
                                    checked
                                />
                                <x-link
                                    href="{{ legacy_url('/collections/reports/annotationmanager.php?collid=' . $collid) }}"
                                    target="_blank"
                                    title="{{ __('editor_batchdeterminations.DISPLAY_QUEUE') }}"
                                >
                                    <i class="fa-solid fa-table"></i>
                                </x-link>
                            </div>

                            <input name="collid" type="hidden" value="{{ $collid }}" />
                            <input name="tabtarget" type="hidden" value="0" />

                            <div class="space-y-2 pt-2">
                                <x-button name="formsubmit" type="submit" value="Add New Determinations">
                                    {{ __('editor_batchdeterminations.ADD_DETERS') }}
                                </x-button>
                                <p>
                                    <span class="text-error">*</span>
                                    {{ __('includes_requiredFieldInstruction.REQUIRED_FIELD') }}
                                </p>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </section>
        </div>
    </div>
</x-margin-layout>
