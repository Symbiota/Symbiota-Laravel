@props(['conflicts'])

<div class="flex flex-col gap-2">
    <div class="text-2xl font-bold">{{ __('checklists_voucheradmin.VOUCHCONF') }}</div>
    <hr />
    <p>{{ __('checklists_vaconflicts.EXPLAIN_PARAGRAPH') }}</p>
</div>

@if(count($conflicts) > 0)
    <x-text-label :label="__('checklists_vaconflicts.CONFLICT_COUNT')"> {{ count($conflicts) }} </x-text-label>
    <form method="post" class="flex flex-col gap-4">
        @csrf
        <table class="border-seperate w-full text-sm">
            <thead class="bg-neutral text-neutral-content">
                <th class="w-fit p-2">
                    <x-checkbox
                        label=""
                        onchange="
                            document
                                .querySelectorAll(`input[name='occid[]']`)
                                .forEach((v) => (v.checked = event.target.checked))
                        "
                    />
                </th>
                <th class="p-2">{{ __('checklists_vaconflicts.CHECK_ID') }}</th>
                <th class="p-2">{{ __('checklists_vaconflicts.VOUCHER_SPEC') }}</th>
                <th class="p-2">{{ __('checklists_vaconflicts.CORRECTED_ID') }}</th>
                <th class="p-2">{{ __('checklists_vaconflicts.CORRECTED_ID') }}</th>
            </thead>
            <tbody>
                @foreach($conflicts as $id => $conflict)
                    <tr
                        @class([
                'bg-base-200'=> $loop->even,
                'bg-base-300' => $loop->odd,
                'py-4',
            ])
                    >
                        <td @class(["p-2", "bg-neutral" => $loop->even, "bg-neutral-lighter" => $loop->odd])
                            ><x-checkbox name="occid[]" label="" value="{{ $conflict['occid'] }}" />
                        </td>
                        <td class="p-2">
                            <x-link
                                target="_blank"
                                href="{{ legacy_url('checklists/clsppeditor.php?tid=' . $conflict['tid'] .'&clid=' . $conflict['clid']) }}"
                                >{{ $conflict['listid'] }}</x-link
                            >
                        </td>
                        <td class="p-2">
                            <x-link target="_blank" href="{{ url('occurrence/' . $conflict['occid']) }}">
                                {{ $conflict['recordnumber'] }}
                            </x-link>
                        </td>
                        <td class="p-2">{{ $conflict['specid'] }}</td>
                        <td class="p-2">{{ $conflict['identifiedby'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <input name="submitaction" type="hidden" value="resolveconflicts" />
        <x-checkbox id="removetaxa" :label="__('checklists_vaconflicts.REMOVE_TAXA')" :checked="true" />
        <div>{{ __('checklists_vaconflicts.BATCH_ACTION') }}:</div>
        <x-button>{{ __('checklists_vaconflicts.LINK_VOUCHERS') }}</x-button>
    </form>
    <div>* {{ __('checklists_vaconflicts.CORRECTED_WILL_ADD') }}</div>

@endif
