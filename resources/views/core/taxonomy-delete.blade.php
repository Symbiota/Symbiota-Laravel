@props(['verifyArr' => [], 'taxonInfo' => []])
<div
    id="taxonomy-delete"
    name="taxonomy-delete"
    x-data="{
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
}"
>
    <div id="evaluation-message" name="evaluation-message">
        <span>
            Taxon record first needs to be evaluated before it can be deleted from the system. The evaluation ensures
            that the deletion of this record will not interfere with data integrity.
        </span>
    </div>
    @if(session('error'))
        <div class="alert alert-error">
            <span class="text-2xl" style="color: var(--color-info-darker)">{{ session('error') }}</span>
        </div>
    @endif
    <x-taxon-linked-item
        :items="$verifyArr['child'] ?? []"
        :title="__('taxonomy_taxonomydelete.CHILD_TAXA')"
        :warning="__('taxonomy_taxonomydelete.CHILDREN_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.CHILD_TAXA_PLURAL')"
        :item-type="'child'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['syn'] ?? []"
        :title="__('taxonomy_taxonomydelete.SYN_LINKS')"
        :warning="__('taxonomy_taxonomydelete.SYN_EXISTS')"
        :item-name-plural="__('taxonomy_taxonomydelete.SYN_LINKS_PLURAL')"
        :item-type="'synonym'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['img'] ?? []"
        :title="__('taxonomy_taxonomydelete.IMAGE_LINKS')"
        :warning="__('taxonomy_taxonomydelete.IMGS_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.IMAGES')"
        :item-type="'image'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['map'] ?? []"
        :title="__('taxonomy_taxonomydelete.TAXON_MAPS')"
        :warning="__('taxonomy_taxonomydelete.MAPS_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.TAXON_MAPS_PLURAL')"
        :item-type="'map'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['vern'] ?? []"
        :title="__('taxonomy_taxonomydelete.VERNACULARS')"
        :warning="__('taxonomy_taxonomydelete.VERNACULARS_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.VERNACULAR_NAMES')"
        :item-type="'vernacular'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['tdesc'] ?? []"
        :title="__('taxonomy_taxonomydelete.TEXT_DESCRIPTIONS')"
        :warning="__('taxonomy_taxonomydelete.TEXT_DESCS_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.LINKED_TEXT_DESCS')"
        :item-type="'text_description'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['occur'] ?? []"
        :title="__('taxonomy_taxonomydelete.OCCURRENCE_RECORDS')"
        :warning="__('taxonomy_taxonomydelete.OCCS_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.LINKED_OCCS')"
        :item-type="'occurrence'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['dets'] ?? []"
        :title="__('taxonomy_taxonomydelete.DETERMINATIONS')"
        :warning="__('taxonomy_taxonomydelete.DETS_REMAPPED')"
        :item-name-plural="__('taxonomy_taxonomydelete.LINKED_DETS')"
        :item-type="'determination'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['cl'] ?? []"
        :title="__('taxonomy_taxonomydelete.CHECKLISTS')"
        :warning="__('taxonomy_taxonomydelete.CHECKLISTS_REMAPPED')"
        :item-name-plural="__('taxonomy_taxonomydelete.LINKED_CHECKLISTS')"
        :item-type="'checklist'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['kmdesc'] ?? []"
        :title="__('taxonomy_taxonomydelete.MORPHO_KEY_DESC')"
        :warning="__('taxonomy_taxonomydelete.MORPHO_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.LINKED_MORPHO')"
        :item-type="'morpho'"
    />
    <x-taxon-linked-item
        :items="$verifyArr['link'] ?? []"
        :title="__('taxonomy_taxonomydelete.LINKED_RESOURCES')"
        :warning="__('taxonomy_taxonomydelete.LINKED_RES_EXIST')"
        :item-name-plural="__('taxonomy_taxonomydelete.LINKED_RES_PLURAL')"
        :item-type="'linked_resource'"
    />

    <fieldset class="border-base-300 mb-4 rounded-md border p-4">
        <legend class="text-lg font-bold">{{ __('taxonomy_taxonomydelete.REMAP_RESOURCES') }}</legend>
        <span>{{ __('taxonomy_taxonomydelete.WARNING_REMAP') }}</span>
        <form id="remap-taxon-form" method="POST" action="{{ route('taxon.remap', ['tid' => $taxonInfo->tid]) }}">
            @csrf
            @method('POST')
            <div class="mb-2">
                <label
                    class="text-lg font-bold"
                    for="remapvalue"
                    >{{ __('checklists_clsppeditor.TARGET_TAXON') }}</label
                >
            </div>
            <x-taxa-search
                class="font-bold"
                required
                id="remapvalue"
                name="remapvalue"
                :label="''"
                :tidName="'remaptid'"
                :hide_selector="true"
                :hide_synonyms_checkbox="true"
                :taxa_value="''"
                :tid_value="''"
            />
            <x-button class="mt-3" color="primary" type="submit">
                {{ __('taxonomy_taxonomydelete.REMAP_TAXON') }}
            </x-button>
        </form>
    </fieldset>
    <fieldset class="border-base-300 mb-4 rounded-md border p-4">
        <legend class="text-lg font-bold">{{ __('taxonomy_taxonomydelete.DELETE_TAX_AND_RES') }}</legend>
        <form id="delete-taxon-form" method="POST" action="{{ route('taxon.delete', ['tid' => $taxonInfo->tid]) }}">
            @csrf
            @method('DELETE')
        </form>
        <x-button
            @click.prevent="if (!isDeleteValid) return; document.getElementById('confirm-delete-taxon-dialog').showModal()"
            x-bind:aria-disabled="!isDeleteValid"
            x-bind:class="!isDeleteValid ? 'opacity-50 cursor-not-allowed' : ''"
            color="danger"
            class="mt-2"
        >
            <span
                x-text="isDeleteValid ? @js(__('taxonomy_taxonomydelete.DELETE_TAXON')) : @js(__('taxonomy_taxonomydelete.DELETE_TAXON_DISABLED'))"
            />
        </x-button>
        <x-modal id="confirm-delete-taxon-dialog">
            <x-slot name="title" class="text-2xl">
                {{ __('taxonomy_taxonomydelete.DELETE_CONFIRM_TITLE') }}
            </x-slot>
            <x-slot name="body" class="space-y-4">
                <p>{{ __('taxonomy_taxonomydelete.DELETE_CONFIRM_MESSAGE', ['name' => $taxonInfo->sciName ?? '']) }}</p>
                <div class="flex justify-end gap-2">
                    <x-button
                        type="button"
                        command="close"
                        commandfor="confirm-delete-taxon-dialog"
                        variant="neutral"
                    >
                        {{ __('taxonomy_taxonomydelete.CANCEL') }}
                    </x-button>
                    <x-button type="submit" form="delete-taxon-form" variant="error">
                        {{ __('taxonomy_taxonomydelete.DELETE_TAXON') }}
                    </x-button>
                </div>
            </x-slot>
        </x-modal>
    </fieldset>
</div>
