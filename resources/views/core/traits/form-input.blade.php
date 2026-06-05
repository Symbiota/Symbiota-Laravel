@props(['traits', 'traitId',
// 'sid', 'state',
'root' => true, 'depth' => 0])

@php
    $props = json_decode($traits[$traitId]['props']);
    $type = $props? $props[0]->controlType: 'radio';

    if(!$root) {
        $type ='select';
    }
@endphp

@if($type === 'select')
    @php
    $state_items = [];
    $default = false;
    foreach($traits[$traitId]['states'] as $sid => $state) {
        if(array_key_exists('coded', $state)) {
            $default = $sid;
        }
        $state_items[] = item($sid, $state['name']);
    }
    @endphp
    <x-select :defaultValue="$default" :label="$traits[$traitId]['name']" :items="$state_items" :inline="true"/>
@elseif($depth < 3)
<div x-data="{ sid: false }" class="flex flex-col gap-2 pl-4">
    @if(!$root)
        <div>{{ $traits[$traitId]['name'] }}</div>
    @endif
    @foreach($traits[$traitId]['states'] as $sid => $state)
        @php
        $isCoded = false;
        if(array_key_exists('coded', $state)) {
            $isCoded = is_numeric($state['coded'])?
            $state['coded']: true;
        }
        @endphp

        @switch($type)
            @case('radio')
                <x-radio.item
                    :checked="$isCoded"
                    :label="$state['name']"
                    :name="'traitid-' . $traitId  . '[]'"
                    :value="$sid"
                    @change="sid = $event.target.value"
                />
                @break
            @case('checkbox')
                <x-checkbox
                    :checked="$isCoded"
                    :label="$state['name']"
                    :name="'traitid-' . $traitId  . '[]'"
                    :value="$sid"
                    @change="sid = $event.target.value"
                />
                @break
            @default
                default
                @break
        @endswitch

        @isset($state['dependTraitID'])
        <div class="flex flex-col gap-1 pl-8" x-show="sid === '{{ $sid }})'">
            @foreach($state['dependTraitID'] as $id)
                <div @class(["flex gap-2 pl-4"])>
                    <x-traits.form-input :traits="$traits" :traitId="$id" :root="false" :depth="$depth + 1"/>
                </div>
            @endforeach
        </div>
        @endisset
    @endforeach
</div>
@endif
