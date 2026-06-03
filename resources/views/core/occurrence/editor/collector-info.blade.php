@props(['occurrence'])
<x-fieldset :legend="__('editor_occurrenceeditor.COLLECTOR_INFO')">
    <div class="flex gap-4">
        <div class="w-50">
            <x-input value="{{ $occurrence->catalogNumber }}" :label="__('collections_list.CATALOG_NUMBER')" />
        </div>
        <table class="bg-base-300 border-base-300 grow border-separate rounded-md border">
            <thead class="bg-base-200">
                <th>{{ __('fieldterms_occurrenceterms.IDENT_NAME') }}</th>
                <th>{{ __('fieldterms_occurrenceterms.IDENT_VALUE') }}</th>
                <th></th>
            </thead>

            <tbody class="bg-base-100">
                @foreach([1, 2 ] as $catalogNumber)
                    <tr class="bg-base-100">
                        <td>
                            <input type="hidden" name="idkey[]" value="" />
                            <input name="idname[]" class="w-full" type="text" />
                        </td>
                        <td><input name="idvalue[]" class="w-full" type="text" /></td>
                        <td>
                            <span class="flex">
                                @if($loop->last)
                                    <span class="px-2 text-center"><i class="fa fa-plus"></i></span>
                                @else
                                    <span class="px-2 text-center"><i class="fa fa-trash"></i></span>
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center gap-2">
        <x-input :value="$occurrence->recordedBy" class="w-50" :label="__('fieldterms_occurrenceterms.RECORDED_BY')" />
        <x-input :value="$occurrence->recordNumber" :label="__('fieldterms_occurrenceterms.RECORD_NUMBER')" />
        <x-input :value="$occurrence->eventDate" class="shrink" :label="__('individual.DATE')" />
        <x-input :value="$occurrence->eventDate2" :label="__('fieldterms_occurrenceterms.EVENT_DATE2')" />
        <div class="mt-5">
            <x-button> {{ __('individual.DUPLICATES') }} </x-button>
            {{-- TODO (Logan) auto search --}}
        </div>
    </div>

    <div class="flex items-center gap-2">
        <x-input
            :value="$occurrence->associatedCollectors"
            :label="__('fieldterms_occurrenceterms.ASSOCIATED_COLLECTORS')"
        />
        <x-input
            :value="$occurrence->verbatimEventDate"
            :label="__('fieldterms_occurrenceterms.VERBATIM_EVENT_DATE')"
        />
        <x-input :value="$occurrence->eventTime" :label="__('collections_list.EVENT_TIME')" />
    </div>
    {{-- todo pipe ex data --}}
    <div class="flex items-center gap-2">
        <x-input class="grow" :label="__('editor_occurrenceeditor.EXS_TITLE')" />
        <div class="w-20">
            <x-input :label="__('exsiccati.NUMBER')" />
        </div>
    </div>
</x-fieldset>
