@props(['children'=>[],])
@if(count($children) < 1)
    <p>{{ __('taxonomy_taxonomyloader.NO_CHILD_TAXA') }}</p>
@else
    <ul>
        @foreach($children as $childTid =>$child)
            <li>
                <div>
                    <x-link href="{{ url('taxon/' . $childTid) }}">
                        <i>{{ $child['sciname'] ?? '' }}</i> {{ $child['author'] ?? '' }}
                    </x-link>
                    @if(!empty($child['accTid']) && intval($child['accTid']) !== $childTid)
                        <span>&#10140</span>
                        <x-link href="{{ url('taxon/' . $child['accTid']) }}">
                            <i>{{ $child['accSciname'] ?? $child['sciname'] ?? '' }}</i> {{ $child['accAuthor'] ?? '' }}
                        </x-link>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
