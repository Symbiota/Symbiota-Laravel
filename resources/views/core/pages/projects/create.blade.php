@php
global $LANG, $IS_KEY_MOD_IS_ACTIVE;
include_once(legacy_path('/classes/utilities/Language.php'));
include_once(legacy_path('/classes/ImInventories.php'));

Language::load([
    'projects/index',
    'checklists/index'
]);

$projManager = new ImInventories('write');
@endphp

<x-margin-layout>
    <div>
        <x-breadcrumbs :items="[
        ['title' => $LANG['NAV_HOME'], 'href' => url('') ],
        ['title' => $LANG['SPECIES_INVENTORIES'], 'href' => url('/checklists') ],
        /* TODO (Logan) translate */
        'Create a Project'
    ]" />
    </div>
    <div>
        <h3 class="text-4xl font-bold text-primary">{{ $LANG['ADD_NEW'] }}</h3>
    </div>

    <form class="flex flex-col gap-4" hx-post="{{ url()->current() }}" method="post" x-on:hx-after-response="console.log(event)" >
        <x-projects.form />
        <x-button>{{ $LANG['ADDNEWPR'] }}</x-button>
    </form>
</x-margin-layout>
