@props(['traits', 'traitId'])
@php
$props = json_decode($traits[$traitId]['props']);
$type = $props? $props[0]->controlType: 'radio';
@endphp

@if($type === 'select')
{{-- todo select part of traits --}}
@else
<div x-data="{ radioValue: null}" {{ $attributes->twMerge('flex flex-col gap-2')}}>
    <div class="font-bold">{{$traits[$traitId]['name']}}</div>
    @foreach ($traits[$traitId]['states'] as $sid => $state)
    <div x-data="{ parentValue: null }">
        <x-radio.item x-init="parentValue = $el.value" @change="radioValue = $el.value" :label="$state['name']" :name="'traitid-' . $traitId  . '[]'" :value="$sid" />
        @isset($state['dependTraitID'])
        <div class="pl-4" x-show="parentValue === radioValue">
            @foreach($state['dependTraitID'] as $id)
                <div>{{ $traits[$id]['name'] }}</div>
                <div class="pl-4 flex  gap-2">
                @foreach($traits[$id]['states'] as $subState)
                    <x-radio.item
                    x-effect="if(parentValue !== radioValue) $el.checked = false"
                    :label="$subState['name']"
                    :name="'traitid-' . $id  . '[]'"
                    :value="$id"
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
