<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use App\Services\TaxonomyMutationService;
use App\Services\TaxonomyPayloadNormalizer;
use App\Services\TaxonomyQueryService;
use App\Services\TaxonResponseHandler;
use App\Services\TaxonViewDataService;
use Illuminate\Http\Request;

class TaxonomyController extends Controller {
    public static function taxon(int $tid) {
        if (! TaxonomyQueryService::taxonData($tid)) {
            return redirect()->route('taxon.index')->withErrors(['error' => __('taxonomy_taxonomyloader.TAXON_NOT_FOUND')]);
        }

        return view('pages/taxon/profile', TaxonViewDataService::buildTaxonViewData($tid));
    }

    public static function editTaxonProfile(int $tid) {
        if (! TaxonomyQueryService::taxonData($tid)) {
            return redirect()->route('taxon.index')->withErrors(['error' => __('taxonomy_taxonomyloader.TAXON_NOT_FOUND')]);
        }

        return view('pages/taxon/edit', TaxonViewDataService::buildTaxonViewData($tid, true));
    }

    public static function editTaxon($tid) {
        $taxonEditorObj = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $taxonInfo = TaxonViewDataService::prepareTaxonInfo($tid, $taxonEditorObj);

        if (! $taxonInfo) {
            return redirect()->route('taxon.index')->withErrors(['error' => __('taxonomy_taxonomyloader.TAXON_NOT_FOUND')]);
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
        $postData = TaxonomyPayloadNormalizer::normalizeCreatePayload(request()->all());

        if ($postData['acceptstatus'] === 0 && ! $postData['tidaccepted']) {
            return redirect()->back()->withInput()->withErrors(['error' => __('taxonomy_taxonomyloader.ACC_NAME_NEEDS_VALUE')]);
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
            return redirect()->back()->withInput()->withErrors(['error' => (string) $tidResult]); // @TODO fix this in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
        }
    }

    public static function showTree(Request $request, $tid = null) {
        $data = [
            'rankMap' => Taxonomy::RANK_MAP,
        ];
        if ($tid != null) {
            $taxon = Taxonomy::query()->findOrFail($tid);
            $data['taxonName'] = $taxon->sciName;
            $data['targetTid'] = $tid;
        }
        $parents = [];
        $parentTid = intval(request('parenttid'));
        if ($parentTid) {
            $data['parentTid'] = $parentTid;
            // Add parent information to data and attach children
        }
        $displayAuthor = intval(request('displayauthor'));
        if ($displayAuthor) {
            $data['displayAuthor'] = $displayAuthor;
        }
        if ($parentTid) {
            $parents = TaxonomyQueryService::getParents($parentTid) ?? [];
            foreach ($parents as $parent) {
                $parent->children = TaxonomyQueryService::getDirectChildren($parent->tid, $displayAuthor);
            }
            $data['parents'] = $parents;

        }

        return view('pages/taxon/show', [
            'parents' => $data['parents'] ?? [],
            'rankMap' => $data['rankMap'] ?? [],
            'targetTid' => $data['targetTid'] ?? null,
            'taxonName' => $data['taxonName'] ?? '',
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

    public static function delete(int $tid) {
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

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]); // @TODO fix this in issue
        }
    }

    public static function remap(int $tid) {
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $remapTid = request('remaptid');
        $remapStatus = $editorManager->transferResources((int) $remapTid);
        $statusStr = request('taxa') ?? '';
        if ($response = TaxonResponseHandler::redirectBackWithManagerIssues($editorManager)) { // @TODO is there a way to use handleStatusReportingAndRouting here?
            return $response;
        }
        if ($remapStatus) {
            $statusStr = __('taxonomy_taxoneditor.SUCCESS_REMAPPING') . ' ' . $statusStr;
            TaxonomyController::delete($tid);

            return redirect()->route('taxon.view', ['tid' => $remapTid])->with('success', $statusStr);
        } else {
            $statusStr = $editorManager->getErrorMessage();

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]); // @TODO fix this in issue
        }
    }

    public static function changeAccepted() {
        $oldTid = (int) request('tid') ?? null;
        $targetTid = (int) request('tidaccepted') ?? null;
        if (! $oldTid || ! $targetTid) {
            $statusStr = __('taxonomy_taxoneditor.INVALID_TAXON_IDS');

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($oldTid);
        $statusStr = $editorManager->submitChangeToAccepted($targetTid, $oldTid); // not the order I would have written this method signature, but not worth the refactor in the old code base yet
        $statusStr = __('taxonomy_taxoneditor.SYNONYM_SUCCESS') . ' ' . $statusStr;

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.view', ['tid' => $targetTid]);
    }

    public static function changeToNotAccepted() {
        $oldTid = (int) request('tid') ?? null;
        $targetTid = (int) request('new-tid') ?? null;
        if (! $oldTid || ! $targetTid) {
            $statusStr = __('taxonomy_taxoneditor.INVALID_TAXON_IDS');

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($oldTid);
        $switchAcceptance = request('switchacceptance') === '1';
        $statusStr = $editorManager->submitChangeToAccepted($oldTid, $targetTid, $switchAcceptance);
        $statusStr = __('taxonomy_taxoneditor.ACCEPTANCE_STATUS_CHANGE_SUCCESS') . ' ' . $statusStr;

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.editview', ['tid' => $oldTid]);
    }

    public static function updateSynonymLink() {
        $currentTid = (int) request('current-tid') ?? null;
        if (! $currentTid) {
            $statusStr = __('taxonomy_taxoneditor.INVALID_TAXON_IDS');

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($currentTid);
        $statusStr = $editorManager->submitSynonymEdits(request('tidsyn'), $currentTid, request('unacceptabilityreason'), request('notes'), request('sortsequence'));

        return TaxonResponseHandler::handleStatusReportingAndRouting($statusStr, $editorManager, 'taxon.editview', ['tid' => $currentTid]);
    }

    public static function reconstructHierarchy() {
        $tid = (int) request('tid') ?? null;
        if (! $tid) {
            $statusStr = __('taxonomy_taxoneditor.INVALID_TAXON_IDS');

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $editorManager->rebuildHierarchy($tid);

        return TaxonResponseHandler::handleStatusReportingAndRouting(__('taxonomy_taxoneditor.HIERARCHY_REBUILD_SUCCESS'), $editorManager, 'taxon.editview', ['tid' => $tid]);
    }

    public static function updateUpperTaxonomy() {
        $tid = (int) request('tid') ?? null;
        if (! $tid) {
            $statusStr = __('taxonomy_taxoneditor.INVALID_TAXON_IDS');

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        $editorManager = TaxonomyMutationService::getTaxonomyEditorManager($tid);
        $statusStr = $editorManager->submitTaxStatusEdits(request('newparenttid') ?? '', request('tidaccepted') ?? '');

        return TaxonResponseHandler::handleStatusReportingAndRouting(__('taxonomy_taxonomyloader.UPPER_TAXONOMY_UPDATE_SUCCESS') . ' ' . $statusStr, $editorManager, 'taxon.editview', ['tid' => $tid]);
    }
}
