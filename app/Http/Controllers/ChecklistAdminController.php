<?php

namespace App\Http\Controllers;

use App\Models\Checklist;

class ChecklistAdminController extends Controller {
    public static function getAdminPage(int $clid) {
        $checklist = Checklist::clid($clid)->first();

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }

    public static function addEditor(int $clid, int $uid) {
        $checklist = Checklist::clid($clid)->first();

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }

    public static function updateMetadata(int $clid, int $uid) {
        $checklist = Checklist::clid($clid)->first();

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }

    public static function addVoucherImage(int $clid, int $uid) {
        $checklist = Checklist::clid($clid)->first();

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }

    public static function addProject(int $clid, int $pid) {
        $checklist = Checklist::clid($clid)->first();

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }

    public static function delete(int $clid) {
        $checklist = Checklist::clid($clid)->first();

        return view('pages/checklist/admin', ['checklist' => $checklist]);
    }
}
