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
            ->where('c.type', 'excludespp')
            ->whereLike('c.access', 'public%')
            ->where('c.clid', $clid)
            ->orderByRaw('p.projname is null, p.projname, c.sortSequence, c.name')
            ->select('*');

        return $checklists_query->first();
    }

    public static function checklist(int $clid) {
        $checklist = self::getChecklistsData($clid);

        return view('pages/checklist/profile', ['checklist' => $checklist]);
    }

    public static function getChecklistsData(Request $request) {
        $checklists_query = DB::table('fmchecklists as c')
            ->leftJoin('fmchklstprojlink as cpl', 'cpl.clid', 'c.clid')
            ->leftJoin('fmprojects as p', 'p.pid', 'cpl.pid')
            ->where('c.type', 'excludespp')
            ->whereLike('c.access', 'public%')
            ->orWhereIn('c.clid', function (Builder $query) {
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
        //$checklists = $query->get();

        // If user is a 'ClAdmin' and then add those checklists via clids

        // If user is 'ProjAdmin' and then add checklists with the proper pid

        //return $checklists;
    }
}
