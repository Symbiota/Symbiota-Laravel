@props(['occurrence' ])

@php global $SERVER_ROOT;

include_once(legacy_path('/classes/OccurrenceAttributes.php'));
$attrManager = new OccurrenceAttributes();
$attrManager->setOccid($occurrence->occid);

$traits = $attrManager->getTraitArr();
$source_items = [];

if($traits) {
    foreach($traits as $traitID => $traitData){
        if(!isset($traitData['dependentTrait'])){
            $statusCode = 0;
            $notes = '';
            $source = '';
            if(array_key_exists('states', $traitData)){
                foreach($traitData['states'] as $id => $stArr){
                    if(isset($stArr['statuscode']) && $stArr['statuscode']) $statusCode = $stArr['statuscode'];
                    if(isset($stArr['notes']) && $stArr['notes']) $notes = $stArr['notes'];
                    if(isset($stArr['source']) && $stArr['source']) $source = $stArr['source'];
                }
            }
        }
    }

    foreach($attrManager->getSourceControlledArr($source) as $controlledSource) {
        $source_items[] = item($controlledSource, $controlledSource);
    }
}
@endphp

<div class="flex flex-col gap-4">
    @foreach($traits as $traitID => $traitData)
        @php if(isset($traitData['dependentTrait'])) continue @endphp
        <form method="POST">
            @csrf
            <x-fieldset :legend="$traitData['name']">
                {{-- Numerical --}}
                <x-traits.form-input :traits="$traits" :traitId="$traitID" />
                <x-input name="notes" :inline="true" label="Notes" />
                <x-select
                    class="w-60"
                    name="source"
                    :label="__('exsiccati.SOURCE')"
                    :inline="true"
                    :items="$source_items"
                />

                <x-select
                    class="w-60"
                    name="setstatus"
                    :label="__('taxonomy_batchloader.STATUS')"
                    default="0"
                    :inline="true"
                    :items="[
                    item(0, __('includes_traittab.NOT_REVIEWED')),
                    item(5, __('includes_traittab.EXPERT_NEEDED')),
                    item(10, __('misc_commentlist.REVIEWED')),
            ]"
                />
                <div class="flex gap-2">
                    <x-button> {{ __('geothesaurus.SAVE_EDITS') }} </x-button>
                    <x-button class="ml-auto" variant="error"> {{ __('includes_traittab.DEL_CODING') }} </x-button>
                </div>
            </x-fieldset>
        </form>
    @endforeach
</div>
