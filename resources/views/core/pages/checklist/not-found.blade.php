<x-layout>
    <div class="flex flex-col gap-2 mx-auto w-[32rem]">
        <h1 class="font-bold text-2xl">
            Checklist Not Found
        </h1>
        <hr/>
        <p>
            This checklist does not exist or the access has been restricted. If this is unexpected make sure you are signed in.
        </p>

        <div class="flex gap-4 mt-4">
        @if(request()->header('referer') && url()->current() != request()->header('referer'))
        <x-button href="{{ request()->header('referer') }}">Back</x-button>
        @endif
        <x-button hx-boost="true" href="{{ url('checklists') }}">Public Checklists</x-button>
        </div>
    </div>
</x-layout>
