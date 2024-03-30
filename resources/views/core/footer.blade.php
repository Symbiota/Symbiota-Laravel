@props(['logos' => [], 'grants' => []])
<footer class="text-center">
    <div class="flex flex-wrap justify-center">
        @foreach ($logos as $logo)
        <div class="flex flex-grow justify-center my-auto">
            <div class="mx-auto w-fit h-fit">
                <a href="{{ $logo['link'] }}" title="{{ $logo['title'] }}" target="_blank">
                    <img class="max-w-52 max-h-20" src="{{ $logo['img'] }}" />
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        <div class="text-xs">
            @if(!empty($grants))
            <p>
                This project made possible by National Science Foundation Awards
                @for ($i = 0; $i < count($grants); $i++)
                    <x-link href="https://www.nsf.gov/awardsearch/showAward?AWD_ID={{$grants[$i]['grant_id']}}" target="_blank">
                       {{$grants[$i]['label']}}
                    </x-link>
                    @if ($i < count($grants) - 1) , @endif
                @endfor
            </p>
            @endif
            <p>
                For more information about Symbiota,
                <x-link href="https://symbiota.org/docs" target="_blank" rel="noreferrer">
                    read the docs
                </x-link>
                or contact the
                <x-link href="https://symbiota.org/contact-the-support-hub/" target="_blank" rel="noreferrer">
                    Symbiota Support Hub
                </x-link>
            </p>
            <p class="text">
                Powered by
                <x-link href="https://symbiota.org/" target="_blank">
                    Symbiota
                </x-link>
            </p>
        </div>
    </div>
</footer>
