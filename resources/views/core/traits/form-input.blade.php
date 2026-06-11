@props(['traits', 'traitId', 'root' => true, 'depth' => 0])

@php
    $props = json_decode($traits[$traitId]['props']);
    $type = $props? $props[0]->controlType: 'radio';
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
    <x-select
        :id="'traitid-' . $traitId  . '[]'"
        :defaultValue="$default"
        :label="$traits[$traitId]['name']"
        :items="$state_items"
        :inline="true"
    />
@elseif($depth < 3)
    @if($type === 'checkbox')
        <div @class(["flex flex-col gap-2", 'pl-4' => !$root])>
            @if(!$root)
                <div class="font-bold">{{ $traits[$traitId]['name'] }}</div>
            @endif
            @foreach($traits[$traitId]['states'] as $sid => $state)
                @php
            $coded = array_key_exists('coded', $state) && (is_numeric($state['coded'])? $state['coded']: true);
            @endphp
                <div x-data="{ checked: {{ $coded? 'true': 'false' }}}">
                    <x-checkbox
                        :checked="$coded"
                        :label="$state['name']"
                        :name="'traitid-' . $traitId  . '[]'"
                        :value="$sid"
                        @change="checked = $event.target.checked"
                    />

                    @isset($state['dependTraitID'])
                        <div
                            class="flex flex-col gap-1"
                            x-show="checked"
                            x-effect="window.setDisabledAll($el, 'input', !checked)"
                        >
                            @foreach($state['dependTraitID'] as $id)
                                <div @class(["flex gap-2 pl-4"])>
                                    <x-traits.form-input
                                        :traits="$traits"
                                        :traitId="$id"
                                        :root="false"
                                        :depth="$depth + 1"
                                    />
                                </div>
                            @endforeach
                        </div>
                    @endisset
                </div>
            @endforeach
        </div>
    @elseif($type === 'radio')
        <fieldset @class(["flex flex-col gap-2", 'pl-4' => !$root]) x-data="{ sid: false }">
            @if(!$root)
                <legend class="font-bold">{{ $traits[$traitId]['name'] }}</legend>
            @endif
            @foreach($traits[$traitId]['states'] as $sid => $state)
                @php
            $coded = array_key_exists('coded', $state) && (is_numeric($state['coded'])? $state['coded']: true);
            @endphp
                <x-radio.item
                    :checked="$coded"
                    :label="$state['name']"
                    :name="'traitid-' . $traitId  . '[]'"
                    :value="$sid"
                    @change="sid = $event.target.value"
                />
                @isset($state['dependTraitID'])
                    <div
                        class="flex flex-col gap-1"
                        x-show="sid === '{{ $sid }}'"
                        x-effect="window.setDisabledAll($el, 'input', sid !== '{{ $sid }}')"
                    >
                        @foreach($state['dependTraitID'] as $id)
                            <div @class(["flex gap-2 pl-4"])>
                                <x-traits.form-input
                                    :traits="$traits"
                                    :traitId="$id"
                                    :root="false"
                                    :depth="$depth + 1"
                                />
                            </div>
                        @endforeach
                    </div>
                @endisset
            @endforeach
        </fieldset>
    @endif
@endif
