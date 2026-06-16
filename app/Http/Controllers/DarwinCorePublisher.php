<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class DarwinCorePublisher extends Controller {
    private static function getDwcaPublisher() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/DwcArchiverPublisher.php');

        return new \DwcArchiverPublisher();
    }

    public static function buildCollectionArchive() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/OccurrenceCollectionProfile.php');
        include_once legacy_path('/classes/utilities/GeneralUtil.php');
        include_once legacy_path('/classes/utilities/Language.php');

        $collManager = new OccurrenceCollectionProfile();

        $dwcaManager = self::getDwcaPublisher();
        $dwcaManager->setVerboseMode(3);
        $dwcaManager->setLimitToGuids(true);

        if (request('collid')) {
            $dwcaManager->setCollArr(request('collid'));
            if ($dwcaManager->createDwcArchive()) {
                $dwcaManager->writeRssFile();
            }
        }

        return view('pages/collections/publisher');
    }

    public static function publishMany(Request $request) {
        $collIds = request('coll');
        $dwcaManager = self::getDwcaPublisher();
        $dwcaManager->setVerboseMode(2);
        $dwcaManager->setLimitToGuids(true);
        $dwcaManager->setRedactLocalities(request('redact')? 1: 0);
        $dwcaManager->setIncludeDets(request('dets')? 1: 0);
        $dwcaManager->setIncludeImgs(request('imgs')? 1: 0);
        $dwcaManager->setTargetPath('dwca-pub');

        foreach($collIds as $id){
            $dwcaManager->resetCollArr($id);
            $includeAttributes = request('attributes') && $dwcaManager->hasAttributes($id)? 1: 0;
            $includeMatSample = request('matsample') && $dwcaManager->hasMaterialSamples($id)? 1: 0;
            $includeIdentifiers = request('identifiers') && $dwcaManager->hasIdentifiers($id)? 1: 0;
            $includeAssociations = request('associations') && $dwcaManager->hasAssociations($id)? 1: 0;

            $dwcaManager->setIncludeAttributes($includeAttributes);
            $dwcaManager->setIncludeMaterialSample($includeMatSample);
            $dwcaManager->setIncludeIdentifiers($includeIdentifiers);
            $dwcaManager->setIncludeAssociations($includeAssociations);

            if($dwcaManager->createDwcArchive()){
                $dwcaManager->writeRssFile();
                // $collManager->batchTriggerGBIFCrawl(array($id));
            }
        }
    }

    public static function publisherPage() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/OccurrenceCollectionProfile.php');
        include_once legacy_path('/classes/utilities/GeneralUtil.php');
        include_once legacy_path('/classes/utilities/Language.php');

        $dwcaManager = self::getDwcaPublisher();
        $dwcaArr = $dwcaManager->getDwcaItems();

        for ($i = 0; $i < count($dwcaArr); $i++) {
            $dwcaArr[$i]['size'] = $dwcaManager->humanFileSize($dwcaArr[$i]['link']);
        }

        $collection_lookup = Collection::getLookup();

        //dd(array_map(fn($v) => $v->toArray(), $collection_lookup));

        return view('pages/collections/publisher', ['archives' => $dwcaArr, 'collection_lookup' => $collection_lookup]);
    }
}
