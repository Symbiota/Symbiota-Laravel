<?php

namespace App\Http\Controllers;

use App\Helpers\RedirectResponseHelper;
use App\Models\Taxonomy;
use App\Services\PayloadNormalizer;
use App\Services\TaxonomyMutationService;
use App\Services\TaxonomyQueryService;
use App\Services\TaxonResponseHandler;
use App\Services\TaxonViewDataService;
use Illuminate\Http\Request;

class TaxonomyController extends Controller {
    public static function taxon(int $tid) {
        if (! TaxonomyQueryService::taxonData($tid)) {
            return RedirectResponseHelper::routeWithError('taxon.index', __('taxonomy_taxonomyloader.TAXON_NOT_FOUND'));
        }

        return view('pages/taxon/profile', TaxonViewDataService::buildTaxonViewData($tid));
    }

    public static function editTaxonProfile(int $tid) {
        if (! TaxonomyQueryService::taxonData($tid)) {
            return RedirectResponseHelper::routeWithError('taxon.index', __('taxonomy_taxonomyloader.TAXON_NOT_FOUND'));
        }

        return view('pages/taxon/edit', TaxonViewDataService::buildTaxonViewData($tid, true));
    }

    public static function editTaxon($tid) {
        $taxonEditorObj = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $taxonInfo = TaxonViewDataService::prepareTaxonInfo($tid, $taxonEditorObj);

        if (! $taxonInfo) {
            return RedirectResponseHelper::routeWithError('taxon.index', __('taxonomy_taxonomyloader.TAXON_NOT_FOUND'));
        }

        $formOptions = TaxonViewDataService::buildTaxonFormOptions();
        $upperTaxonomyEditInfo = TaxonViewDataService::prepareUpperTaxonomyEditInfo($taxonEditorObj);

        return view('pages/taxon/editTaxon', array_merge($formOptions, [
            'taxonInfo' => $taxonInfo,
            'upperTaxonomyEditInfo' => $upperTaxonomyEditInfo,
        ]));
    }

    public static function createTaxon() {
        return view('pages/taxon/create', array_merge(TaxonViewDataService::buildTaxonFormOptions(), [
            'mode' => 'create',
            'securitystatusstart' => 0,
        ]));
    }

    public static function store() {
        $postData = PayloadNormalizer::normalizeCreatePayload(request()->all());

        if ($postData['acceptstatus'] === 0 && ! $postData['tidaccepted']) {
            return RedirectResponseHelper::backWithError(__('taxonomy_taxonomyloader.ACC_NAME_NEEDS_VALUE'));
        }

        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager();

        // if (! $editorManager->validateNewName($postData)) {
        //     // Redirect back with error message
        //     return redirect()->back()->withInput()->withErrors(['error' => 'Validation failed for the new taxon. Please check your input and try again.']);
        // } else { // @TODO to be fixed in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
        $tidResult = $editorManager->loadNewName($postData);
        // }

        if ($tidResult > 0) { // @TODO use redirectBackWithManagerIssues here after some massaging
            // Redirect to the newly created taxon's page
            return redirect()->route('taxon.view', ['tid' => $tidResult])->with('success', __('taxonomy_taxonomyloader.TAXON_CREATED_SUCCESSFULLY'));
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
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($postData['update-tid'] ?? null);
        $editType = $postData['edit-type'] ?? '';
        $editorManager->setTaxAuthId($postData['acceptedstatus'] ?? null); // @TODO I don't think that this is accurate - taxAuthId just happens to be 1 in this case
        $statusStr = TaxonomyMutationService::processUpdateAction($editType, $editorManager, $postData);
        $resolvedTid = TaxonResponseHandler::resolveUpdateTid($postData, $editorManager);

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.view', ['tid' => $resolvedTid]);
    }

    public static function delete() {
        $tid = (int) request()->all()['tid'] ?? null;
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);
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
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);

        $remapStatus = $editorManager->transferResources((int) $requestData['remaptid']);
        $statusStr = $requestData['taxa'] ?? '';
        if ($response = TaxonResponseHandler::redirectBackWithManagerIssues($editorManager)) { // @TODO is there a way to use handleStatusReportingAndRouting here?
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
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($oldTid);
        $statusStr = $editorManager->submitChangeToAccepted($targetTid, $oldTid); // not the order I would have written this method signature, but not worth the refactor in the old code base yet
        $statusStr = __('taxonomy_taxoneditor.SYNONYM_SUCCESS') . ' ' . $statusStr;

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.view', ['tid' => $targetTid]);
    }

    public static function changeToNotAccepted() {
        $requestData = request()->all();
        $oldTid = (int) $requestData['tid'] ?? null;
        $targetTid = (int) $requestData['new-tid'] ?? null;
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($oldTid);
        $switchAcceptance = $requestData['switchacceptance'] === '1';
        $statusStr = $editorManager->submitChangeToAccepted($oldTid, $targetTid, $switchAcceptance);
        $statusStr = __('taxonomy_taxoneditor.ACCEPTANCE_STATUS_CHANGE_SUCCESS') . ' ' . $statusStr;

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.editview', ['tid' => $oldTid]);
    }

    public static function updateSynonymLink() {
        $requestData = request()->all();
        $currentTid = (int) $requestData['current-tid'] ?? null;
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($currentTid);
        $statusStr = $editorManager->submitSynonymEdits($requestData['tidsyn'], $currentTid, $requestData['unacceptabilityreason'], $requestData['notes'], $requestData['sortsequence']);

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.editview', ['tid' => $currentTid]);
    }

    public static function reconstructHierarchy() {
        $tid = (int) request()->all()['tid'] ?? null;
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $editorManager->rebuildHierarchy($tid);

        return TaxonResponseHandler::handleStatusReportingAndRouting(__('taxonomy_taxoneditor.HIERARCHY_REBUILD_SUCCESS'), $editorManager, 'taxon.editview', ['tid' => $tid]);
    }

    public static function updateUpperTaxonomy() {
        $requestData = request()->all();
        $tid = (int) $requestData['tid'] ?? null;
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $statusStr = $editorManager->submitTaxStatusEdits($requestData['newparenttid'] ?? '', $requestData['tidaccepted'] ?? '');

        return TaxonResponseHandler::handleStatusReportingAndRouting(__('taxonomy_taxonomyloader.UPPER_TAXONOMY_UPDATE_SUCCESS') . ' ' . $statusStr, $editorManager, 'taxon.editview', ['tid' => $tid]);
    }
}
