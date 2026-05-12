<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

class CollectionTraitController extends Controller {
    private static function attributeManager($collId) {
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

    private const ATTR_MINING_FIELDS = [
        'habitat' => 'Habitat',
        'substrate' => 'Substrate',
        'occurrenceremarks' => 'Occurrence Remarks (notes)',
        'dynamicproperties' => 'Dynamic Properties',
        'verbatimattributes' => 'Verbatim Attributes (description)',
        'behavior' => 'Behavior',
        'reproductivecondition' => 'Reproductive Condition',
        'lifestage' => 'Life Stage',
        'sex' => 'Sex',
    ];

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

    // List the collection IDs for current user.
    private static function editableCollectionIds(): array {
        $user = request()->user();

        if (! $user || Gate::check('SUPER_ADMIN')) {
            return [];
        }

        return $user->collections()
            ->pluck('collid')
            ->map(fn ($collid) => (int) $collid)
            ->all();
    }

    //turn legacy collid input into the comma-separated string for OccurrenceAttributes.
    private static function resolveMiningCollid(?int $collId = null): string {
        if ($collId !== null) {
            return (string) $collId;
        }

        $isAdmin = Gate::check('SUPER_ADMIN');
        $editableIds = self::editableCollectionIds();
        $requestedCollid = request('collid', '');

        if (! $isAdmin && count($editableIds) === 1 && ! $requestedCollid && ! request('selectall')) {
            return (string) current($editableIds);
        }

        if (request('selectall') || $requestedCollid === 'all') {
            return 'all';
        }

        $selectedIds = is_array($requestedCollid) ? $requestedCollid : explode(',', (string) $requestedCollid);
        $selectedIds = array_values(array_unique(array_filter(array_map(
            fn ($id) => is_numeric($id) ? (int) $id : null,
            $selectedIds
        ))));

        if (! $isAdmin) {
            $selectedIds = array_values(array_intersect($selectedIds, $editableIds));
        }

        return implode(',', $selectedIds);
    }

    // turn input into strings
    private static function normalizeInput(string $key): string {
        $value = request($key, '');

        return is_scalar($value) ? (string) $value : '';
    }

    private static function miningRequestData(?int $collId = null): array {
        $collid = self::resolveMiningCollid($collId);
        $traitID = is_numeric(request('traitid')) ? (int) request('traitid') : 0;
        $tidFilter = is_numeric(request('tidfilter')) ? (int) request('tidfilter') : 0;
        $fieldName = self::normalizeInput('fieldname');

        if (! array_key_exists($fieldName, self::ATTR_MINING_FIELDS)) {
            $fieldName = '';
        }

        return [
            'collid' => $collid,
            'traitID' => $traitID,
            'fieldName' => $fieldName,
            'stringFilter' => self::normalizeInput('stringfilter'),
            'taxonFilter' => self::normalizeInput('taxonfilter'),
            'tidFilter' => $tidFilter,
            'submitForm' => self::normalizeInput('submitform'),
        ];
    }

    // Build the data shared by the selection, filtering, and batch-assignment mining states.
    private static function getMiningPageData(?int $collId = null): array {
        $requestData = self::miningRequestData($collId);
        $attrManager = self::attributeManager($requestData['collid']);
        $editableIds = self::editableCollectionIds();
        $collArr = $attrManager->getCollectionList(Gate::check('SUPER_ADMIN') ? '' : $editableIds);
        $fieldValues = [];
        $traitArr = [];

        if ($requestData['collid'] && $requestData['traitID'] && $requestData['fieldName']) {
            $fieldValues = $attrManager->getFieldValueArr(
                $requestData['traitID'],
                $requestData['fieldName'],
                $requestData['tidFilter'],
                $requestData['stringFilter']
            );
            $traitArr = $attrManager->getTraitArr($requestData['traitID'], false);
        }

        return array_merge($requestData, [
            'attrManager' => $attrManager,
            'collArr' => $collArr,
            'fieldArr' => self::ATTR_MINING_FIELDS,
            'fieldValues' => $fieldValues,
            'traitArr' => $traitArr,
            'miningErrors' => message_bag([]),
        ]);
    }

    // assign in bathces selected trait states to all occurrences that match the selected verbatim field values.
    private static function submitMiningAttributes($attrManager, array $pageData) {
        $fieldValueArr = request('fieldvalue', []);
        $fieldValueArr = is_array($fieldValueArr) ? $fieldValueArr : [$fieldValueArr];
        $stateIDArr = [];

        foreach (request()->all() as $postKey => $postValue) {
            if (str_starts_with($postKey, 'traitid-')) {
                $stateIDArr = array_merge($stateIDArr, is_array($postValue) ? $postValue : [$postValue]);
            }
        }

        $messages = [];
        if (! $fieldValueArr) {
            $messages[] = __('traitattr_attributemining.MUST_SELECT_FIELD_VALUE');
        }
        if (! $stateIDArr) {
            $messages[] = __('traitattr_attributemining.CHOOSE_ONE_STATE');
        }

        if ($messages) {
            return message_bag($messages);
        }

        if (! $attrManager->submitBatchAttributes(
            $pageData['traitID'],
            $pageData['fieldName'],
            $pageData['tidFilter'],
            $stateIDArr,
            $fieldValueArr,
            request('notes'),
            request('reviewstatus')
        )) {
            return message_bag([$attrManager->getErrorMessage()]);
        }

        return message_bag([]);
    }

    public static function mining(?int $collId = null) {
        $pageData = self::getMiningPageData($collId);

        if (request()->isMethod('post') && $pageData['submitForm'] === 'Harvest from Collections' && ! $pageData['collid']) {
            $pageData['miningErrors'] = message_bag([__('traitattr_attributemining.SELECT_COLLECT_TO_HARVEST')]);
        } elseif (request()->isMethod('post') && $pageData['submitForm'] === 'Get Field Values') {
            $messages = [];
            if (! $pageData['traitID']) {
                $messages[] = __('traitattr_attributemining.MUST_SELECT_TRAIT');
            }
            if (! $pageData['fieldName']) {
                $messages[] = __('traitattr_attributemining.MUST_SELECT_SOURCE_FIELD');
            }
            $pageData['miningErrors'] = message_bag($messages);
        } elseif (request()->isMethod('post') && $pageData['submitForm'] === 'Batch Assign State(s)') {
            $pageData['miningErrors'] = self::submitMiningAttributes($pageData['attrManager'], $pageData);
            $pageData = array_merge(self::getMiningPageData($collId), [
                'miningErrors' => $pageData['miningErrors'],
            ]);
        }

        return view('pages/collections/attribute-mining', $pageData);
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
