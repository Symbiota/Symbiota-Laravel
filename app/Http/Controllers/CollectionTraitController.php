<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

class CollectionTraitController extends Controller {
    private static function attributeManager(int $collId) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/OccurrenceAttributes.php');

        $attrManager = new \OccurrenceAttributes();
        $attrManager->setCollid($collId);
        $attrManager->setFilterAttributes(request()->all());

        $taxonFilter = $attrManager->getFilterAttribute('taxonfilter');
        $tidFilter = $attrManager->getFilterAttribute('tidfilter');
        $reviewUid = $attrManager->getFilterAttribute('reviewuid');
        $reviewDate = $attrManager->getFilterAttribute('reviewdate');
        $reviewStatus = $attrManager->getFilterAttribute('reviewstatus');
        $sourceFilter = $attrManager->getFilterAttribute('sourcefilter');
        $localFilter = $attrManager->getFilterAttribute('localfilter');

        return $attrManager;
    }

    const EDIT = 1;

    const REVIEW = 2;

    private static function getPageData($attrManager, $mode) {
        $traitID = request('traitid');
        $imgArr = [];
        $occid = 0;
        $catNum = '';

        if ($traitID) {
            $imgRetArr = [];
            if ($mode == self::EDIT) {
                $imgRetArr = $attrManager->getImageUrls();
                $imgArr = current($imgRetArr);
            } elseif ($mode == self::REVIEW) {
                $imgRetArr = $attrManager->getReviewUrls($traitID);
                if ($imgRetArr) {
                    $imgArr = current($imgRetArr);
                }
            }
            if ($imgRetArr) {
                $catNum = $imgArr['catnum'];
                unset($imgArr['catnum']);
                $occid = key($imgRetArr);
                if ($occid) {
                    $attrManager->setOccid($occid);
                }
            }
        }

        return [
            'attrManager' => $attrManager,
            'traitID' => $traitID,
            'images' => $imgArr,
            'occid' => $occid,
            'catNum' => $catNum,
            'mode' => $mode == self::REVIEW ? self::REVIEW : self::EDIT,
        ];
    }

    public static function editor(int $collId) {
        $attrManager = self::attributeManager($collId);
        $mode = self::getMode($collId);

        return view('pages/collections/trait-editor',
            self::getPageData($attrManager, $mode)
        );
    }

    private static function getMode(int $collId) {
        $mode = request('mode') == self::REVIEW ? self::REVIEW : self::EDIT;
        $canReview = Gate::check('COLL_ADMIN', $collId);

        if ($mode === self::REVIEW && ! $canReview) {
            $mode = self::EDIT;
        }

        return $mode;
    }

    public static function getImages(int $collId) {
        $attrManager = self::attributeManager($collId);
        $mode = self::getMode($collId);

        return view('traits/image-form',
            self::getPageData($attrManager, $mode)
        );
    }

    public static function save(int $collId) {
        $attrManager = self::attributeManager($collId);
        $attrManager->setOccid(request('targetoccid'));
        $mode = self::getMode($collId);

        $errors = [];

        try {
            if ($mode === self::REVIEW) {
                $attrManager->editAttributes(request()->all());
            } elseif ($mode === self::EDIT) {
                if (! $attrManager->addAttributes(request()->all(), request()->user()->uid)) {
                    $errors = message_bag([$attrManager->getErrorMessage()]);
                }
            }
        } catch (\Throwable $th) {
            $errors = message_bag([$th->getMessage()]);
        }

        $pageData = self::getPageData($attrManager, $mode);
        $pageData['errors'] = $errors;

        return view('traits/image-form', $pageData);
    }
}
