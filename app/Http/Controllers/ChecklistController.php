<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\table;

class ChecklistController extends Controller {
    public static function getChecklistData(int $clid) {

        $user = request()->user();

        $checklists_query = DB::table('fmchecklists as c')
            ->leftJoin('fmchklstprojlink as cpl', 'cpl.clid', 'c.clid')
            ->leftJoin('fmprojects as p', 'p.pid', 'cpl.pid')
            ->where('c.clid', $clid)
            ->orderByRaw('p.projname is null, p.projname, c.sortSequence, c.name')
            ->select('c.*', 'p.*');

        if(!$user || !$user->canViewChecklist($clid)) {
            $checklists_query
                ->where('c.type', '!=', 'excludespp')
                ->whereLike('c.access', 'public%')
                ->where('p.ispublic', 1);
        }

        return $checklists_query->first();
    }

    public static function checklist(int $clid) {
        $checklist = self::getChecklistData($clid);

        if(empty($checklist)) {
            return view('pages/checklist/not-found');
        }

        $taxon_query = DB::table('taxa as t')
            ->join('taxstatus as ts', 'ts.tid', 't.tid');

        // Selects taxa associated to a given checklist
        $sub_query = DB::table('fmchklsttaxalink')->where('clid', $clid)->select('tid');

        // Optionally grabs
        if ($checklist->type != 'rarespp') {
            $parent_query = DB::table('fmchklsttaxalink as ctl')
                ->join('taxstatus as ts', 'ts.tid', 'ctl.tid')
                ->join('taxa as t', 't.tid', 'ts.tid')
                ->where('clid', $clid)
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

        $vouchers = DB::table('fmvouchers as fm')
            ->select('fmt.tid', 'fm.occid', 'fm.tid', 'fm.notes', 'fm.editornotes', 'o.recordedBy', 'o.recordNumber', 'c.institutionCode')
            ->join('fmchklsttaxalink as fmt', 'fmt.clTaxaID', 'fm.clTaxaID')
            ->join('omoccurrences as o', 'o.occid', 'fm.occid')
            ->join('omcollections as c', 'o.collid', 'c.collid')
            ->where('fm.clid', $clid)
            ->orderBy('c.collid')
            ->whereIn('fmt.tid', $taxons->map(fn ($t) => $t->tid))
            ->distinct()
            ->get();

        $page_data = [
            'checklist' => $checklist,
            'vouchers' => $vouchers,
            'taxons' => $taxons,
        ];

        if (request()->query('partial') === 'taxa-list') {
            return view('pages/checklist/profile', $page_data)->fragment('taxa-list');
        }

        return view('pages/checklist/profile', $page_data);
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

    public static function createChecklist(Request $request) {
        $user = $request->user();

        DB::transaction(function () use ($user, $request) {
            $clid = DB::table('fmchecklists')->insertGetId([
                'name' => $request->get('checklist_name'),
                'uid' => $user->uid
            ]);

            $userRole = new UserRole();

            $userRole->role = UserRole::CL_ADMIN;
            $userRole->tableName = 'fmchecklists';
            $userRole->uid = $user->uid;
            $userRole->tablePK = $clid;
            $userRole->save();
        });

        return view('pages/user/profile', [
            'user' => $user
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
}
