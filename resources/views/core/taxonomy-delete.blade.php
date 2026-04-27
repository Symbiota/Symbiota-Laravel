@props(['verifyArr' => [], 'taxonInfo' => []])
<script>
    window._taxonVerifyArr = {!! json_encode($verifyArr ?? [], JSON_HEX_TAG | JSON_HEX_AMP) ?: '{}' !!};
</script>
<div id="taxonomy-delete" name="taxonomy-delete" x-data="{
    isDeleteValid: false,
    async validateDelete(){
        if (window.validateTaxonDelete) {
            const isDelable = await window.validateTaxonDelete(@js($verifyArr));
            this.isDeleteValid = isDelable;
        }
    },
    init() {
        this.validateDelete();
    }
}">
    <div id="evaluation-message" name="evaluation-message">
        <span>
            Taxon record first needs to be evaluated before it can be deleted from the system. The evaluation ensures that the deletion of this record will not interfere with data integrity.
        </span>
    </div>
    @if (session('error'))
        <div class="alert alert-error">
            <span class="text-2xl" style="color: var(--color-info-darker)">{{ session('error') }}</span>
        </div>
    @endif
    <x-taxon-linked-item :items="$verifyArr['child'] ?? []" :title="__('taxonomy_taxonomydelete.CHILD_TAXA')" :warning="__('taxonomy_taxonomydelete.CHILDREN_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.CHILD_TAXA_PLURAL')" />
    <x-taxon-linked-item :items="$verifyArr['syn'] ?? []" :title="__('taxonomy_taxonomydelete.SYN_LINKS')" :warning="__('taxonomy_taxonomydelete.SYN_EXISTS')" :item-name-plural="__('taxonomy_taxonomydelete.SYN_LINKS_PLURAL')" />
    <x-taxon-linked-item :items="$verifyArr['img'] ?? []" :title="__('taxonomy_taxonomydelete.IMAGE_LINKS')" :warning="__('taxonomy_taxonomydelete.IMGS_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.IMAGES')" />
    <x-taxon-linked-item :items="$verifyArr['map'] ?? []" :title="__('taxonomy_taxonomydelete.TAXON_MAPS')" :warning="__('taxonomy_taxonomydelete.MAPS_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.TAXON_MAPS_PLURAL')" />
    <x-taxon-linked-item :items="$verifyArr['vern'] ?? []" :title="__('taxonomy_taxonomydelete.VERNACULARS')" :warning="__('taxonomy_taxonomydelete.VERNACULARS_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.VERNACULAR_NAMES')" />
    <x-taxon-linked-item :items="$verifyArr['tdesc'] ?? []" :title="__('taxonomy_taxonomydelete.TEXT_DESCRIPTIONS')" :warning="__('taxonomy_taxonomydelete.TEXT_DESCS_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.LINKED_TEXT_DESCS')" />
    <x-taxon-linked-item :items="$verifyArr['occur'] ?? []" :title="__('taxonomy_taxonomydelete.OCCURRENCE_RECORDS')" :warning="__('taxonomy_taxonomydelete.OCCS_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.LINKED_OCCS')" />
    <x-taxon-linked-item :items="$verifyArr['dets'] ?? []" :title="__('taxonomy_taxonomydelete.DETERMINATIONS')" :warning="__('taxonomy_taxonomydelete.DETS_REMAPPED')" :item-name-plural="__('taxonomy_taxonomydelete.LINKED_DETS')" />
    <x-taxon-linked-item :items="$verifyArr['cl'] ?? []" :title="__('taxonomy_taxonomydelete.CHECKLISTS')" :warning="__('taxonomy_taxonomydelete.CHECKLISTS_REMAPPED')" :item-name-plural="__('taxonomy_taxonomydelete.LINKED_CHECKLISTS')" />
    <x-taxon-linked-item :items="$verifyArr['kmdesc'] ?? []" :title="__('taxonomy_taxonomydelete.MORPHO_KEY_DESC')" :warning="__('taxonomy_taxonomydelete.MORPHO_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.LINKED_MORPHO')" />
    <x-taxon-linked-item :items="$verifyArr['link'] ?? []" :title="__('taxonomy_taxonomydelete.LINKED_RESOURCES')" :warning="__('taxonomy_taxonomydelete.LINKED_RES_EXIST')" :item-name-plural="__('taxonomy_taxonomydelete.LINKED_RES_PLURAL')" />

    <fieldset>
        <legend class="font-bold text-lg">{{ __('taxonomy_taxonomydelete.REMAP_RESOURCES') }}</legend>
        <span>{{__('taxonomy_taxonomydelete.WARNING_REMAP')}}</span>
    </fieldset>
    <fieldset>
        <legend class="font-bold text-lg">{{ __('taxonomy_taxonomydelete.DELETE_TAX_AND_RES') }}</legend>
        <x-button
            @click.prevent="isDeleteValid && (window.location.href = '{{ route('taxon.delete', ['tid' => $taxonInfo->tid]) }}')"
            x-bind:aria-disabled="!isDeleteValid"
            x-bind:class="!isDeleteValid ? 'opacity-50 cursor-not-allowed' : ''"
            color="danger"
            class="mt-2">
            <span x-text="isDeleteValid ? @js(__('taxonomy_taxonomydelete.DELETE_TAXON')) : @js(__('taxonomy_taxonomydelete.DELETE_TAXON_DISABLED'))" />
        </x-button>
    </fieldset>
</div>