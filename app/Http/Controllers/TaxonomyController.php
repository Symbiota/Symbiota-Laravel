<?php

namespace App\Http\Controllers;

use App\Helpers\InputNormalizer;
use App\Helpers\RedirectResponseHelper;
use App\Models\Taxonomy;
use App\Services\TaxonomyQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaxonomyController extends Controller {
    private static function getTaxonomyEditorManager($tid = null) {
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $taxonEditorObj = new \TaxonomyEditorManager();
        $tid = (int) $tid;
        if ($tid) {
            $taxonEditorObj->setTid($tid);
            $taxonEditorObj->setTaxon();
        }

        return $taxonEditorObj;
    }

    private static function buildTaxonViewData(int $tid, bool $includeMedia = false): array {
        $taxonomy = Taxonomy::find($tid);
        $viewData = [
            'taxon' => TaxonomyQueryService::taxonData($tid),
            'parents' => TaxonomyQueryService::getParents($tid),
            'common_names' => $taxonomy?->commonNames()->get() ?? collect(),
            'occurrence_count' => $taxonomy?->occurrences()->count() ?? 0,
            'children' => TaxonomyQueryService::getDirectChildren($tid, 1),
            'taxa_descriptions' => TaxonomyQueryService::getTaxaDescriptions($tid),
            'external_links' => $taxonomy?->externalLinks()->get() ?? collect(),
        ];

        if ($includeMedia) {
            $viewData['media'] = DB::table('media')
                ->where('tid', $tid)
                ->select('*')
                ->orderBy('sortSequence')
                ->get();
        }

        return $viewData;
    }

    private static function buildTaxonFormOptions(): array {
        $kingdoms = DB::table('taxa')->where('rankID', 10)->select('tid', 'sciName')->get();
        $primaryKingdom = config('portal.primary_taxonomic_kingdom');
        $allTaxonRanks = DB::table('taxonunits')->distinct()->select('rankid', 'rankname')->where('kingdomName', $primaryKingdom)->orderBy('rankid')->orderBy('rankname', 'desc')->get();
        $indContent = [['title' => '', 'value' => '', 'disabled' => false], ['title' => '×', 'value' => '×', 'disabled' => false]];
        $securityOptions = [['title' => 'No Security', 'value' => 0, 'disabled' => false], ['title' => 'Hide Locality Details', 'value' => 1, 'disabled' => false]];
        ! empty($GLOBALS['ACTIVATE_PALEO_DAGGER']) ? $indContent[] = ['title' => '†', 'value' => '†', 'disabled' => false] : null;

        return [
            'kingdoms' => $kingdoms,
            'allTaxonRanks' => $allTaxonRanks,
            'indContent' => $indContent,
            'securityOptions' => $securityOptions,
            'canCreateOrEdit' => Gate::check('TAXON_EDITOR'),
        ];
    }

    private static function redirectBackWithManagerIssues($editorManager, string $warningTranslationKey = 'taxonomy_taxoneditor.FOLLOWING_WARNINGS') {
        if ($editorManager->getWarningArr()) {
            $statusStr = __($warningTranslationKey) . ': ' . implode(';', $editorManager->getWarningArr());

            return RedirectResponseHelper::backWithError($statusStr);
        }

        if ($statusStr = $editorManager->getErrorMessage()) {
            return RedirectResponseHelper::backWithError($statusStr);
        }

    }

    private static function handleTaxonEditsAction($editorManager, array $postData) {
        return $editorManager->submitTaxonEdits($postData);
    }

    private static function handleUpdateTaxStatusAction($editorManager, array $postData) {
        return $editorManager->submitTaxStatusEdits($postData['parenttid'], $postData['tidaccepted']);
    }

    private static function handleSynonymEditsAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;

        return $editorManager->submitSynonymEdits($postData['tidsyn'], $tid, $postData['unacceptabilityreason'], $postData['notes'], $postData['sortsequence']);
    }

    private static function handleLinkToAcceptedAction($editorManager, array $postData) {
        $deleteOther = array_key_exists('deleteother', $postData);

        return $editorManager->submitAddAcceptedLink($postData['tidaccepted'], $deleteOther);
    }

    private static function handleDeleteAcceptedLinkAction($editorManager, array $postData) {
        return $editorManager->removeAcceptedLink($postData['deltidaccepted']);
    }

    private static function handleChangeToAcceptedAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;
        $tidAccepted = $postData['tidaccepted'];
        $switchAcceptance = array_key_exists('switchacceptance', $postData);

        return $editorManager->submitChangeToAccepted($tid, $tidAccepted, $switchAcceptance);
    }

    private static function handleChangeToNotAcceptedAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;
        $tidAccepted = $postData['tidaccepted'];

        return $editorManager->submitChangeToNotAccepted($tid, $tidAccepted, $postData['unacceptabilityreason'], $postData['notes']);
    }

    private static function handleUpdateHierarchyAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;
        $editorManager->rebuildHierarchy($tid);

        return true;
    }

    private static function handleRemapTaxonAction($editorManager, array $postData) {
        $statusStr = '';
        $remapStatus = $editorManager->transferResources($postData['remaptid']);
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());
        }

        if ($remapStatus) {
            return __('taxonomy_taxoneditor.SUCCESS_REMAPPING') . ' ' . $statusStr;
        }

        return $editorManager->getErrorMessage(); // @TODO could this leverage one of the error handling methods?
    }

    private static function handleDeleteTaxonAction($editorManager) {
        $statusStr = '';
        $delStatus = $editorManager->deleteTaxon();
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());
        }

        if ($delStatus) {
            return __('taxonomy_taxonomydelete.SUCCESS_DELETING') . ' ' . $statusStr;
        }

        return $editorManager->getErrorMessage(); // @TODO could this leverage one of the error handling methods?
    }

    private static function processUpdateAction(string $editType, $editorManager, array $postData) {
        return match ($editType) {
            'taxonedits' => self::handleTaxonEditsAction($editorManager, $postData), // @TODO many of these are moot now. Track and delete those that are uneccessary
            'updatetaxstatus' => self::handleUpdateTaxStatusAction($editorManager, $postData),
            'synonymedits' => self::handleSynonymEditsAction($editorManager, $postData),
            'linkToAccepted' => self::handleLinkToAcceptedAction($editorManager, $postData),
            'deltidaccepted' => self::handleDeleteAcceptedLinkAction($editorManager, $postData),
            'changetoaccepted' => self::handleChangeToAcceptedAction($editorManager, $postData),
            'changeToNotAccepted' => self::handleChangeToNotAcceptedAction($editorManager, $postData),
            'updatehierarchy' => self::handleUpdateHierarchyAction($editorManager, $postData),
            'remapTaxon' => self::handleRemapTaxonAction($editorManager, $postData),
            'deleteTaxon' => self::handleDeleteTaxonAction($editorManager),
            default => 'Unsupported edit type',
        };
    }

    public static function taxon(int $tid) {
        $tid = (int) $tid;
        if (! TaxonomyQueryService::taxonData($tid)) {
            return RedirectResponseHelper::routeWithError('taxon.index', 'Unable to load taxon profile because the taxon was not found.');
        }

        return view('pages/taxon/profile', self::buildTaxonViewData($tid));
    }

    public static function editTaxonProfile(int $tid) {
        $tid = (int) $tid;
        if (! TaxonomyQueryService::taxonData($tid)) {
            return RedirectResponseHelper::routeWithError('taxon.index', 'Unable to load taxon profile editor because the taxon was not found.');
        }

        return view('pages/taxon/edit', self::buildTaxonViewData($tid, true));
    }

    public static function editTaxon($tid) {
        // @TODO add build editTaxonData method with taxonInfo and upperTaxonomyEditInfo (this part already done)
        $tid = (int) $tid;
        $taxon = TaxonomyQueryService::taxonData($tid);

        if (! $taxon) {
            return RedirectResponseHelper::routeWithError('taxon.index', 'Unable to load taxon editor because the taxon was not found.');
        }

        $taxonInfo = $taxon;
        $securitystatusstart = $taxon->securitystatus ?? 0;
        $formOptions = self::buildTaxonFormOptions();

        $parentName = '';
        if ($taxon && $taxon->parenttid) {
            $parentName = DB::table('taxa')->where('tid', $taxon->parenttid)->value('sciName') ?? '';
        }

        $acceptedName = '';
        if ($taxon && $taxon->tidaccepted && $taxon->tidaccepted != $taxon->tid) {
            $acceptedName = DB::table('taxa')->where('tid', $taxon->tidaccepted)->value('sciName') ?? '';
        }

        $taxonEditorObj = self::getTaxonomyEditorManager($tid);
        $verifyArr = $taxonEditorObj->verifyDeleteTaxon();
        $taxonEditorObj->setTaxon();
        $taxonInfo->synonyms = $taxonEditorObj->getSynonyms();
        $taxonInfo->isAccepted = $taxonEditorObj->getIsAccepted();
        $taxonInfo->acceptedArr = [];
        $taxonInfo->taxonAuthId = $taxonEditorObj->getTaxAuthId();
        $taxonInfo->children = $taxonEditorObj->getChildren();
        if ($taxonEditorObj->getIsAccepted() != 1) {
            $taxonInfo->acceptedArr = $taxonEditorObj->getAcceptedArr();
        }

        if (! empty($verifyArr['child'])) {
            $verifyArr['child'] = array_map(
                fn ($name, $url) => ['name' => $name, 'url' => $url],
                $verifyArr['child'],
                array_map(fn ($key) => url('/taxon/' . $key), array_keys($verifyArr['child']))
            );
        }
        $upperTaxonomyEditInfo = self::prepareUpperTaxonomyEditInfo($taxonEditorObj);

        // @TODO condense props into taxonInfo and maybe upperTaxonomyEditInfo
        return view('pages/taxon/editTaxon', array_merge($formOptions, [
            'mode' => 'edit',
            'targetTid' => request()->route('tid'),
            'taxonInfo' => $taxonInfo,
            'parentName' => $parentName,
            'acceptedName' => $acceptedName,
            'securitystatusstart' => $securitystatusstart,
            'verifyArr' => $verifyArr,
            'parents' => TaxonomyQueryService::getParents($tid),
            'rankMap' => Taxonomy::RANK_MAP,
            'upperTaxonomyEditInfo' => $upperTaxonomyEditInfo,
        ]));
    }

    private static function prepareUpperTaxonomyEditInfo($taxonEditorObj) {
        $upperTaxonomyEditInfo = [];
        $upperTaxonomyEditInfo['acceptedArr'] = $taxonEditorObj->getAcceptedArr();
        $upperTaxonomyEditInfo['tid'] = $taxonEditorObj->getTid();
        $upperTaxonomyEditInfo['isAccepted'] = $taxonEditorObj->getIsAccepted();
        $upperTaxonomyEditInfo['parentNameFull'] = strip_tags(html_entity_decode((string) ($taxonEditorObj->getParentNameFull() ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $upperTaxonomyEditInfo['rankId'] = $taxonEditorObj->getRankId();
        $upperTaxonomyEditInfo['family'] = $taxonEditorObj->getFamily();
        $upperTaxonomyEditInfo['parentTid'] = $taxonEditorObj->getParentTid();
        $upperTaxonomyEditInfo['parentName'] = $taxonEditorObj->getParentName();
        $upperTaxonomyEditInfo['taxauthid'] = $taxonEditorObj->getTaxauthid();

        return $upperTaxonomyEditInfo;
    }

    public static function createTaxon() {
        return view('pages/taxon/create', array_merge(self::buildTaxonFormOptions(), [
            'mode' => 'create',
            'securitystatusstart' => 0,
        ]));
    }

    private static function normalizeCreatePayload(array $postData): array { // @TODO this seems long-winded. DRY up if possible
        $normalized = $postData;

        $stringFields = [
            'author',
            'unitind1',
            'unitname1',
            'unitind2',
            'unitname2',
            'unitind3',
            'unitname3',
            'cultivarEpithet',
            'tradeName',
            'source',
            'notes',
            'parentname',
            'acceptedstr',
            'unacceptabilityreason',
        ];

        foreach ($stringFields as $field) {
            $normalized[$field] = trim((string) ($normalized[$field] ?? ''));
        }

        $normalized['acceptstatus'] = ((int) ($normalized['acceptstatus'] ?? 1) === 1) ? 1 : 0;
        $normalized['rankid'] = InputNormalizer::optionalInt($normalized['rankid'] ?? null) ?? 0;
        $normalized['securitystatus'] = InputNormalizer::optionalInt($normalized['securitystatus'] ?? null) ?? 0;

        $normalizedParentTid = InputNormalizer::optionalInt($normalized['parenttid'] ?? null);
        if ($normalizedParentTid === null && $normalized['parentname'] !== '') {
            $normalizedParentTid = DB::table('taxa')
                ->where('sciName', $normalized['parentname'])
                ->value('tid');
        }
        $normalized['parenttid'] = $normalizedParentTid;

        $normalizedTidAccepted = InputNormalizer::optionalInt($normalized['tidaccepted'] ?? null);
        if ($normalized['acceptstatus'] === 0 && $normalizedTidAccepted === null && $normalized['acceptedstr'] !== '') {
            $normalizedTidAccepted = DB::table('taxa')
                ->where('sciName', $normalized['acceptedstr'])
                ->value('tid');
        }
        $normalized['tidaccepted'] = $normalizedTidAccepted;

        return $normalized;
    }

    private static function resolveUpdateTid(array $postData, $editorManager): ?int {
        $tid = InputNormalizer::optionalInt($postData['update-tid'] ?? null);

        if ($tid !== null) {
            return $tid;
        }

        if (method_exists($editorManager, 'getTid')) {
            return InputNormalizer::optionalInt($editorManager->getTid());
        }

        return null;
    }

    public static function store() {
        $postData = self::normalizeCreatePayload(request()->all());

        if ($postData['acceptstatus'] === 0 && ! $postData['tidaccepted']) {
            return RedirectResponseHelper::backWithError(__('taxonomy_taxonomyloader.ACC_NAME_NEEDS_VALUE'));
        }

        $editorManager = self::getTaxonomyEditorManager();

        // if (! $editorManager->validateNewName($postData)) {
        //     // Redirect back with error message
        //     return redirect()->back()->withInput()->withErrors(['error' => 'Validation failed for the new taxon. Please check your input and try again.']);
        // } else { // @TODO to be fixed in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
        $tidResult = $editorManager->loadNewName($postData);
        // }

        if ($tidResult > 0) { // @TODO use redirectBackWithManagerIssues here after some massaging
            // Redirect to the newly created taxon's page
            return redirect()->route('taxon.view', ['tid' => $tidResult])->with('success', 'Taxon created successfully!');
        } else {
            return RedirectResponseHelper::backWithError((string) $tidResult); // @TODO fix this in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
        }
    }

    public static function show(Request $request, $tid = null) {
        $targetTid = $tid ?? null;
        $taxonName = null;
        if ($targetTid) {
            $taxon = Taxonomy::findOrFail($targetTid);
            $taxonName = $taxon->sciName;
        }
        $parents = [];
        $parentTid = $request->filled('parenttid') ? (int) $request->input('parenttid') : null;
        $displayAuthor = $request->filled('displayauthor') ? (int) $request->input('displayauthor') : 0;
        // if($displayAuthor){
        //     include_once legacy_path('/classes/TaxonomyDisplayManager.php');
        //     $taxonomyDisplayManager = new \TaxonomyDisplayManager();
        //     $taxonomyDisplayManager->setDisplayAuthor($displayAuthor);

        // }
        if ($parentTid) {
            $parents = TaxonomyQueryService::getParents($parentTid);
        }
        // @TODO get each parent's children
        foreach ($parents as $parent) {
            $parent->children = TaxonomyQueryService::getDirectChildren($parent->tid, $displayAuthor);
        }

        return view('pages/taxon/show', [
            'parents' => $parents,
            'rankMap' => Taxonomy::RANK_MAP,
            'targetTid' => $targetTid,
            'taxonName' => $taxonName,
        ]);
    }

    public static function update() {
        $postData = request()->all();
        $editorManager = self::getTaxonomyEditorManager($postData['update-tid'] ?? null);
        $editType = $postData['edit-type'] ?? '';
        $editorManager->setTaxAuthId($postData['acceptedstatus'] ?? null); // @TODO I don't think that this is accurate - taxAuthId just happens to be 1 in this case
        $statusStr = self::processUpdateAction($editType, $editorManager, $postData);
        $resolvedTid = self::resolveUpdateTid($postData, $editorManager);

        return self::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.view', ['tid' => $resolvedTid]);
    }

    public static function delete() {
        $tid = (int) request()->all()['tid'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($tid);
        $delStatus = $editorManager->deleteTaxon();
        if ($editorManager->getWarningArr()) {
            $statusStr = implode('; ', $editorManager->getWarningArr());
        }
        if ($delStatus) {
            $statusStr = __('taxonomy_taxonomydelete.SUCCESS_DELETING');

            return redirect()->route('taxon.createview')->with('success', $statusStr);
        } else {
            $statusStr = $editorManager->getErrorMessage();

            return RedirectResponseHelper::backWithError($statusStr); // @TODO fix this in issue
        }
    }

    public static function remap() {
        $requestData = request()->all();
        $tid = (int) $requestData['tid'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($tid);

        $remapStatus = $editorManager->transferResources((int) $requestData['remaptid']);
        $statusStr = $requestData['taxa'] ?? '';
        if ($response = self::redirectBackWithManagerIssues($editorManager)) { // @TODO is there a way to use handleStatusReportingAndRouting here?
            return $response;
        }
        if ($remapStatus) {
            $statusStr = __('taxonomy_taxoneditor.SUCCESS_REMAPPING') . ' ' . $statusStr;
            TaxonomyController::delete();

            return redirect()->route('taxon.view', ['tid' => $requestData['remaptid']])->with('success', $statusStr);
        } else {
            $statusStr = $editorManager->getErrorMessage();

            return RedirectResponseHelper::backWithError($statusStr); // @TODO fix this in issue
        }
    }

    public static function changeAccepted() {
        $requestData = request()->all();
        $oldTid = (int) $requestData['tid'] ?? null;
        $targetTid = (int) $requestData['tidaccepted'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($oldTid);
        $statusStr = $editorManager->submitChangeToAccepted($targetTid, $oldTid); // not the order I would have written this method signature, but not worth the refactor in the old code base yet
        $statusStr = __('taxonomy_taxoneditor.SYNONYM_SUCCESS') . ' ' . $statusStr;

        return self::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.view', ['tid' => $targetTid]);
    }

    public static function changeToNotAccepted() {
        $requestData = request()->all();
        $oldTid = (int) $requestData['tid'] ?? null;
        $targetTid = (int) $requestData['new-tid'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($oldTid);
        $switchAcceptance = $requestData['switchacceptance'] === '1';
        $statusStr = $editorManager->submitChangeToAccepted($oldTid, $targetTid, $switchAcceptance);
        $statusStr = __('taxonomy_taxoneditor.ACCEPTANCE_STATUS_CHANGE_SUCCESS') . ' ' . $statusStr;

        return self::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.editview', ['tid' => $oldTid]);
    }

    public static function updateSynonymLink() {
        $requestData = request()->all();
        $currentTid = (int) $requestData['current-tid'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($currentTid);
        $statusStr = $editorManager->submitSynonymEdits($requestData['tidsyn'], $currentTid, $requestData['unacceptabilityreason'], $requestData['notes'], $requestData['sortsequence']);

        return self::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.editview', ['tid' => $currentTid]);
    }

    public static function reconstructHierarchy() {
        $tid = (int) request()->all()['tid'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($tid);
        $editorManager->rebuildHierarchy($tid);

        return self::handleStatusReportingAndRouting(__('taxonomy_taxoneditor.HIERARCHY_REBUILD_SUCCESS'), $editorManager, 'taxon.editview', ['tid' => $tid]);
    }

    public static function updateUpperTaxonomy() {
        $requestData = request()->all();
        $tid = (int) $requestData['tid'] ?? null;
        $editorManager = self::getTaxonomyEditorManager($tid);
        $statusStr = $editorManager->submitTaxStatusEdits($requestData['newparenttid'] ?? '', $requestData['tidaccepted'] ?? '');

        return self::handleStatusReportingAndRouting(__('taxonomy_taxonomyloader.UPPER_TAXONOMY_UPDATE_SUCCESS') . ' ' . $statusStr, $editorManager, 'taxon.editview', ['tid' => $tid]);
    }

    public static function handleStatusReportingAndRouting($statusStr, $editorManager, $redirectRoute, $redirectParams = []) {
        if ($response = self::redirectBackWithManagerIssues($editorManager)) {
            return $response;
        }

        if (in_array($redirectRoute, ['taxon.view', 'taxon.editview', 'taxon.profileEdit'], true)) {
            $redirectTid = InputNormalizer::optionalInt($redirectParams['tid'] ?? null);

            if ($redirectTid === null) {
                return RedirectResponseHelper::backWithError('Unable to redirect to taxon profile because the taxon ID was missing.');
            }

            $redirectParams['tid'] = $redirectTid;
        }

        return redirect()->route($redirectRoute, $redirectParams)->with('success', $statusStr);
    }
}
