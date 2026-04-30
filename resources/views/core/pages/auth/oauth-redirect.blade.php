<x-layout class="flex items-center justify-center" :hasHeader="false" :hasFooter="false" :hasNavbar="false">
    <div class="border-base-300 h-fit border p-4">
        <div class="text-xl">Orcid Redirect</div>
        Request From:
        <x-link href="{{ request()->header('referer') }}"> {{ request()->header('referer') }} </x-link>
    </div>
</x-layout>
