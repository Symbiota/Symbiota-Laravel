<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaxonomyController extends Controller {
    public static function taxonData(int $tid) {
        $taxon = DB::table('taxa as t')
            ->leftJoin('taxstatus as ts', 'ts.tid', 't.tid')
            ->where('t.tid', $tid)
            ->where('taxauthid', 1)
            ->select('*')
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
        $taxon = self::taxonData($tid);

        $parents = self::getParents($tid);

        $common_names = self::getCommonNames($tid);
        $children = self::getDirectChildren($tid, 1);

        $occurrence_count = self::getTaxonOccurrenceStats($tid);
        $taxa_descriptions = self::getTaxaDescriptions($tid);
        $external_links = self::getExternalLinks($tid);

        return view('pages/taxon/profile', [
            'taxon' => $taxon,
            'parents' => $parents,
            'common_names' => $common_names,
            'occurrence_count' => $occurrence_count,
            'children' => $children,
            'taxa_descriptions' => $taxa_descriptions,
            'external_links' => $external_links,
        ]);
    }

    public static function editTaxonProfile(int $tid) {
        $taxon = self::taxonData($tid);

        $parents = self::getParents($tid);

        $common_names = self::getCommonNames($tid);
        $children = self::getDirectChildren($tid, 1);

        $occurrence_count = self::getTaxonOccurrenceStats($tid);
        $taxa_descriptions = self::getTaxaDescriptions($tid);
        $external_links = self::getExternalLinks($tid);

        $taxa_media = DB::table('media')
            ->where('tid', $tid)
            ->select('*')
            ->orderBy('sortSequence')
            ->get();

        return view('pages/taxon/edit', [
            'taxon' => $taxon,
            'parents' => $parents,
            'common_names' => $common_names,
            'occurrence_count' => $occurrence_count,
            'children' => $children,
            'taxa_descriptions' => $taxa_descriptions,
            'external_links' => $external_links,
            'media' => $taxa_media,
        ]);
    }

    public static function editTaxon($tid) {
        $taxon = self::taxonData($tid);
        $taxonInfo = $taxon;
        $securitystatusstart = $taxon->securitystatus ?? 0;
        if (! $taxon) {
            // @TODO return a 404 not found page
        }
        $kingdoms = DB::table('taxa')->where('rankID', 10)->select('tid', 'sciName')->get();
        $primaryKingdom = config('portal.primary_taxonomic_kingdom');
        $allTaxonRanks = DB::table('taxonunits')->distinct()->select('rankid', 'rankname')->where('kingdomName', $primaryKingdom)->orderBy('rankid')->orderBy('rankname', 'desc')->get();
        $indContent = [['title' => '', 'value' => '', 'disabled' => false], ['title' => '×', 'value' => '×', 'disabled' => false]];
        $securityOptions = [['title' => 'No Security', 'value' => 0, 'disabled' => false], ['title' => 'Hide Locality Details', 'value' => 1, 'disabled' => false]];
        ! empty($GLOBALS['ACTIVATE_PALEO_DAGGER']) ? $indContent[] = ['title' => '†', 'value' => '†', 'disabled' => false] : null; // @TODO confirm that GLOBALS can be accessed this way

        $parentName = '';
        if ($taxon && $taxon->parenttid) {
            $parentName = DB::table('taxa')->where('tid', $taxon->parenttid)->value('sciName') ?? '';
        }

        $acceptedName = '';
        if ($taxon && $taxon->tidaccepted && $taxon->tidaccepted != $taxon->tid) {
            $acceptedName = DB::table('taxa')->where('tid', $taxon->tidaccepted)->value('sciName') ?? '';
        }

        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $taxonEditorObj = new \TaxonomyEditorManager();
        $taxonEditorObj->setTid($tid);
        $verifyArr = $taxonEditorObj->verifyDeleteTaxon();
        $taxonEditorObj->setTaxon();
        $taxonInfo->synonyms = $taxonEditorObj->getSynonyms();
        $taxonInfo->isAccepted = $taxonEditorObj->getIsAccepted();
        $taxonInfo->acceptedArr = [];
        if ($taxonEditorObj->getIsAccepted() <> 1) {
            $taxonInfo->acceptedArr = $taxonEditorObj->getAcceptedArr();
        }

        if (! empty($verifyArr['child'])) {
            $verifyArr['child'] = array_map(
                fn ($name, $url) => ['name' => $name, 'url' => $url],
                $verifyArr['child'],
                array_map(fn ($key) => url('/taxon/' . $key), array_keys($verifyArr['child']))
            );
        }

        return view('pages/taxon/editTaxon', [
            'mode' => 'edit',
            'targetTid' => request()->route('tid'),
            'kingdoms' => $kingdoms,
            'allTaxonRanks' => $allTaxonRanks,
            'indContent' => $indContent,
            'securityOptions' => $securityOptions,
            'canCreateOrEdit' => Gate::check('TAXON_EDITOR'),
            'taxonInfo' => $taxonInfo,
            'parentName' => $parentName,
            'acceptedName' => $acceptedName,
            'securitystatusstart' => $securitystatusstart,
            'verifyArr' => $verifyArr,
        ]);
    }

    public static function createTaxon() {
        $kingdoms = DB::table('taxa')->where('rankID', 10)->select('tid', 'sciName')->get();
        $primaryKingdom = config('portal.primary_taxonomic_kingdom');
        $allTaxonRanks = DB::table('taxonunits')->distinct()->select('rankid', 'rankname')->where('kingdomName', $primaryKingdom)->orderBy('rankid')->orderBy('rankname', 'desc')->get();
        $indContent = [['title' => '', 'value' => '', 'disabled' => false], ['title' => '×', 'value' => '×', 'disabled' => false]];
        $securityOptions = [['title' => 'No Security', 'value' => 0, 'disabled' => false], ['title' => 'Hide Locality Details', 'value' => 1, 'disabled' => false]];
        ! empty($GLOBALS['ACTIVATE_PALEO_DAGGER']) ? $indContent[] = ['title' => '†', 'value' => '†', 'disabled' => false] : null; // @TODO confirm that GLOBALS can be accessed this way

        return view('pages/taxon/create', [
            'mode' => 'create',
            'kingdoms' => $kingdoms,
            'allTaxonRanks' => $allTaxonRanks,
            'indContent' => $indContent,
            'securityOptions' => $securityOptions,
            'canCreateOrEdit' => Gate::check('TAXON_EDITOR'),
            'securitystatusstart' => 0,
        ]);
    }

    public static function store() {
        $postData = request()->all();
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $editorManager = new \TaxonomyEditorManager();

        // if (! $editorManager->validateNewName($postData)) {
        //     // Redirect back with error message
        //     return redirect()->back()->withInput()->withErrors(['error' => 'Validation failed for the new taxon. Please check your input and try again.']);
        // } else { // @TODO to be fixed in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
        $tidResult = $editorManager->loadNewName($postData);
        // }

        if ($tidResult > 0) {
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

        $rankMap = [
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

        return view('pages/taxon/show', [
            'parents' => $parents,
            'rankMap' => $rankMap,
            'targetTid' => $targetTid,
            'taxonName' => $taxonName,
        ]);
    }

    public static function update() {
        $postData = request()->all();
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $editorManager = new \TaxonomyEditorManager();
        $editType = $postData['edit-type'] ?? null;
        $editorManager->setTaxon();
        $editorManager->setTid($postData['tidaccepted'] ?? null);
        $editorManager->setTaxAuthId($postData['acceptedstatus'] ?? null);
        // $editorManager->setTaxAuthId($taxAuthId);
        // $postData['securitystatusstart'] = $editorManager->getSecurityStatus();

        if ($editType === 'taxonedits') {
            $statusStr = $editorManager->submitTaxonEdits($postData);
        } elseif ($editType === 'updatetaxstatus') {
            $statusStr = $editorManager->submitTaxStatusEdits($postData['parenttid'], $postData['tidaccepted']);
        } elseif ($editType === 'synonymedits') {
            $statusStr = $editorManager->submitSynonymEdits($postData['tidsyn'], $tid, $postData['unacceptabilityreason'], $postData['notes'], $postData['sortsequence']);
        } elseif ($editType === 'linkToAccepted') {
            $deleteOther = array_key_exists('deleteother', $postData) ? true : false;
            $statusStr = $editorManager->submitAddAcceptedLink($postData['tidaccepted'], $deleteOther);
        } elseif ($editType === 'deltidaccepted') {
            $statusStr = $editorManager->removeAcceptedLink($postData['deltidaccepted']);
        } elseif ($editType === 'changetoaccepted') {
            $tidAccepted = $postData['tidaccepted'];
            $switchAcceptance = array_key_exists('switchacceptance', $postData) ? true : false;
            $statusStr = $editorManager->submitChangeToAccepted($tid, $tidAccepted, $switchAcceptance);
        } elseif ($editType === 'changeToNotAccepted') {
            $tidAccepted = $postData['tidaccepted'];
            $statusStr = $editorManager->submitChangeToNotAccepted($tid, $tidAccepted, $postData['unacceptabilityreason'], $postData['notes']);
        } elseif ($editType == 'updatehierarchy') {
            $statusStr = $editorManager->rebuildHierarchy($tid);
        } elseif ($editType == 'remapTaxon') {
            $remapStatus = $editorManager->transferResources($postData['remaptid']);
            if ($editorManager->getWarningArr()) {
                $statusStr = $LANG['FOLLOWING_WARNINGS'] . ': ' . implode(';', $editorManager->getWarningArr());
            }
            if ($remapStatus) {
                $statusStr = $LANG['SUCCESS_REMAPPING'] . ' ' . $statusStr;
                header('Location: taxonomydisplay.php?target=' . $postData['genusstr'] . '&statusstr=' . $statusStr);
            } else {
                $statusStr = $editorManager->getErrorMessage();
            }
        } elseif ($editType == 'deleteTaxon') {
            $delStatus = $editorManager->deleteTaxon();
            if ($editorManager->getWarningArr()) {
                $statusStr = $LANG['FOLLOWING_WARNINGS'] . ': ' . implode(';', $editorManager->getWarningArr());
            }
            if ($delStatus) {
                $statusStr = $LANG['SUCCESS_DELETING'] . ' ' . $statusStr;
                header('Location: taxonomydisplay.php?statusstr=' . $statusStr);
            } else {
                $statusStr = $editorManager->getErrorMessage();
            }
        }

        if ($statusStr === true || $statusStr === '') { // successful runs of the above return empty strings
            // Redirect to the newly created taxon's page
            $tid = $postData['tidaccepted'] ?? null;

            return redirect()->route('taxon.view', ['tid' => $tid])->with('success', 'Taxon updated successfully!');
        } else {
            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]); // @TODO fix this in issue https://github.com/Symbiota/Symbiota-Laravel/issues/119
        }
    }

    public static function delete() {
        $tid = (int) request()->all()['tid'] ?? null;
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $editorManager = new \TaxonomyEditorManager();
        $editorManager->setTid($tid);
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

        $tid = (int) request()->all()['tid'] ?? null;
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $editorManager = new \TaxonomyEditorManager();
        $editorManager->setTid($tid);

        $remapStatus = $editorManager->transferResources((int) $requestData['remaptid']);
        $statusStr = $requestData['taxa'] ?? '';
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        if ($remapStatus) {
            $statusStr = __('taxonomy_taxoneditor.SUCCESS_REMAPPING') . ' ' . $statusStr;
            TaxonomyController::delete();

            return redirect()->route('taxon.view', ['tid' => $requestData['remaptid']])->with('success', $statusStr);
        } else {
            $statusStr = $editorManager->getErrorMessage();

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]); // @TODO fix this in issue
        }
    }

    public static function changeAccepted() {
        $requestData = request()->all();
        $oldTid = (int) request()->all()['tid'] ?? null;
        $targetTid = (int) request()->all()['tidaccepted'] ?? null;
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $editorManager = new \TaxonomyEditorManager();
        $editorManager->setTid($oldTid);
		$statusStr = $editorManager->submitChangeToAccepted($targetTid, $oldTid); // not the order I would have written this method signature, but not worth the refactor in the old code base yet
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());
            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        if ($statusStr = $editorManager->getErrorMessage()) {
            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        } 
        $statusStr = __('taxonomy_taxoneditor.SYNONYM_SUCCESS') . ' ' . $statusStr;
        return redirect()->route('taxon.editview', ['tid' => $oldTid])->with('success', $statusStr);
    }

    public static function changeToNotAccepted() {
        $requestData = request()->all();
        $oldTid = (int) request()->all()['tid'] ?? null;
        $targetTid = (int) request()->all()['new-tid'] ?? null;
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $editorManager = new \TaxonomyEditorManager();
        $editorManager->setTid($oldTid);
		$switchAcceptance = array_key_exists("switchacceptance", $_REQUEST) ? true : false;
		$statusStr = $editorManager->submitChangeToAccepted($oldTid, $targetTid, $switchAcceptance);
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());
            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        if($statusStr = $editorManager->getErrorMessage()){
            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
        $statusStr = __('taxonomy_taxoneditor.ACCEPTANCE_STATUS_CHANGE_SUCCESS') . ' ' . $statusStr;
        return redirect()->route('taxon.editview', ['tid' => $oldTid])->with('success', $statusStr);
    }
}
