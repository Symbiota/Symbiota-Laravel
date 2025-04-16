<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaType;
use App\Models\Occurrence;
use App\Models\OccurrenceIdentification;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller {
    public static function profilePage(int $occid) {
        $occurrence = DB::table('omoccurrences as o')
            ->join('omcollections as c', 'c.collID', 'o.collid')
            ->where('o.occid', '=', $occid)
            ->select('o.*', 'c.icon', 'c.collectionName', 'c.institutionCode', 'c.contactJson')
            ->first();

        $media = Media::where('occid', $occid)->get();
        $identification = OccurrenceIdentification::where('occid', $occid)->get();

        $collection_contacts = false;
        try {
            $collection_contacts = json_decode($occurrence->contactJson);
        } finally {}

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

        return view('pages/occurrence/profile', [
            'occurrence' => $occurrence,
            'images' => $images,
            'audio' => $audio,
            'collection_contacts' => $collection_contacts
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
