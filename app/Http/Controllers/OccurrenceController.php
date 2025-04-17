<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaType;
use App\Models\OccurrenceComment;
use App\Models\OccurrenceIdentification;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller {
    public static function profilePage(int $occid) {
        $occurrence = DB::table('omoccurrences as o')
            ->join('omcollections as c', 'c.collID', 'o.collid')
            ->where('o.occid', '=', $occid)
            ->select('o.*', 'c.icon', 'c.collectionName', 'c.institutionCode', 'c.contactJson', 'c.rights')
            ->first();

        $media = Media::where('occid', $occid)->get();
        $determinations = OccurrenceIdentification::where('occid', $occid)->get();
        $identifiers = DB::table('omoccuridentifiers')->where('occid', $occid)->get();
        $comments = OccurrenceComment::getCommentsWithUsername($occid);

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
}
