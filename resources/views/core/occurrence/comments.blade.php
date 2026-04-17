@props(['comments', 'occurrence'])

@if (Auth::check())
<form hx-post="{{ url('occurrence/' . $occurrence->occid . '/comment') }}" hx-target="#comment-tab" hx-swap="innerHTML" class="grid grid-cols-1 gap-2">
    @csrf
    <x-input area :label="__('individual.NEW_COMMENT')" id="comment-input" name="comment" rows="8"/>
    <x-button type="submit" class="w-fit">
        {{ __('individual.SUBMIT_COMMENT') }}
    </x-button>
    <p>{{ __('individual.MESSAGE_WARNING') }}</p>
</form>
@endif

<x-errors :errors="$comment_errors ?? []"/>

@if(count($comments))
<div class="flex flex-col gap-4">
@foreach ($comments as $comment)
    <div class="p-4 border border-base-300 flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <span class="font-medium">{{ $comment->username}}</span>
            <span class="text-base-content/50">posted {{ $comment->initialtimestamp }}</span>
            <span class="flex-grow flex justify-end gap-2">
                {{-- TODO (Logan) report functionality --}}

                @if($comment->reviewstatus != 2)
                <form hx-patch="{{ url('occurrence/' . $occurrence->occid . '/comment/' . $comment->comid . '/report') }}"
                    hx-target="#comment-tab"
                    hx-swap="innerHTML"
                    >
                    @csrf
                    <x-button variant="error">
                        {{ __('individual.REPORT') }}
                    </x-button>
                </form>
                @endif

                @php $user = request()->user(); @endphp
                @if($user)
                    @if ($user && $user->uid == $comment->uid || Gate::check('COLL_EDIT', [$occurrence->collid]))

                        @if($comment->reviewstatus == 2)
                        <form hx-patch="{{ url('occurrence/' . $occurrence->occid . '/comment/' . $comment->comid . '/public') }}"
                            hx-target="#comment-tab"
                            hx-swap="innerHTML">
                            @csrf
                            <x-button>
                                {{ __('individual.MAKE_COMMENT_PUBLIC') }}
                            </x-button>
                        </form>
                        @endif

                        <form hx-delete="{{ url('occurrence/' . $occurrence->occid . '/comment/' . $comment->comid) }}"
                            hx-target="#comment-tab"
                            hx-swap="innerHTML"
                            hx-confirm="{{ __('individual.CONFIRM_DELETE') }}">
                            @csrf
                            <x-button variant="error">
                                {{ __('individual.DELETE_COMMENT') }}
                            </x-button>
                        </form>
                    @endif
                @endif
            </span>
        </div>
        <div>
            {{ $comment->comment }}
        </div>
        @if($comment->reviewstatus == 2)
            <div class="p-2 bg-error text-error-content rounded-md w-fit">
                {{ __('individual.COMMENT_NOT_PUBLIC') }}
            </div>
        @endif
    </div>
@endforeach
</div>
@else
<div class="text-lg font-bold">{{ __('individual.NO_COMMENTS') }}</div>
@endif
