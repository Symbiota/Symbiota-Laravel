@props(['variant' => 'primary', 'href', 'async' => false])
@php
$variant_def = match($variant) {
 "neutral" => ['bg-neutral', 'text-neutral-content', 'hover:bg-neutral-lighter', 'active:bg-neutral-darker'],
 "accent" => ['bg-accent','text-accent-content','hover:bg-accent-lighter','active:bg-accent-darker'],
 "secondary" => ['bg-secondary', 'text-secondary-content', 'hover:bg-secondary-lighter', 'active:bg-secondary-darker'],
 "error" => ['bg-error', 'text-error-content', 'hover:bg-error-lighter', 'active:bg-error-darker'],
 "clear-primary" => ['bg-transparent', 'text-primary border-primary border-2'],
  Default => ['bg-primary','text-primary-content','hover:bg-primary-lighter', 'active:bg-primary-darker', 'disabled:grayscale'],
};

$base = 'rounded-lg shadow-xl focus:ring hover:ring-4 hover:ring-accent focus:ring-accent focus:outline-none text-base font-bold flex items-center gap-1 py-1 px-2.5 h-fit w-fit';
$tag = 'button';

if(isset($href) && $href) {
    $tag = 'a';
}

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
                await new Promise(resolve => setTimeout(resolve, 1000))
                el.disabled = false;
                this.loading = false;
                return true;
            }
        }));
    })
</script>
@endPushOnce
<{{$tag}} {{isset($href) && $href ? 'href=' . $href: ''}} {{$async? 'x-data="loadingUtils" @click="() => load($el)"': ''}} {{ $attributes->class($variant_def)->twMerge($base)}}>
    {{ $slot }}
    @if(isset($icon) && !$icon->isEmpty())
    <div x-cloak x-show="loading" >
       {{ $icon }}
    </div>
    @endif
</{{$tag}}>
