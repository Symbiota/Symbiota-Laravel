<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller {
    public static function getProjectData(int $pid) {
        $project = DB::table('fmprojects')
            ->select('pid', 'projname', 'managers')
            ->where('pid', '=', $pid)
            ->first();

        $checklists = DB::table('fmchecklists as c')
            ->select('link.pid', 'c.clid', 'c.name', 'mapChecklist')
            ->leftJoin('fmchklstprojlink as link', 'link.clid', '=', 'c.clid')
            ->where('link.pid', '=', $pid)
            ->orderByRaw('-link.pid DESC')
            ->get();

        return ['project' => $project, 'checklists' => $checklists];
    }

    public static function project(int $pid) {
        view('pages/project',self::getProjectData($pid));
    }
}
