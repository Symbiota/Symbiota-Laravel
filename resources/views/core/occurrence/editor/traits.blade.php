@props(['occurrence', 'traits' => []])
<div class="flex flex-col gap-4">
    @foreach($traits as $traitID => $traitData)
    @php if(isset($traitData['dependentTrait'])) continue @endphp
    <form>
        <x-fieldset :legend="$traitData['name']">
            {{-- Numerical --}}
            <x-traits.form-input :traits="$traits" :traitId="$traitID" />
            <x-input :inline="true" label="Notes" />
            <x-select
                class="w-60"
                label="Source"
                :inline="true"
                :items="[
                ['title' => 'Machine Learning', 'value' => 'machine_learning', 'disabled' => false],
                ['title' => 'Physical Specimen', 'value' => 'physical_specimen', 'disabled' => false],
                ['title' => 'Viewing Image', 'value' => 'viewing_image', 'disabled' => false],
                ['title' => 'Verbatim Text Mining', 'value' => 'verbatim_text_mining', 'disabled' => false],
            ]"
            />

            <x-select
                class="w-60"
                label="Status"
                :inline="true"
                :items="[
                    item(0, 'Not Reviewed'),
                    item(5, 'Expert Needed'),
                    item(10, 'Reviewed'),
            ]"
            />
            <div class="flex gap-2">
                <x-button>
                    {{ __('geothesaurus.SAVE_EDITS') }}
                </x-button>
                <x-button class="ml-auto" variant="error">
                    {{ __('includes_traittab.DEL_CODING') }}
                </x-button>
            </div>
        </x-fieldset>
    </form>
    @endforeach
</div>
