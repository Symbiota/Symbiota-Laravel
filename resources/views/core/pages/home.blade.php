@php $LANG_TAG = App::currentLocale(); @endphp
<x-margin-layout>
    <div>
        <h1 class="text-3xl my-3 font-bold font-sans text-primary">{!! __('home.welcome-title') !!}</h1>
        <p class="text-base font-sans">{!! __('home.welcome-text') !!}</p>
    </div>
</x-layout>
