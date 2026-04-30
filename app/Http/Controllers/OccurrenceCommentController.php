<?php

namespace App\Http\Controllers;

use App\Models\Occurrence;
use App\Models\OccurrenceComment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class OccurrenceCommentController extends Controller {
    /**
     * Helper function to constrain comment view logic
     *
     **/
    private static function view(Occurrence $occurrence, ?MessageBag $errors = null): View {
        return view('occurrence/comments', [
            'occurrence' => $occurrence,
            'comments' => OccurrenceComment::getCommentsWithUsername($occurrence),
            'comment_errors' => $errors,
        ]);
    }

    /**
     * Helper function to isolate update comment -> render pipeline
     *
     * Note this a helper don't pass in user input into fields.
     *
     **/
    private static function updateComment(int $occid, int $comid, array $fields): View {
        $updated = OccurrenceComment::query()
            ->where('occid', $occid)
            ->where('comid', $comid)
            ->update($fields);

        $occurrence = Occurrence::fromKey($occid);

        return self::view($occurrence);
    }

    public static function post(int $occid): View {
        $user = request()->user();
        $input = request()->input();
        $errors = null;

        $validator = Validator::make($input, [
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        } else {
            $new_comment = new OccurrenceComment();
            $new_comment->fill($input);
            $new_comment->uid = $user->uid;
            $new_comment->occid = $occid;
            $new_comment->save();
        }
        $occurrence = Occurrence::fromKey($occid);

        return self::view($occurrence, $errors);
    }

    public static function delete(int $occid, int $comid): View {
        $occurrence = Occurrence::fromKey($occid);
        $comment = OccurrenceComment::query()->where('occid', $occid)->where('comid', $comid)->first();
        $user = request()->user();

        if ($user && $comment->uid === $user->uid || Gate::check('COLL_EDIT', $occurrence->collid)) {
            $comment->delete();
        }

        return self::view($occurrence);
    }

    public static function report(int $occid, int $comid): View {
        return self::updateComment($occid, $comid, ['reviewstatus' => 2]);
    }

    public static function public(int $occid, int $comid): View {
        return self::updateComment($occid, $comid, ['reviewstatus' => 1]);
    }
}
