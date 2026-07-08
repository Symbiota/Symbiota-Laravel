@props([
    'datasetId' => 0,
    'metadata' => [],
    'role' => '',
    'roleLabel' => '',
    'isEditor' => 0,
    'occurrences' => [],
    'occurrenceCount' => 0,
    'pageNumber' => 1,
    'pageCount' => 1,
    'users' => [],
    'tabIndex' => 0,
])

@php
    $errors = $errors ?? message_bag([]);
    $tabs = [__('datasets_datasetmanager.OCC_LIST')];

    if ($isEditor === 1) {
        $tabs[] = __('datasets_datasetmanager.GEN_MANAGEMENT');
        $tabs[] = __('datasets_datasetmanager.USER_ACCESS');
    }

    $activeTab = $isEditor === 1 ? $tabIndex : 0;
    $editUrl = static fn ($page = 1, $tab = 0) => route('datasets.edit', ['dataset_id' => $datasetId])
        . '?' . http_build_query(['pagenumber' => $page, 'tabindex' => $tab]);
    $roleSections = [
        'DatasetAdmin' => __('datasets_datasetmanager.FULL_ACCESS'),
        'DatasetEditor' => __('datasets_datasetmanager.READ_WRITE_ACCESS'),
        'DatasetReader' => __('datasets_datasetmanager.READ_ACCESS'),
    ];
    $name = old('name', $metadata['name'] ?? '');
    $notes = old('notes', $metadata['notes'] ?? '');
    $description = old('description', $metadata['description'] ?? '');
    $isPublic = $errors->any() ? (bool) old('ispublic') : (bool) ($metadata['ispublic'] ?? false);

    $breadcrumbs = [
        ['title' => __('header.H_HOME'), 'href' => url('/')],
        ['title' => __('datasets_datasetmanager.MY_PROF'), 'href' => url('/user/profile')],
        ['title' => __('datasets_datasetmanager.RETURN_DS_LISTING'), 'href' => route('datasets.index')],
        ['title' => __('datasets_datasetmanager.DS_MANAGER')],
    ];
@endphp

@pushOnce('js-scripts')
    <script>
        function selectDatasetOccurrences(checked) {
            document.querySelectorAll('input[name="occid[]"]').forEach((input) => {
                input.checked = checked;
            });
        }

        function validateDatasetOccurrenceForm(form) {
            const selected = Array.from(form.querySelectorAll('input[name="occid[]"]')).some((input) => input.checked);

            if (!selected) {
                alert(@js(__('datasets_datasetmanager.PLS_SEL_SPC')));
                return false;
            }

            return true;
        }

        function validateDatasetUserForm(form) {
            if (!form.uid.value) {
                alert(@js(__('datasets_datasetmanager.SEL_USER_LIST')));
                return false;
            }

            return true;
        }

        function setDatasetUserId(event) {
            document.getElementById("uid-add").value = event.detail.selection.id;
        }

        function targetDatasetDownload(form) {
            window.open("", "downloadpopup", "left=100,top=50,width=900,height=700");
            form.target = "downloadpopup";
        }
    </script>
@endPushOnce

