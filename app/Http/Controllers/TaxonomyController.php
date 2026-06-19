<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaxonomyController extends Controller {
    private static $rankMap = [
            0 => 1, // non-ranked node
            1 => 2, // organism
            10 => 3, // kingdom
            20 => 4, // subkingdom
            30 => 5, // division
            40 => 6, // subdivision
            50 => 7, // superclass
            60 => 8, // class
            70 => 9, // subclass
            100 => 10, // order
            110 => 11, // suborder
            140 => 12, // family
            150 => 13, // subfamily
            160 => 14, // tribe
            170 => 15, // subtribe
            180 => 16, // genus
            190 => 17, // subgenus
            200 => 18, // section
            210 => 19, // subsection
            220 => 20, // species
            300 => 21, // infraspecies
        ];

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
        $viewData = [
            'taxon' => self::taxonData($tid),
            'parents' => self::getParents($tid),
            'common_names' => self::getCommonNames($tid),
            'occurrence_count' => self::getTaxonOccurrenceStats($tid),
            'children' => self::getDirectChildren($tid, 1),
            'taxa_descriptions' => self::getTaxaDescriptions($tid),
            'external_links' => self::getExternalLinks($tid),
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

    private static function redirectBackWithError(string $error) {
        return redirect()->back()->withInput()->withErrors(['error' => $error]);
    }

    private static function redirectToRouteIndexWithError(string $route, string $error) {
        return redirect()->route($route)->withErrors(['error' => $error]);
    }

    private static function redirectBackWithManagerIssues($editorManager, string $warningTranslationKey = 'taxonomy_taxoneditor.FOLLOWING_WARNINGS') {
        if ($editorManager->getWarningArr()) {
            $statusStr = __($warningTranslationKey) . ': ' . implode(';', $editorManager->getWarningArr());

            return self::redirectBackWithError($statusStr);
        }

        if ($statusStr = $editorManager->getErrorMessage()) {
            return self::redirectBackWithError($statusStr);
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

        return $editorManager->getErrorMessage();
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

        return $editorManager->getErrorMessage();
    }

    private static function processUpdateAction(string $editType, $editorManager, array $postData) {
        return match ($editType) {
            'taxonedits' => self::handleTaxonEditsAction($editorManager, $postData),
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

    public static function taxonData(int $tid) {
        $taxon = DB::table('taxa as t')
            ->leftJoin('taxstatus as ts', 'ts.tid', 't.tid')
            ->where('t.tid', $tid)
            ->where('ts.taxauthid', 1)
            ->select(
                't.*',
                'ts.tidaccepted',
                'ts.taxauthid',
                'ts.parenttid',
                'ts.family',
                'ts.taxonomicStatus',
                'ts.taxonomicSource',
                'ts.sourceIdentifier',
                'ts.UnacceptabilityReason',
                'ts.notes as statusNotes',
                'ts.SortSequence',
                'ts.modifiedUid as statusModifiedUid',
                'ts.modifiedTimestamp as statusModifiedTimestamp',
                'ts.initialtimestamp as statusInitialTimestamp'
            )
            ->first();

        return $taxon;
    }

    public static function getParents(int $tid): array {
        $parent_tree = DB::select('with RECURSIVE parents as (
	SELECT * from taxstatus where tid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, parents as p where ts.tid = p.parenttid and ts.taxauthid = 1 and ts.tid != 1
) SELECT DISTINCT taxa.tid, sciName, parents.family, parenttid, taxa.rankID, rankname
            from parents join taxa on taxa.tid = parents.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName order by taxa.rankID', [$tid]);

        return $parent_tree;
    }

    public static function getDirectChildren(int $tid, int $displayAuthor = 0) {
        $query = DB::table('taxa as t')
            ->join('taxstatus as ts', 'ts.tid', 't.tid')
            ->leftJoin('media as m', function (JoinClause $query) {
                $query->on('m.tid', 't.tid')
                    ->where('m.mediaType', 'image');
            })
            ->join('taxonunits as tu', function (JoinClause $query) {
                $query->on('tu.rankid', 't.rankID')
                    ->whereRaw('tu.kingdomName = t.kingdomName');
            })->where('ts.taxauthid', 1)
            ->where('ts.parenttid', $tid)
            ->groupBy('t.tid')
            ->select(array_filter(['t.tid', 'sciName', $displayAuthor ? 't.author' : null, 'ts.family', 'parenttid', 't.rankID', 'rankname', DB::raw('COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl')]));

        $direct_children = $query->get();

        foreach ($direct_children as $child) {
            if (! $child->thumbnailUrl) {
                DB::table('media')->where($child->tid);
            }
        }

        return $direct_children;
    }

    // Be very Careful when calling this function can be very slow depending on the tid
    public static function getAllChildren(int $tid): array {
        $child_tree = DB::select('with RECURSIVE children as (
	SELECT * from taxstatus where parenttid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, children as c where ts.parenttid = c.tid and ts.taxauthid = 1 and ts.tidaccepted = ts.tid
) SELECT taxa.tid, sciName, children.family, parenttid, taxa.rankID, rankname, COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl
            from children join taxa on taxa.tid = children.tid left join media as m on m.tid = taxa.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName group by taxa.tid order by taxa.rankID', [$tid]);

        return $child_tree;
    }

    public static function getCommonNames(int $tid) {
        $common_names = DB::table('taxavernaculars')->where('tid', $tid)->select('*')->get();

        return $common_names;
    }

    public static function getTaxonOccurrenceStats(int $tid) {
        $occurrence_count = DB::table('omoccurrences')->where('tidInterpreted', $tid)->count('*');

        return $occurrence_count;
    }

    public static function getExternalLinks(int $tid) {
        $external_links_query = DB::table('taxaresourcelinks as trl')
            ->where('trl.tid', $tid)
            ->select('*');

        return $external_links_query
            ->get();
    }

    public static function getTaxaDescriptions(int $tid) {
        $statements = DB::table('taxadescrblock as tdb')->join('taxadescrstmts as tds', 'tds.tdbid', 'tdb.tdbid')
            ->where('tdb.tid', $tid)
            ->select('tdProfileID', 'source', 'sourceUrl', 'heading', 'statement')
            ->get();

        $taxa_descriptions = [];

        foreach ($statements as $statement) {
            if ($taxa_descriptions[$statement->tdProfileID] ?? false) {
                $taxa_descriptions[$statement->tdProfileID]['statements'][$statement->heading] = $statement->statement;
            } else {
                $taxa_descriptions[$statement->tdProfileID] = [
                    'source' => $statement->source,
                    'sourceUrl' => $statement->sourceUrl,
                    'statements' => [],
                ];
            }
        }

        return $taxa_descriptions;
    }

    public static function taxon(int $tid) {
        $tid = (int) $tid;
        if (! self::taxonData($tid)) {
            return self::redirectToRouteIndexWithError('taxon.index', 'Unable to load taxon profile because the taxon was not found.');
        }

        return view('pages/taxon/profile', self::buildTaxonViewData($tid));
    }

    public static function editTaxonProfile(int $tid) {
        $tid = (int) $tid;
        if (! self::taxonData($tid)) {
            return self::redirectToRouteIndexWithError('taxon.index', 'Unable to load taxon profile editor because the taxon was not found.');
        }

        return view('pages/taxon/edit', self::buildTaxonViewData($tid, true));
    }

    public static function editTaxon($tid) {
        $tid = (int) $tid;
        $taxon = self::taxonData($tid);

        if (! $taxon) {
            return self::redirectToRouteIndexWithError('taxon.index', 'Unable to load taxon editor because the taxon was not found.');
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
            'parents' => self::getParents($tid),
            'rankMap' => self::$rankMap,
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

    private static function normalizeOptionalInt(mixed $value): ?int {
        if ($value === null) {
            return null;
        }

        if (is_string($value) && trim($value) === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private static function normalizeCreatePayload(array $postData): array {
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
        $normalized['rankid'] = self::normalizeOptionalInt($normalized['rankid'] ?? null) ?? 0;
        $normalized['securitystatus'] = self::normalizeOptionalInt($normalized['securitystatus'] ?? null) ?? 0;

        $normalizedParentTid = self::normalizeOptionalInt($normalized['parenttid'] ?? null);
        if ($normalizedParentTid === null && $normalized['parentname'] !== '') {
            $normalizedParentTid = DB::table('taxa')
                ->where('sciName', $normalized['parentname'])
                ->value('tid');
        }
        $normalized['parenttid'] = $normalizedParentTid;

        $normalizedTidAccepted = self::normalizeOptionalInt($normalized['tidaccepted'] ?? null);
        if ($normalized['acceptstatus'] === 0 && $normalizedTidAccepted === null && $normalized['acceptedstr'] !== '') {
            $normalizedTidAccepted = DB::table('taxa')
                ->where('sciName', $normalized['acceptedstr'])
                ->value('tid');
        }
        $normalized['tidaccepted'] = $normalizedTidAccepted;

        return $normalized;
    }

    private static function resolveUpdateTid(array $postData, $editorManager): ?int {
        $tid = self::normalizeOptionalInt($postData['update-tid'] ?? null);

        if ($tid !== null) {
            return $tid;
        }

        if (method_exists($editorManager, 'getTid')) {
            return self::normalizeOptionalInt($editorManager->getTid());
        }

        return null;
    }

    public static function store() {
        $postData = self::normalizeCreatePayload(request()->all());

        if ($postData['acceptstatus'] === 0 && ! $postData['tidaccepted']) {
            return self::redirectBackWithError(__('taxonomy_taxonomyloader.ACC_NAME_NEEDS_VALUE'));
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
            return redirect()->back()->withInput()->withErrors(['error' => $tidResult]); // @TODO fix this in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
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
            $parents = self::getParents($parentTid);
        }
        // @TODO get each parent's children
        foreach ($parents as $parent) {
            $parent->children = self::getDirectChildren($parent->tid, $displayAuthor);
        }

        return view('pages/taxon/show', [
            'parents' => $parents,
            'rankMap' => self::$rankMap,
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

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]); // @TODO fix this in issue
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

            return self::redirectBackWithError($statusStr); // @TODO fix this in issue
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
            $redirectTid = self::normalizeOptionalInt($redirectParams['tid'] ?? null);

            if ($redirectTid === null) {
                return self::redirectBackWithError('Unable to redirect to taxon profile because the taxon ID was missing.');
            }

            $redirectParams['tid'] = $redirectTid;
        }

        return redirect()->route($redirectRoute, $redirectParams)->with('success', $statusStr);
    }
}
