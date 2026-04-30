<x-layout>
    <div class="mx-auto flex w-[32rem] flex-col gap-2">
        <h1 class="text-2xl font-bold">Checklist Not Found</h1>
        <hr />
        <p>This checklist does not exist or the access has been restricted. If this is unexpected make sure you are signed in.</p>

        <div class="mt-4 flex gap-4">
            @if(request()->header('referer') && url()->current() != request()->header('referer'))
                <x-button href="{{ request()->header('referer') }}">Back</x-button>
            @endif
            <x-button hx-boost="true" href="{{ url('checklists') }}">Public Checklists</x-button>
        </div>
    </div>
</x-layout>
