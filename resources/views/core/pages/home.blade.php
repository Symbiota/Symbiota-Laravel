@php $LANG_TAG = App::currentLocale(); @endphp
<x-layout>
    <div class="navpath"></div>
    <div id="innertext" class="w-[90%] max-w-screen-lg mx-auto py-12">
        <div>
            <h1 class="text-3xl my-3 font-bold font-sans text-primary">{!! __('home.welcome-title') !!}</h1>
            <p class="text-base font-sans">{!! __('home.welcome-text') !!}</p>
        </div>
    </div>
</x-layout>
