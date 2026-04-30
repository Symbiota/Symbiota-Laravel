@props(['logos' => [], 'grants' => []])
<footer {{ $attributes->twMerge('bg-footer text-footer-content p-8 text-center') }}>
    @if(!empty($logos))
        <div class="m-auto flex max-w-[1024px] flex-wrap justify-center">
            @foreach($logos as $logo)
                <div class="my-auto flex flex-grow basis-0 justify-center">
                    <div class="mx-auto h-fit w-fit">
                        <a href="{{ $logo['link'] }}" title="{{ $logo['title'] }}" target="_blank">
                            <img class="max-h-20 max-w-52" src="{{ $logo['img'] }}" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-4">
        <div class="text-xs">
            @if(!empty($grants))
                <p>
                    {{ __('header.F_NSF_AWARDS') }}
                    @for($i = 0; $i < count($grants); $i++)
                        <x-link
                            class="text-xs"
                            href="https://www.nsf.gov/awardsearch/showAward?AWD_ID={{ $grants[$i]['grant_id'] }}"
                            target="_blank"
                        >
                            {{ $grants[$i]['label'] }}
                        </x-link>
                        @if($i < count($grants) - 1) ,@endif
                    @endfor
                </p>
            @endif
            <p>
                {{ __('header.F_MORE_INFO') }},

                <x-link class="text-xs" href="{{ docs_url() }}" target="_blank" rel="noreferrer">
                    {{ __('header.F_READ_DOCS') }},
                </x-link>
                {{ __('header.F_CONTACT') }},
                <x-link
                    class="text-xs"
                    href="https://symbiota.org/contact-the-support-hub/"
                    target="_blank"
                    rel="noreferrer"
                >
                    {{ __('header.F_SSH') }},
                </x-link>
            </p>
            <p>
                {{ __('header.F_POWERED_BY') }},
                <x-link class="text-xs" href="https://symbiota.org/" target="_blank"> Symbiota </x-link>
            </p>
        </div>
    </div>
</footer>
