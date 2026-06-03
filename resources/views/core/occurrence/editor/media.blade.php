@props(['media' => [], 'tags' => []])

@php
global $SERVER_ROOT, $IMG_WEB_WIDTH, $IMG_TN_WIDTH;
include_once(legacy_path('/classes/Media.php'));
$mediaTags = \Media::getMediaTagKeys();
@endphp
<div class="flex flex-col gap-4" x-data="{ show_enter_url: false }">
    <x-button> {{ __('taxa.ADD_IMAGE') }} </x-button>

    <x-fieldset :legend="__('includes_imagetab.ADD_NEW_RESOURCE')">
        <div x-show="!show_enter_url">
            <x-fileinput :label="__('includes_imagetab.SELECT_IMG')" />
        </div>

        <div x-show="show_enter_url" class="flex flex-col gap-2">
            <x-input :label="__('includes_imagetab.MEDIA_URL')" required />
            <x-input :label="__('includes_imagetab.MED_VERS') . '(+- ' . config('portal.img_web_width_px') . 'px)'" />
            <x-input :label="__('includes_imagetab.THUMB_VERS') . '(+- ' . config('portal.img_tn_width_px') . 'px)'" />
            <x-checkbox :label="__('includes_imagetab.COPY_TO_SERVER')" />
        </div>
        <x-checkbox :label="__('includes_imagetab.DO_NOT_MAP_LARGE')" />

        <x-link x-show="!show_enter_url" href="#enter_url" @click="show_enter_url = true">
            {{ __('misc_collmetadata.ENTER_URL') }}
        </x-link>
        <x-link x-show="show_enter_url" href="#upload_local" @click="show_enter_url = false">
            {{ __('profile_tpimageeditor.UPLOAD_LOCAL') }}
        </x-link>

        <x-input :label="__('imagelib_imgdetails.CAPTION')" />
        <x-select :label="__('taxa.CREATOR')" />
        <x-input :label="__('imagelib_imgdetails.CREATOR_OVERRIDE')" />

        <x-input :label="__('projects.NOTES')" />
        <x-input :label="__('imagelib_imgdetails.COPYRIGHT')" />
        <x-input :label="__('includes_imagetab.SOURCE_WEBPAGE')" />
        <x-input :label="__('includes_imagetab.SORT')" />

        <div class="flex flex-col gap-1">
            @foreach($mediaTags as $key => $label)
                <x-checkbox :name="'ch_' . $key" :label="$label" />
            @endforeach
        </div>

        <x-button> {{ __('includes_imagetab.SUBMIT_NEW') }} </x-button>
    </x-fieldset>

    @foreach($media as $m)
        <div class="border-base-300 flex flex-row gap-4 border p-4">
            <div class="my-auto">
                <a href="{{ $m['thumbnail_url'] }}">
                    <img src="{{ $m['thumbnail_url'] }}" />
                </a>
            </div>
            <div>
                <div class="flex justify-end text-xl">
                    {{-- TODO (Logan) get edit form outline --}}
                    <x-icons.edit />
                </div>

                @foreach([
                    'caption' => __('imagelib_imgdetails.CAPTION'),
                    'creator' => __('taxa.CREATOR'),
                    'notes' => __('projects.NOTES'),
                    'source_url' => __('includes_imagetab.SOURCE_WEBPAGE'),
                    'url' => __('includes_imagetab.WEB_URL'),
                    'original_url' => __('includes_imagetab.LARGE_IMG_URL'),
                    'thumbnail_url' => __('includes_imagetab.THUMB_URL'),
                    'sort' => __('includes_imagetab.SORT'),
                ] as $field => $label)
                    <x-text-label :label="$label">
                        @if(($url = $m[$field] ?? false) && in_array($field, ['url', 'original_url', 'thumbnail_url', 'source_url']))
                            <x-link :href="$url">{{ $url }}</x-link>
                        @else
                            {{ $m[$field] ?? '' }}
                        @endif
                    </x-text-label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
