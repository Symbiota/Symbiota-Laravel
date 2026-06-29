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
        $label_image = null;

        foreach ($media as $id => $resource) {
            foreach (['url', 'sourceUrl', 'thumbnailUrl', 'originalUrl'] as $field) {
                if (substr($resource[$field], 0, 1) == '/') {
                    $media[$id][$field] = config('portal.media_domain') . $resource[$field];
                }
            }

            if ($label_image === null && $resource['mediaType'] === \MediaType::Image) {
                $label_image = $media[$id];
            }
        }

        return view('pages/occurrence/editor', [
            'occurrence' => $occurrence,
            'collection' => $collection,
            'identifiers' => $occurrence->getIdentifiers(),
            'creators' => \Media::getCreatorArray(),
            'tags' => \Media::getMediaTagKeys(),
            'media' => $media,
            'media_tags' => $media_tags,
            'label_image' => $label_image,
        ]);
    }
}
