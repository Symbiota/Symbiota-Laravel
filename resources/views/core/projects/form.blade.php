@props(['project' => \App\Models\Project::make()])

@csrf
<x-input id="projname" :label="__('projects.PROJNAME')" :value="$project->projname" required />

<x-input id="managers" :label="__('projects.MANAG')" :value="$project->managers" />

<x-rich-editor id="fulldescription" :label="__('projects.DESCRIP')">
    {!! Purify::clean($project->fulldescription) !!}
</x-rich-editor>

<x-input id="notes" :label="__('projects.NOTES')" :value="$project->notes" />

<x-select
    id="ispublic"
    :defaultValue="$project->ispublic"
    :label="__('projects.ACCESS')"
    :items="[
        item(0, __('projects.PRIVATE')),
        item(1, __('projects.PUBLIC'))
    ]"
/>
