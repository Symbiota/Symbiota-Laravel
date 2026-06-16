@props(['collection', 'archives' => [], 'collection_lookup' => []])
<x-margin-layout class="flex flex-col gap-4" x-data="{ edit_open: false}">
    <x-breadcrumbs
        :items="[
        ['title' => __('header.H_HOME'), 'href' => route('home')],
        ['title' => __('header.H_SITEMAP'), 'href' => url('/sitemap')],
        ['title' => __('publisher.DWCA_PUBLISHING')]
        ]"
    />
    <span class="flex items-center">
        <x-page-title> {{ __('publisher.DWCA_PUBLISHING') }} </x-page-title>

        @can('SUPER_ADMIN')
        <x-button class="h-8 w-8 p-0 justify-center items-center ml-auto" @click="edit_open = !edit_open">
            <x-icons.edit class="text-inherit hover:text-inherit"/>
        </x-button>
        @endcan
    </span>

    @isset($collection)
        <h2 class="text-2xl font-bold">{{ $collection->collectionName }}</h2>
    @endisset

    <p>
        {{ __('publisher.DWCA_EXPLAIN_1') }}
        <x-link target="_blank" href="https://en.wikipedia.org/wiki/Darwin_Core_Archive">
            {{ __('publisher.DWCA') }}
        </x-link>
        {{ __('publisher.DWCA_EXPLAIN_2') }}
        <x-link href="http://rs.tdwg.org/dwc/terms/"> {{ __('publisher.DWC') }} </x-link>
        {{ __('publisher.DWCA_EXPLAIN_3') }}
        <x-link href="https://docs.symbiota.org/Collection_Manager_Guide/Data_Publishing/publishing_idigbio">
            {{ __('publisher.PUBLISH_IDIGBIO') }}
        </x-link>
        &
        <x-link href="https://docs.symbiota.org/Collection_Manager_Guide/Data_Publishing/publishing_gbif">
            {{ __('publisher.PUBLISH_GBIF') }}
        </x-link>
    </p>

    <div>
        <h2 class="text-2xl font-bold">{{ __('publisher.DATA_USE_POLICY') }}</h2>
        <p>
            {{ __('publisher.DATA_POLICY_1') }}
            <x-link href="{{ url('usagepolicy') }}"> {{ __('publisher.DATA_USE_POLICY') }}. </x-link>
            {{ __('publisher.DATA_POLICY_2') }}
        </p>
    </div>

    <div>
        <x-text-label :label="__('misc_collprofiles.RSS_FEED')">
            @if(file_exists(legacy_path('content/dwca/rss.xml')))
                <x-link :href="legacy_url('content/dwca/rss.xml')"> {{ legacy_url('content/dwca/rss.xml') }} </x-link>
            @else
                --{{ __('publisher.FEED_NOT_PUBLISHED') }}--
            @endif
        </x-text-label>
    </div>

    @can('SUPER_ADMIN')
    <div x-cloak x-show="edit_open">
        <form>
            <x-fieldset :legend="__('publisher.PUBLISH_REFRESH')">
                @foreach($collection_lookup as $c)
                    @if(!$c->guidTarget)
                    <span class="inline-flex gap-2 ml-8">
                        <x-link href="#">
                            {{ $c->collectionName }}
                        </x-link>
                        -
                        <span class="text-error">
                            {{ __('publisher.MISSING_GUID') }}
                        </span>
                    </span>
                    @elseif($c->dwcaurl && !strpos($serverName, 'localhost') && strpos($c->dwcaurl, str_replace('www.', '', $serverName)) === false)
                        <span class="inline-flex gap-2 ml-8">
                            <x-link href="#">
                                {{ $c->collectionName }}
                            </x-link>
                            <span class="text-error">
                                - {{ __('publisher.ALREADY_PUB_DOMAIN') }}
                            </span>
                        </span>
                    @else
                        <x-checkbox name="coll[]" :value="$c->collID" :checked="$c->dwcaUrl">
                            <x-slot name="label">
                                <x-link href="#">
                                    {{ $c->collectionName }}
                                </x-link>
                                @if($c->dwcaUrl)
                                    - Published
                                @endif
                            </x-slot>
                        </x-checkbox>
                    @endif
                @endforeach
                <x-fieldset :legend="__('checklists_checklist.OPTIONS')">
                    @foreach([
                        'dets' => __('publisher.INCLUDE_DETS'),
                        // todo change field to media
                        'imgs' => __('publisher.INCLUDE_MEDIA_URLS'),
                        'attributes' => __('publisher.INCLUDE_ATTRIBUTES'),
                        'matsample' => __('publisher.INCLUDE_MATSAMPLE'),
                        'identifiers' => __('publisher.INCLUDE_MATSAMPLE'),
                        'identifiers' => __('publisher.INCLUDE_ASSOCIATIONS'),
                        'redact' => __('publisher.REDACT_REC'),
                    ] as $field => $label)
                        <x-checkbox :id="$field" :label="$label" :checked="true" />
                    @endforeach
                </x-fieldset>
                <x-button> {{ __('publisher.CREATE_REFRESH') }} </x-button>
            </x-fieldset>
        </form>
    </div>
    @endcan

    @if(!empty($archives))
    <table class="table-auto border-separate border-spacing-y-2">
        <thead class="border-b-1 text-left">
            <th>{{ __('misc_collmetadata.COLL_NAME') }}</th>
            <th>{{ __('publisher.DWCA') }}</th>
            <th>{{ __('projects.METADATA') }}</th>
            <th>{{ __('publisher.PUB_DATE') }}</th>
        </thead>
        <tbody>
            @foreach($archives as $archive)
                <tr>
                    <td>
                        @if($collectionName = $collection_lookup[$archive['collid']]->collectionName)
                            <x-link target="_blank" :href="url('collections/' . $archive['collid'])">
                                {{ $collectionName }}
                            </x-link>
                        @endif
                    </td>
                    <td>
                        <span class="flex items-center gap-4">
                            <x-link :href="$archive['link']"> DwC-A ({{ $archive['size'] }}) </x-link>
                        </span>
                    </td>
                    <td>
                        <x-link :href="legacy_url('collections/datasets/emlhandler.php?collid=' . $archive['collid'])">
                            EML
                        </x-link>
                    </td>
                    <td>{{ date('Y-m-d', strtotime($archive['pubDate'])) }}</td>
                    @can('SUPER_ADMIN')
                        <td>
                            <x-button class="flex h-6 w-6 justify-center p-0">
                                <x-icons.delete class="text-inherit hover:text-inherit" />
                            </x-button>
                        </td>
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        NOthing todo
    @endif
</x-margin-layout>
