@props(['project' => \App\Models\Project::make()])

@php global $LANG @endphp

@csrf
<x-input
    id="projname"
    :label="$LANG['PROJNAME']"
    :value="$project->projname"
/>

<x-input
    id="managers"
    :label="$LANG['MANAG']"
    :value="$project->managers"
/>

<x-rich-editor id="fulldescription" :label="$LANG['DESCRIP']">
    {{ Purify::clean($project->fulldescription) }}
</x-rich-editor>

<x-input
    id="notes"
    :label="$LANG['NOTES']"
    :value="$project->notes"
/>

<x-select
    id="ispublic"
    :defaultValue="$project->isPublic"
    :label="$LANG['ACCESS']"
    :items="[
        [ 'value' => 0, 'title' => $LANG['PRIVATE'], 'disabled' => false ],
        [ 'value' => 1, 'title' => $LANG['PUBLIC'], 'disabled' => false ]
    ]"
/>
