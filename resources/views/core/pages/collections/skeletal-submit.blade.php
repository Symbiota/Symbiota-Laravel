@props(['collection'])
<x-margin-layout
    x-data="{
        show_description: false,
        seconds: 0,
        record_cnt: 0,
        timerUpdate() {
            this.seconds++;
            $el.querySelector('#seconds').innerHTML = `${this.seconds % 60}`.padStart(2, '0');
            $el.querySelector('#minutes').innerHTML = `${parseInt(this.seconds/60,10)}`.padStart(2, '0');
            $el.querySelector('#count').innerHTML = this.record_cnt;
            $el.querySelector('#rate').innerHTML = Math.round(3660 * this.record_cnt / this.seconds);
        },
        show_taxa: true,
        show_authorship: true,
        show_family: true,
        show_security: true,
        show_country: false,
        show_state: true,
        show_county: true,
        show_collector: false,
        show_collector_number: false,
        show_collector_date: false,
        show_label_project: false,
        show_processing_staus: false,
        show_language: false,
        show_exsiccata: false,
        show_other_catalognumbers: false,
    }"
    x-init="setInterval(() => timerUpdate(), 1000)"
>
    <x-breadcrumbs :items="[
        ['title' => __('header.H_HOME'), 'href' => url('') ],
        ['title' => __('editor_skeletalsubmit.COL_MNGMT'), 'href' => url('/collections/' . request('collid')) ],
        ['title' => __('editor_skeletalsubmit.OCC_SKEL_SUBMIT')],
    ]"/>
    <div>
        <h1 class="text-4xl font-bold font-sans">
            {{ __('editor_skeletalsubmit.OCC_SKEL_SUBMIT') }}
        </h1>
        <h2 class="text-2xl font-bold font-sans">
            {{ $collection->collectionName }}
        </h2>
    </div>

    <div>
        <div class="flex items-center gap-2">
            <div class="text-2xl font-bold">
                {{ __('editor_skeletalsubmit.SKELETAL_DATA') }}
            </div>
            <x-button
                class="rounded-full h-5 w-5 justify-center"
                @click="show_description=!show_description"
                title="{{ __('editor_skeletalsubmit.TOOL_DESCRIPTION') }}"
            >
                ?
            </x-button>
            <div class="flex-grow"></div>
            <x-text-label class="flex-grow flex justify-end" :label="__('editor_skeletalsubmit.SESSION')">
                <span id="minutes">00</span>:<span id="seconds">00</span>
            </x-text-label>
            <x-text-label :label="__('individual.COUNT')">
                <span id="count">0</span>
            </x-text-label>

            <x-text-label :label="__('editor_skeletalsubmit.RATE')">
                <span id="rate">0</span> {{ __('editor_skeletalsubmit.PER_HOUR') }}
            </x-text-label>
        </div>
        <hr/>
    </div>

    <div x-show="show_description" class="flex flex-col gap-2">
        <p>
            {{ __('editor_skeletalsubmit.SKELETAL_DESCIPRTION_1') }}
        </p>
        <p>
            {{ __('editor_skeletalsubmit.SKELETAL_DESCIPRTION_2') }}
        </p>
        <p>
            {{ __('editor_skeletalsubmit.SKELETAL_DESCIPRTION_3') }}
        </p>
    </div>

    <x-accordion :label="__('editor_skeletalsubmit.DISPLAY_OPTIONS')" :open="false">
        <div class="flex flex-col gap-2">
            @foreach([
                ['label' => __('cleaning.OTHER_CAT_NUMS'), 'show' => 'show_other_catalognumbers'],
                ['label' => __('editor_skeletalsubmit.AUTHORSHIP'), 'show' => 'show_authorship'],
                ['label' => __('taxa.FAMILY'), 'show' => 'show_family'],
                ['label' => __('editor_skeletalsubmit.SECURITY'), 'show' => 'show_security'],
                ['label' => __('collections_list.COUNTRY'), 'show' => 'show_country'],
                ['label' => __('collections_list.STATE_PROVINCE'), 'show' => 'show_state'],
                ['label' => __('editor_skeletalsubmit.COUNTY_PARISH'), 'show' => 'show_county'],
                ['label' => __('collections_list.COLLECTOR'), 'show' => 'show_collector'],
                ['label' => __('editor_skeletalsubmit.COLLECTOR_NO'), 'show' => 'show_collector_number'],
                ['label' => __('editor_skeletalsubmit.COLLECTION_DATE'), 'show' => 'show_collector_date'],
                ['label' => __('editor_skeletalsubmit.LABEL_PROJECT'), 'show' => 'show_label_project'],
                ['label' => __('editor_skeletalsubmit.PROCESSING_STATUS'), 'show' => 'show_processing_staus'],
                ['label' => __('glossary_addterm.LANGUAGE'), 'show' => 'show_language'],
                ['label' => __('editor_skeletalsubmit.EXSICCATA'), 'show' => 'show_exsiccata'],
            ] as $checkbox)
                <x-checkbox :label="$checkbox['label']" x-bind:checked="{{$checkbox['show']}}" x-on:change="{{ $checkbox['show'] }}=$event.target.checked"/>
            @endforeach
            <x-radio :label="__('editor_skeletalsubmit.CATNUM_MATCH')" name="addaction" :options="[
                ['label' => __('editor_skeletalsubmit.RESTRICT_IF_EXISTS'), 'value' => 1],
                ['label' => __('editor_skeletalsubmit.APPEND_VALUES'), 'value' => 2],
            ]"
            />
        </div>
    </x-accordion>

    @fragment('skeletal-submit')
    <form
        class="flex flex-col gap-2"
        hx-post="{{ url()->current() }}"
        hx-include="input[name=addaction]"
        hx-target="#skeletal-session-records"
        hx-swap="afterbegin"
        x-data="{ form: $el }"
    >
        @csrf
        <div x-show="show_taxa">
            <x-taxa-search name="sciname"/>
        </div>
        <x-input name="scientificnameauthorship" :label="__('editor_skeletalsubmit.AUTHORSHIP')" x-show="show_authorship"/>
        @can('TAXONOMY')
        <x-button :href="url('taxon/create')">
            {{ __('sitemap.ADDTAXANAME')}}
        </x-button>
        @endcan
        <x-input name="family" :label="__('taxa.FAMILY')" x-show="show_family"/>

        <x-checkbox name="recordsecurity" :label="__('editor_skeletalsubmit.PROTECT_LOCALITY_DETAILS_FROM_PUBLIC')" x-show="show_security"/>

        <div class="flex gap-2" x-show="show_country || show_state || show_county">
            <x-input name="country" :label="__('collections_list.COUNTRY')" x-show="show_country"/>
            <x-input name="stateprovince" :label="__('collections_list.STATE_PROVINCE')" x-show="show_state"/>
            <x-input name="county" :label="__('editor_skeletalsubmit.COUNTY_PARISH')" x-show="show_county"/>
        </div>

        <div class="flex gap-2" x-show="show_collector || show_collector_number || show_collector_date">
            <x-input name="recordedby" :label="__('collections_list.COLLECTOR')" x-show="show_collector"/>
            <x-input name="recordnumber" :label="__('editor_skeletalsubmit.COLLECTOR_NO')" x-show="show_collector_number"/>
            <x-input name="eventdate" :label="__('editor_skeletalsubmit.COLLECTION_DATE')" x-show="show_collector_date"/>
        </div>

        <div class="flex gap-2" x-show="show_label_project || show_processing_staus || show_language">
            <x-input name="labelproject" :label="__('editor_skeletalsubmit.LABEL_PROJECT')" x-show="show_label_project"/>
            <x-select
                default="0"
                class="w-full"
                name="processingstatus"
                :label="__('editor_skeletalsubmit.PROCESSING_STATUS')"
                :items="[
                    item('Select Status', 'Select Status'),
                    item(__('editor_occurrencetable_display.UNPROCESSED'), 'unprocessed'),
                    item(__('editor_occurrencetable_display.STAGE_1'), 'stage 1'),
                    item(__('editor_occurrencetable_display.STAGE_2'), 'stage 2'),
                    item(__('editor_occurrencetable_display.STAGE_3'), 'stage 3'),
                    item(__('editor_occurrencetable_display.EXPERT_REQUIRED'), 'expert required'),
                    item(__('editor_occurrencetable_display.PENDING_REVIEW_NFN'), 'pending review-nfn'),
                    item(__('editor_occurrencetable_display.PENDING_REVIEW'), 'pending review'),
                    item(__('editor_occurrencetable_display.REVIEWED'), 'reviewed'),
                    item(__('editor_occurrencetable_display.CLOSED'), 'CLOSED'),
                ]"
                x-show="show_processing_staus"
            />
            <x-input name="language" :label="__('glossary_addterm.LANGUAGE')" x-show="show_language"/>
        </div>

        {{-- TODO EXSICCATA search --}}
        <div class="flex gap-2" x-show="show_exsiccata">
            <x-input name="exstitle" :label="__('editor_skeletalsubmit.EXSTITLE')"/>
            <input type="hidden" id="ometid" name="ometid"/>
            <x-input name="exsnumber" :label="__('editor_skeletalsubmit.EXSNUMBER')"/>
        </div>

        <div class="flex gap-2">
            <x-input name="catalognumber" :label="__('editor_skeletalsubmit.CATALOGNUMBER')" required />
            <x-input name="othercatalognumbers" :label="__('cleaning.OTHER_CAT_NUMS')" x-show="show_other_catalognumbers"/>
        </div>

        <div class="flex gap-2">
            <x-button>
                {{ __('profile_occurrencemenu.ADD_RECORD')}}
            </x-button>

            <x-button type="button" variant="neutral" @click="form.reset()">
                {{ __('editor_skeletalsubmit.CLEAR')}}
            </x-button>
        </div>
        <div id="form-errors"></div>
    </form>
    @endfragment

    <div>
        <div class="text-2xl font-bold capitalize">{{ __('imagelib_search.RECORDS') }}</div>
        <hr/>
    </div>
    <div id="skeletal-session-records" x-on:htmx:after-swap="record_cnt = $el.children.length">
    </div>
</x-margin-layout>
