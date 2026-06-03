<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Occurrence;

class OccurrenceEditorController extends Controller {
    public static function editPage(int $occId) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/Media.php');

        $occurrence = Occurrence::query()->where('occid', $occId)->first();

        $media = \Media::fetchOccurrenceMedia($occId);
        $media_tags = \Media::getMediaTags(array_keys($media));
        $collection = Collection::get($occurrence->collid);

        return view('pages/occurrence/editor', [
            'occurrence' => $occurrence,
            'collection' => $collection,
            'creators' => \Media::getCreatorArray(),
            'tags' => \Media::getMediaTagKeys(),
            'media' => $media,
            'media_tags' => $media_tags,
        ]);
    }
}
