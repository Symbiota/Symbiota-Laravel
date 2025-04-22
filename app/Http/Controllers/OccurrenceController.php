<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaType;
use App\Models\OccurrenceComment;
use App\Models\OccurrenceEdit;
use App\Models\OccurrenceIdentification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OccurrenceController extends Controller {
    private static function occurrenceProfileData(int $occid) {
        return DB::table('omoccurrences as o')
            ->join('omcollections as c', 'c.collID', 'o.collid')
            ->where('o.occid', '=', $occid)
            ->select('o.*', 'c.icon', 'c.collectionName', 'c.institutionCode', 'c.contactJson', 'c.rights')
            ->first();
    }

    public static function profilePage(int $occid) {
        $occurrence = self::occurrenceProfileData($occid);
        $media = Media::where('occid', $occid)->get();
        $determinations = OccurrenceIdentification::where('occid', $occid)->get();
        $identifiers = DB::table('omoccuridentifiers')->where('occid', $occid)->get();
        $comments = OccurrenceComment::getCommentsWithUsername($occurrence);

        $linked_checklists = DB::table('fmvouchers as v')
            ->join('fmchecklists as c', 'c.clid', 'v.clid')
            ->where('v.occid', $occid)
            ->distinct()
            ->select('c.*')
            ->get();

        $linked_datasets = DB::table('omoccurdatasetlink as l')
            ->join('omoccurdatasets as d', 'd.datasetID', 'l.datasetID')
            ->where('l.occid', $occid)
            ->distinct()
            ->where('isPublic', 1)
            ->select('d.*')
            ->get();

        $editHistory = [];

        if(Gate::check('COLL_EDIT', $occurrence->collid)) {
            $editHistory = OccurrenceEdit::getGroupedByEdit($occid);
        }

        $collection_contacts = false;
        try {
            $collection_contacts = json_decode($occurrence->contactJson);
        } finally {
        }

        $images = [];
        $audio = [];

        foreach ($media as $item) {
            $type = MediaType::tryFrom($item->mediaType);

            if ($type == MediaType::Image) {
                $images[] = $item;
            } elseif ($type == MediaType::Audio) {
                $audio[] = $item;
            }
        }

        return view('pages/occurrence/profile', [
            'occurrence' => $occurrence,
            'images' => $images,
            'audio' => $audio,
            'identifiers' => $identifiers,
            'collection_contacts' => $collection_contacts,
            'determinations' => $determinations,
            'comments' => $comments,
            'editHistory' => $editHistory,
            'linked_checklists' => $linked_checklists,
            'linked_datasets' => $linked_datasets,
        ]);
    }

    public static function editPage(int $occid) {
        $occurrence = DB::table('omoccurrences as o')
            ->select('*')
            ->where('o.occid', '=', $occid)
            ->first();

        return view('pages/occurrence/editor', ['occurrence' => $occurrence]);

    }

    public static function postComment(int $occid) {
        $user = request()->user();
        $input = request()->input();
        $errors = [];

        $validator = Validator::make($input, [
            'comment' => ['required', 'string'],
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
        } else {
            $new_comment = new OccurrenceComment();
            $new_comment->fill($input);
            $new_comment->uid = $user->uid;
            $new_comment->occid = $occid;
            $new_comment->save();
        }
        $occurrence = self::occurrenceProfileData($occid);
        return view('pages/occurrence/profile', [
            'occurrence' => $occurrence,
            'comments' => OccurrenceComment::getCommentsWithUsername($occurrence),
            'comment_errors' => $errors,
        ])->fragment('comments');
    }

    public static function deleteComment(int $occid, int $comid) {
        $occurrence =self::occurrenceProfileData($occid);
        $comment = OccurrenceComment::where('occid', $occid)->where('comid', $comid)->first();
        $user = request()->user();

        if($user && $comment->uid === $user->uid || Gate::check('COLL_EDIT', $occurrence->collid)) {
            $comment->delete();
        }

        return view('pages/occurrence/profile', [
            'occurrence' => $occurrence,
            'comments' => OccurrenceComment::getCommentsWithUsername($occurrence),
        ])->fragment('comments');
    }

    private static function updateComment(int $occid, int $comid, array $fields) {
        $updated = OccurrenceComment::where('occid', $occid)->where('comid', $comid)->update($fields);

        $occurrence = self::occurrenceProfileData($occid);

        return view('pages/occurrence/profile', [
            'occurrence' => $occurrence,
            'comments' => OccurrenceComment::getCommentsWithUsername($occurrence),
        ])->fragment('comments');
    }

    public static function reportComment(int $occid, int $comid) {
        return self::updateComment($occid, $comid, ['reviewstatus' => 2]);
    }

    public static function publicComment(int $occid, int $comid) {
        return self::updateComment($occid, $comid, ['reviewstatus' => 1]);
    }
}
