@props(['type' => 'primary', 'async' => false])
@php
$type_def = match($type) {
 "neutral" => ['bg-neutral', 'text-neutral-content', 'hover:bg-neutral-lighter', 'active:bg-neutral-darker'],
 "accent" => ['bg-accent','text-accent-content','hover:bg-accent-lighter','active:bg-accent-darker'],
 "secondary" => ['bg-secondary', 'text-secondary-content', 'hover:bg-secondary-lighter', 'active:bg-secondary-darker' ],
  Default => ['bg-primary','text-primary-content','hover:bg-primary-lighter', 'active:bg-primary-darker'],
};
@endphp
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    document.addEventListener('alpine:init', e => {
        Alpine.data('loadingUtils', () => ({
            loading: false,
            async load(el) {
                console.log('load')
                this.loading = true;
                el.disabled = true;
                await new Promise(resolve => setTimeout(resolve, 5000))
                el.disabled = false;
                this.loading = false;
            }
        }));
    })
</script>
@endPushOnce

{{-- Tailwind class that are concated are in in safe list to ensure their inclusion--}}
{{-- btn bg-{{$type}} text-{{$type}}-content  --}}
<button x-data="loadingUtils" @click="() => load($el)" {{ $attributes->class($type_def)->twMerge('rounded-lg shadow-xl focus:ring-4 hover:ring-4 hover:ring-accent focus:ring-accent focus:outline-none text-xl font-bold flex items-center gap-1 py-1 px-6 h-fit')}} >
    {{ $slot }}
    @if(isset($icon) && !$icon->isEmpty())
    <div x-cloak x-show="loading" >
       {{ $icon }}
    </div>
    @endif
</button>
