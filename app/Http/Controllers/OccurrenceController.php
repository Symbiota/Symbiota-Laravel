<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaType;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller {
    public static function profilePage(int $occid) {
        $occurrence = DB::table('omoccurrences as o')
            ->join('omcollections as c', 'c.collID', 'o.collid')
            ->where('o.occid', '=', $occid)
            ->select('o.*', 'c.icon', 'c.collectionName', 'c.institutionCode')
            ->first();

        $media = Media::query()
            ->select('*')
            ->where('occid', $occid)
            ->get();

        $images = [];
        $audio = [];

        foreach ($media as $item) {
            $type = MediaType::tryFrom($item->mediaType);

            if($type == MediaType::Image) {
                $images[] = $item;
            } else if($type == MediaType::Audio) {
                $audio[] = $item;
            }
        }
        return view('pages/occurrence/profile', ['occurrence' => $occurrence, 'images' => $images, 'audio' => $audio]);
    }

    public static function editPage(int $occid) {
        $occurrence = DB::table('omoccurrences as o')
            ->select('*')
            ->where('o.occid', '=', $occid)
            ->first();

        return view('pages/occurrence/editor', ['occurrence' => $occurrence]);

    }
}
