@props(['traits', 'traitId'])
@php
$props = json_decode($traits[$traitId]['props']);
$type = $props? $props[0]->controlType: 'radio';

$coded = [];
foreach($traits[$traitId]['states'] as $sid => $state) {
    $isCoded = false;
    if(array_key_exists('coded', $state)) {
        $isCoded = is_numeric($state['coded'])?
            $state['coded']: true;
    }

    if($isCoded) {
        $coded[] = $sid;
    }
}
@endphp

@if($type === 'select')
{{-- todo select part of traits --}}
@else
<div x-data="{ radioValue: {{ $coded[0] ?? 'null' }} }" {{ $attributes->twMerge('flex flex-col gap-2')}}>
    <div class="font-bold">{{ $traits[$traitId]['name'] }}</div>
    @foreach ($traits[$traitId]['states'] as $sid => $state)
    @php
    $isCoded = false;
    if(array_key_exists('coded', $state)) {
        $isCoded = is_numeric($state['coded'])?
        $state['coded']: true;
    }
    @endphp

    <div x-data="{ parentValue: {{ $isCoded? $sid:'null' }} }">
        <x-radio.item :checked="$isCoded" x-init="parentValue = $el.value" @change="radioValue = $el.value" :label="$state['name']" :name="'traitid-' . $traitId  . '[]'" :value="$sid" />
        @isset($state['dependTraitID'])
        <div class="pl-4" x-show="parentValue == radioValue">
            @foreach($state['dependTraitID'] as $id)
                <div>{{ $traits[$id]['name'] }}</div>
                <div class="pl-4 flex  gap-2">
                @foreach($traits[$id]['states'] as $subId => $subState)
                    @php
                    $isChildCoded = false;
                    if(array_key_exists('coded', $subState)) {
                        $isChildCoded = is_numeric($subState['coded'])?
                        $subState['coded']: true;
                    }
                    @endphp
                    <x-radio.item
                    x-effect="if(parentValue !== radioValue) $el.checked = false"
                    :label="$subState['name']"
                    :name="'traitid-' . $id  . '[]'"
                    :value="$subId"
                    :checked="$isChildCoded"
                    />
                @endforeach
                </div>
            @endforeach
        </div>
        @endisset
    </div>
    @endforeach
</div>
@endif
