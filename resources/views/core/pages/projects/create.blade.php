@php
include_once(legacy_path('/classes/ImInventories.php'));
$projManager = new ImInventories('write');
@endphp

<x-margin-layout>
    <div>
        <x-breadcrumbs :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        ['title' => __('checklists.SPECIES_INVENTORIES'), 'href' => url('/checklists') ],
        __('projects.ADDNEWPR')
    ]" /> </div>
    <fieldset class="flex flex-col gap-4">
        <div>
            <h3 class="text-4xl font-bold text-primary">{{ __('projects.ADDNEWPR') }}</h3>
        </div>

        <form class="flex flex-col gap-4" hx-post="{{ url()->current() }}" method="post">
            <x-projects.form />
            <x-button>{{ __('projects.ADDNEWPR') }}</x-button>
        </form>
    </fieldset>
</x-margin-layout>
