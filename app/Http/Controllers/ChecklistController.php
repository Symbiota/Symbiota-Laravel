<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller {
    public static function getChecklistData(int $clid) {
        $checklists_query = DB::table('fmchecklists as c')
            ->leftJoin('fmchklstprojlink as cpl', 'cpl.clid', 'c.clid')
            ->leftJoin('fmprojects as p', 'p.pid', 'cpl.pid')
            ->where('c.type', '!=', 'excludespp')
            ->whereLike('c.access', 'public%')
            ->where('p.ispublic', 1)
            ->where('c.clid', $clid)
            ->orderByRaw('p.projname is null, p.projname, c.sortSequence, c.name')
            ->select('*');

        return $checklists_query->first();
    }

    public static function checklist(int $clid) {
        $checklist = self::getChecklistData($clid);

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

        $taxons = $taxon_query
            ->joinSub($sub_query, 'checklist_taxa', 'checklist_taxa.tid', 't.tid')
            ->orderBy('family')
            ->orderBy('sciname')
            ->select('t.tid', 'family', 'sciname', 'unitName1', 'unitName2', 'rankId')
            ->get();

        return view('pages/checklist/profile', ['checklist' => $checklist, 'taxons' => $taxons]);
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
}