<x-margin-layout>
    <div class="mb-4">
        <x-breadcrumbs :items="$breadcrumbs" />
    </div>

    <div class="space-y-4">
        @if(session('status'))
            <div class="m-4 {{ session('statusType') === 'success' ? 'text-success' : 'text-error' }}">
                {!! Purify::clean(session('status')) !!}
            </div>
        @endif

        <x-errors :errors="$errors" />

        <x-page-title class="text-4xl">{{ __('datasets_datasetmanager.DS_OCC_MANAGER') }}</x-page-title>

        @if($datasetId && $metadata)
            <h2 class="text-2xl font-bold">{{ $metadata['name'] ?? '' }}</h2>
            @if($role)
                <div title="{{ $roleLabel }}">{{ __('datasets_datasetmanager.ROLE') }}: {{ $role }}</div>
            @endif
            @if($isEditor)
                <x-tabs :tabs="$tabs" :active="$activeTab">
                    <div>
                        @if($occurrences)
                            <div class="mb-3 font-bold">
                                {{ __('datasets_datasetmanager.COUNT') }}: {{ count($occurrences) }} {{ __('datasets_datasetmanager.RECORDS') }}
                            </div>
                            @if($pageCount > 1)
                                <div class="mb-3">
                                    {{ __('datasets_datasetmanager.PAGE') }}
                                    <b>{{ $pageNumber }}</b>
                                    {{ __('datasets_datasetmanager.OF') }}
                                    <b>{{ $pageCount }}</b>
                                    :

                                    @for($page = max(1, $pageNumber - 5); $page <= min($pageCount, $pageNumber + 5); $page++)
                                        @if($page === $pageNumber)
                                            <b>{{ $page }}</b>
                                        @else
                                            <x-link href="{{ $editUrl($page) }}">{{ $page }}</x-link>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                            <form
                                method="POST"
                                action="{{ route('datasets.update', ['dataset_id' => $datasetId]) }}"
                                onsubmit="return validateDatasetOccurrenceForm(this);"
                            >
                                @csrf

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr>
                                                <th class="p-2">
                                                    <input
                                                        type="checkbox"
                                                        onclick="selectDatasetOccurrences(this.checked)"
                                                        title="{{ __('datasets_datasetmanager.SEL_DESEL_SPCS') }}"
                                                    />
                                                </th>
                                                <th class="p-2">{{ __('datasets_datasetmanager.CAT_NUM') }}</th>
                                                <th class="p-2">{{ __('datasets_datasetmanager.COLLECTOR') }}</th>
                                                <th class="p-2">{{ __('datasets_datasetmanager.SCI_NAME') }}</th>
                                                <th class="p-2">{{ __('datasets_datasetmanager.LOCALITY') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($occurrences as $occid => $occurrence)
                                                <tr>
                                                    <td class="p-2">
                                                        <input name="occid[]" type="checkbox" value="{{ $occid }}" />
                                                    </td>
                                                    <td class="p-2">
                                                        <x-link
                                                            href="{{ url('/occurrence/' . $occid) }}"
                                                            target="_blank"
                                                        >
                                                            {{ $occurrence['catnum'] }}
                                                        </x-link>
                                                    </td>
                                                    <td class="p-2">{{ $occurrence['coll'] }}</td>
                                                    <td class="p-2">{{ $occurrence['sciname'] }}</td>
                                                    <td class="p-2">{{ $occurrence['loc'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($isEditor < 3)
                                    <div class="mt-4">
                                        <x-button name="submitaction" type="submit" value="Remove Selected Occurrences">
                                            {{ __('datasets_datasetmanager.REM_SEL_OCCS') }}
                                        </x-button>
                                    </div>
                                @endif
                            </form>
                            <form
                                class="mt-4"
                                method="POST"
                                action="{{ legacy_url('/collections/download/index.php') }}"
                                onsubmit="targetDatasetDownload(this)"
                            >
                                <input name="searchvar" type="hidden" value="datasetid={{ $datasetId }}" />
                                <input name="dltype" type="hidden" value="specimen" />
                                <x-button name="submitaction" type="submit" value="exportAll">
                                    {{ __('datasets_datasetmanager.EXPORT_DS') }}
                                </x-button>
                            </form>
                        @else
                            <div class="m-4 font-bold">{{ __('datasets_datasetmanager.NO_OCCS_DS') }}</div>
                            <div class="m-4">
                                {{ __('datasets_datasetmanager.LINK_OCCS_VIA') }}
                                <x-link href="{{ url('/collections/search') }}">
                                    {{ __('datasets_datasetmanager.OCC_SEARCH') }}
                                </x-link>
                                {{ __('datasets_datasetmanager.OR_VIA_OCC_PROF') }}
                            </div>
                        @endif
                    </div>

                    @if($isEditor === 1)
                        <div>
                            <fieldset class="rounded border p-4">
                                <legend class="px-1 font-bold">{{ __('datasets_datasetmanager.EDITOR') }}</legend>

                                <form
                                    method="POST"
                                    action="{{ route('datasets.update', ['dataset_id' => $datasetId]) }}"
                                    class="space-y-4"
                                >
                                    @csrf

                                    <x-input
                                        id="name"
                                        name="name"
                                        :label="__('datasets_datasetmanager.NAME')"
                                        type="text"
                                        :value="$name"
                                        required
                                        class="w-[70%]"
                                    />

                                    <x-checkbox
                                        id="ispublic"
                                        name="ispublic"
                                        :label="__('datasets_datasetmanager.PUB_VISIBLE')"
                                        value="1"
                                        :checked="$isPublic"
                                    />

                                    <x-input
                                        id="notes"
                                        name="notes"
                                        :label="__('datasets_datasetmanager.NOTES_INTERNAL')"
                                        type="text"
                                        :value="$notes"
                                        class="w-[70%]"
                                    />

                                    <x-rich-editor
                                        id="description"
                                        name="description"
                                        :label="__('datasets_datasetmanager.DESCRIPTION')"
                                        class="min-h-40"
                                        >{!! Purify::clean($description) !!}</x-rich-editor
                                    >

                                    <input name="tabindex" type="hidden" value="1" />

                                    <x-button name="submitaction" type="submit" value="Save Edits">
                                        {{ __('datasets_datasetmanager.SAVE_EDITS') }}
                                    </x-button>
                                </form>
                            </fieldset>

                            <fieldset class="mt-6 rounded border p-4">
                                <legend class="px-1 font-bold">{{ __('datasets_datasetmanager.DEL_DS') }}</legend>

                                <form
                                    method="POST"
                                    action="{{ route('datasets.update', ['dataset_id' => $datasetId]) }}"
                                    onsubmit="return confirm(@js(__('datasets_datasetmanager.SURE_DEL_DS_PERM')));"
                                >
                                    @csrf
                                    <input name="tabindex" type="hidden" value="1" />
                                    <x-button variant="error" name="submitaction" type="submit" value="Delete Dataset">
                                        {{ __('datasets_datasetmanager.DEL_DS') }}
                                    </x-button>
                                </form>
                            </fieldset>
                        </div>
                        <div>
                            @foreach($roleSections as $roleName => $label)
                                <fieldset class="mb-4 rounded border p-4">
                                    <legend class="px-1 font-bold">{{ $label }}</legend>

                                    @if(! empty($users[$roleName]))
                                        <ul class="list-disc pl-6">
                                            @foreach($users[$roleName] as $uid => $username)
                                                <li>
                                                    {{ $username }}
                                                    <form
                                                        method="POST"
                                                        action="{{ route('datasets.update', ['dataset_id' => $datasetId]) }}"
                                                        class="inline"
                                                        onsubmit="return confirm(@js(__('datasets_datasetmanager.SURE_REM_USER') . ' ' . $username . '?'));"
                                                    >
                                                        @csrf
                                                        <input name="submitaction" type="hidden" value="DelUser" />
                                                        <input name="role" type="hidden" value="{{ $roleName }}" />
                                                        <input name="uid" type="hidden" value="{{ $uid }}" />
                                                        <input name="tabindex" type="hidden" value="2" />
                                                        <button
                                                            type="submit"
                                                            title="{{ __('datasets_datasetmanager.DROP_ICON') }}"
                                                        >
                                                            <i class="fa-solid fa-xmark text-error"></i>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div>{{ __('datasets_datasetmanager.NONE_ASSIGNED') }}</div>
                                    @endif
                                </fieldset>
                            @endforeach

                            <fieldset class="rounded border p-4">
                                <legend class="px-1 font-bold">{{ __('datasets_datasetmanager.ADD_USER') }}</legend>

                                <form
                                    method="POST"
                                    action="{{ route('datasets.update', ['dataset_id' => $datasetId]) }}"
                                    class="space-y-4"
                                    onsubmit="return validateDatasetUserForm(this);"
                                >
                                    @csrf

                                    <div class="max-w-xl" title="{{ __('datasets_datasetmanager.TYPE_LOGIN') }}">
                                        <x-autocomplete-input
                                            id="userinput"
                                            name="adduser"
                                            :label="__('datasets_datasetmanager.LOGIN_NAME')"
                                            search="{{ route('datasets.user-search') }}"
                                            request_config='{"alias":{"adduser":"term"}}'
                                        >
                                            <x-slot
                                                name="input"
                                                @auto_input_select="setDatasetUserId($event)"
                                                class="w-full"
                                            ></x-slot>
                                            <x-slot name="menu"></x-slot>
                                        </x-autocomplete-input>
                                        <input id="uid-add" name="uid" type="hidden" value="" />
                                    </div>

                                    <div>
                                        <x-form-label :label="__('datasets_datasetmanager.ROLE')" for="role" />
                                        <select id="role" name="role" class="rounded border px-2 py-1">
                                            <option value="DatasetAdmin">
                                                {{ __('datasets_datasetmanager.FULL_ACCESS') }}
                                            </option>
                                            <option value="DatasetEditor">
                                                {{ __('datasets_datasetmanager.READ_WRITE_ACCESS') }}
                                            </option>
                                            <option value="DatasetReader">
                                                {{ __('datasets_datasetmanager.READ_ACCESS') }}
                                            </option>
                                        </select>
                                    </div>

                                    <input name="tabindex" type="hidden" value="2" />
                                    <x-button type="submit" name="submitaction" value="addUser">
                                        {{ __('datasets_datasetmanager.ADD_USER') }}
                                    </x-button>
                                </form>
                            </fieldset>
                        </div>
                    @endif
                </x-tabs>
            @else
                <div class="m-8">{{ __('datasets_datasetmanager.NOT_AUTH') }}</div>
            @endif
        @else
            <div class="font-bold">{{ __('datasets_datasetmanager.DS_NOT_IDENTIFIED') }}</div>
        @endif
    </div>
</x-margin-layout>
