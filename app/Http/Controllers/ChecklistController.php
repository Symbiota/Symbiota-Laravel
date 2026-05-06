<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class ChecklistController extends Controller {
    private static function parseChecklistRequest($checklist): array {
        //Set Display Settings
        $defaultSettings = json_decode($checklist->defaultSettings ?? '{}');

        return [
            'show_synonyms' => request('show_common') ?? $defaultSettings->dsynonyms ?? false,
            'show_common' => request('show_common') ?? $defaultSettings->dcommon ?? false,
            'show_notes_vouchers' => request('show_notes_vouchers') ?? $defaultSettings->dvouchers ?? false,
            'show_taxa_authors' => request('show_taxa_authors') ?? $defaultSettings->dauthors ?? false,
            'show_images' => request('show_images') ?? $defaultSettings->dimages ?? false,
            'show_taxa_alphabetically' => request('show_taxa_alphabetically') ?? $defaultSettings->dalpha ?? false,
            'limit_voucher_images' => request('limit_voucher_images') ?? $defaultSettings->dvoucherimages ?? false,
            'show_subgenera' => request('show_subgenera') ?? $defaultSettings->dsubgenera ?? false,
            'activate_key' => $defaultSettings->activatekey ?? $GLOBALS['KEY_MOD_IS_ACTIVE'] ?? false,
        ];
    }

    private static function getClManager($checklist) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/ChecklistManager.php');

        $clManager = new \ChecklistManager();
        $clManager->setClid($checklist->clid);

        return $clManager;
    }

    private static function getProfilePageData($checklist) {
        $clManager = self::getClManager($checklist);
        $page_data = self::parseChecklistRequest($checklist);

        $clManager->setShowCommon($page_data['show_common']);
        $clManager->setShowSynonyms($page_data['show_synonyms']);
        $clManager->setShowVouchers($page_data['show_notes_vouchers']);
        $clManager->setShowAuthors($page_data['show_taxa_authors']);
        $clManager->setShowImages($page_data['show_images']);
        $clManager->setShowAlphaTaxa($page_data['show_taxa_alphabetically']);
        $clManager->setLimitImagesToVouchers($page_data['limit_voucher_images']);
        $clManager->setShowSubgenera($page_data['show_subgenera']);

        $page_data['checklist'] = $checklist;
        $page_data['clManager'] = $clManager;

        $page_data['taxaList'] = $clManager->getTaxaList(1, 0);
        // Old call Rework after logic changes start
        // $taxons = self::getChecklistTaxa($checklist);
        $page_data['voucherArr'] = $clManager->getVoucherArr();
        // Old call Rework after logic changes start
        // $vouchers = self::getVouchers($checklist, $taxons);

        $page_data['parent'] = $clManager->getParentChecklist();
        $page_data['children'] = $clManager->getChildClidArr();
        $page_data['exclusions'] = $clManager->getExclusionChecklist();

        return $page_data;
    }

    public static function getChecklistData(int $clid) {

        $user = request()->user();

        $checklists_query = DB::table('fmchecklists as c')
            ->leftJoin('fmchklstprojlink as cpl', 'cpl.clid', 'c.clid')
            ->leftJoin('fmprojects as p', 'p.pid', 'cpl.pid')
            ->where('c.clid', $clid)
            ->orderByRaw('p.projname is null, p.projname, c.sortSequence, c.name')
            ->select('c.*', 'c.notes', 'p.pid', 'p.projname', 'p.displayname', 'p.managers', 'p.briefdescription', 'p.fulldescription');

        if (! $user || ! $user->canViewChecklist($clid)) {
            $checklists_query
                ->where('c.type', '!=', 'excludespp')
                ->whereLike('c.access', 'public%')
                ->where('p.ispublic', 1);
        }

        return $checklists_query->first();
    }

    public static function getChecklistTaxa($checklist) {
        $taxon_query = DB::table('taxa as t')
            ->join('taxstatus as ts', 'ts.tid', 't.tid');

        // Selects taxa associated to a given checklist
        $sub_query = DB::table('fmchklsttaxalink')->where('clid', $checklist->clid)->select('tid');

        // Optionally grabs
        if ($checklist->type != 'rarespp') {
            $parent_query = DB::table('fmchklsttaxalink as ctl')
                ->join('taxstatus as ts', 'ts.tid', 'ctl.tid')
                ->join('taxa as t', 't.tid', 'ts.tid')
                ->where('clid', $checklist->clid)
                ->whereNotNull('ts.parenttid')
                ->where('t.rankId', '>', 220)->select('ts.parenttid as tid');

            $sub_query->union($parent_query);
        }

        $common_sub = DB::table('taxavernaculars')->groupBy('tid')
            ->selectRaw('tid, GROUP_CONCAT(VernacularName) as vernacularNames');

        $synonyms_sub = DB::table('taxstatus as ts')
            ->join('taxa as t', 't.tid', 'ts.tid')
            ->whereRaw('ts.tid != tidaccepted')
            ->groupBy('tidaccepted')
            ->selectRaw('tidaccepted, GROUP_CONCAT(t.sciname) as synonyms');

        $taxons = $taxon_query
            ->joinSub($sub_query, 'checklist_taxa', 'checklist_taxa.tid', 't.tid')
            ->when(request()->query('taxa'), function (Builder $query, $taxa) {
                $taxa_search = DB::table('taxa as t')
                    ->join('taxaenumtree as e', 't.tid', 'e.parenttid')
                    ->whereLike('t.sciname', $taxa . '%')
                    ->select('e.tid');
                $query->joinSub($taxa_search, 'taxa_search', 'taxa_search.tid', 't.tid');
            })
            ->leftJoinSub($common_sub, 'commons', 'commons.tid', 't.tid')
            ->leftJoinSub($synonyms_sub, 'other_taxa', 'other_taxa.tidaccepted', 't.tid')
            ->orderBy('family')
            ->orderBy('sciname')
            ->select('t.tid', 'family', 'sciname', 'author', 'vernacularNames', 'synonyms', 'unitName1', 'unitName2', 'rankId')
            ->distinct()
            ->get();

        return $taxons;
    }

    public static function getVouchers($checklist, $taxons) {
        return DB::table('fmvouchers as fm')
            ->select('fmt.tid', 'fm.occid', 'fm.tid', 'fm.notes', 'fm.editornotes', 'o.recordedBy', 'o.recordNumber', 'c.institutionCode')
            ->join('fmchklsttaxalink as fmt', 'fmt.clTaxaID', 'fm.clTaxaID')
            ->join('omoccurrences as o', 'o.occid', 'fm.occid')
            ->join('omcollections as c', 'o.collid', 'c.collid')
            ->where('fm.clid', $checklist->clid)
            ->orderBy('c.collid')
            ->whereIn('fmt.tid', $taxons->map(fn ($t) => $t->tid))
            ->distinct()
            ->get();
    }

    public static function checklist(int $clid) {
        $checklist = self::getChecklistData($clid);

        if (empty($checklist)) {
            return view('pages/checklist/not-found');
        }

        $page_data = self::getProfilePageData($checklist);

        if (request()->query('partial') === 'taxa-list') {
            return view('pages/checklist/profile', $page_data)->fragment('taxa-list');
        }

        return view('pages/checklist/profile', $page_data);
    }

    public static function browserPrint(int $clid) {
        $checklist = self::getChecklistData($clid);

        if (empty($checklist)) {
            return view('pages/checklist/not-found');
        }

        $page_data = self::getProfilePageData($checklist);

        return view('pages/checklist/printProfile', $page_data);
    }

    public static function getChecklistsData(Request $request) {
        $checklists_query = DB::table('fmchecklists as c')
            ->leftJoin('fmchklstprojlink as cpl', 'cpl.clid', 'c.clid')
            ->leftJoin('fmprojects as p', 'p.pid', 'cpl.pid')
            ->where('c.type', '!=', 'excludespp')
            ->whereLike('c.access', 'public%')
            ->where('p.ispublic', 1)
            ->whereIn('c.clid', function (Builder $query) {
                $checklistChildren = DB::table('fmchklstchildren')
                    ->select('clid')
                    ->distinct();
                $query
                    ->select('clid')
                    ->from('fmchklsttaxalink')
                    ->union($checklistChildren);
            })
            ->orderByRaw('p.projname is null, p.projname, c.sortSequence, c.name')
            ->select('*');

        // If user is a 'ClAdmin' and then add those checklists via clids

        // If user is 'ProjAdmin' and then add checklists with the proper pid
        return $checklists_query->get();
    }

    public static function checklists(Request $request) {
        $checklists = self::getChecklistsData($request);

        return view('pages/checklists', ['checklists' => $checklists]);
    }

    public static function dynamicMapPage(Request $request) {
        return view('pages/checklist/dynamic-builder');
    }

    public static function buildDynChecklist(Request $request) {
        global $LANG, $SERVER_ROOT, $DYN_CHECKLIST_RADIUS;
        include_once legacy_path('/classes/utilities/Language.php');
        include_once legacy_path('/classes/DynamicChecklistManager.php');

        \Language::load([
            'checklists/dynamicmap',
            'checklists/checklist',
        ]);

        $error = null;

        $lat = filter_var(request('lat'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $lng = filter_var(request('lng'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $radius = filter_var(request('radius'), FILTER_SANITIZE_NUMBER_INT);
        $radiusUnits = request('radiusunits') === 'mi' ? 'mi' : 'km';
        $dynamicRadius = $DYN_CHECKLIST_RADIUS ?? 10;
        $taxa = request('taxa');
        $tid = filter_var(request('tid'), FILTER_SANITIZE_NUMBER_INT);
        $interface = request('interface') === 'checklist' ? 'checklist' : 'key';

        $dynClManager = new \DynamicChecklistManager();

        if ($taxa && ! $tid) {
            $tid = $dynClManager->getTid($taxa);
        }

        $dynClid = 0;

        if (is_numeric($lng) && is_numeric($lng)) {
            if ($radius) {
                $dynClid = $dynClManager->createChecklist($lat, $lng, $radius, $radiusUnits, $tid);
            } else {
                $dynClid = $dynClManager->createDynamicChecklist($lat, $lng, $dynamicRadius, $tid);
            }
        }

        $dynClManager->removeOldChecklists();

        if (! $dynClid) {
            $error = new MessageBag([$LANG['ERROR_GEN_CHECK']]);
        } elseif ($interface == 'key') {
            return redirect(legacy_url('ident/key.php?dynclid=' . $dynClid . '&taxon=All Species'));
        } else {
            return redirect(legacy_url('checklists/checklist.php?dynclid=' . $dynClid));
        }

        return view('pages/checklist/dynamic-builder', ['errors' => $error]);
    }

    public static function mapPage(Request $request) {
        return view('pages/checklist/map');
    }

    public static function createChecklist(Request $request) {
        $user = $request->user();

        DB::transaction(function () use ($user, $request) {
            $clid = DB::table('fmchecklists')->insertGetId([
                'name' => $request->get('checklist_name'),
                'uid' => $user->uid,
            ]);

            $userRole = new UserRole();

            $userRole->role = UserRole::CL_ADMIN;
            $userRole->tableName = 'fmchecklists';
            $userRole->uid = $user->uid;
            $userRole->tablePK = $clid;
            $userRole->save();
        });

        return view('pages/user/profile', [
            'user' => $user,
        ])->fragment('checklists');
    }

    private static function getUserChecklists(Request $request) {
        $user = $request->user();

        $checklists = DB::table('fmchecklists')
            ->join('userroles as ur', 'tablePK', 'clid')
            ->whereIn('role', [UserRole::CL_ADMIN])
            ->where('ur.uid', $user->uid)
            ->get();

        return $checklists;
    }

    public static function getAdminPage(int $clid) {
        $checklist = self::getChecklistData($clid);

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }
}
