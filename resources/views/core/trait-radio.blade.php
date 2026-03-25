@props(['traits', 'traitId'])

{{ print_r($traits[$traitId]['props']) }}
<div class="font-bold text-lg">{{$traits[$traitId]['name']}}</div>
@foreach ($traits[$traitId]['states'] as $sid => $state)
<div>
{{ $state['name'] }}
@isset($state['dependTraitID'])
<div class="pl-4">
    @foreach($state['dependTraitID'] as $id)
        <div>{{ $traits[$id]['name'] }}</div>
        <div class="pl-4">
        @foreach($traits[$id]['states'] as $subState)
            <div>{{ $subState['name'] }}</div>
        @endforeach
        </div>
    @endforeach
</div>
@endisset
</div>
@endforeach
