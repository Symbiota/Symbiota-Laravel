<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class ProjectController extends Controller {
    public static function getProjectData(int $pid) {
        $project = DB::table('fmprojects')
            ->select('pid', 'projname', 'managers', 'fullDescription', 'notes', 'isPublic')
            ->where('pid', '=', $pid)
            ->first();

        $checklists = DB::table('fmchecklists as c')
            ->select('link.pid', 'c.defaultSettings', 'c.clid', 'c.name', 'mapChecklist')
            ->leftJoin('fmchklstprojlink as link', 'link.clid', '=', 'c.clid')
            ->where('link.pid', '=', $pid)
            ->orderByRaw('-link.pid DESC')
            ->get();

        return ['project' => $project, 'checklists' => $checklists];
    }

    /* Wrapper to reuse setup of legacy project manager. This should get remove when the models system is integrated into projects */
    private static function getProjectManager(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

        return $projManager;
    }

    public static function project(int $pid) {
        return view('pages/project', self::getProjectData($pid));
    }

    public static function publicProjects() {
        return view('pages/projects');
    }

    public static function projectAdminView(int $pid, Arrayable|Array $data = [], ?string $fragment = null) {
        $viewData = [
            ...self::getProjectData($pid),
            ...$data
        ];

        if($fragment) {
            return view('pages/projects/edit', $viewData)->fragment($fragment);
        } else {
            return view('pages/projects/edit', $viewData);
        }
    }

    public static function projectAdmin(int $pid) {
        return self::projectAdminView($pid);
    }

    public static function create(int $pid) {
        $projManager = self::getProjectManager($pid);

	    // addNewProject
		$pid = $projManager->insertProject(request()->all());
		if(!$pid) $statusStr = $projManager->getErrorMessage();

        return self::projectAdmin($pid);
    }

    public static function update(int $pid) {
        $projManager = self::getProjectManager($pid);

	    // submitEdit
		$projManager->updateProject(request()->all());

        return self::projectAdmin($pid);
    }

    public static function delete(int $pid) {
        $projManager = self::getProjectManager($pid);

        if(!$projManager->deleteProject(request('pid'))) {
            return self::projectAdminView($pid, [
                'delete_errors' =>  new MessageBag([ $projManager->getErrorMessage() ])
            ], 'project_delete_form');
        }

        return response(null, 204, [
            // TODO (Logan) Should this just be checklists  page,
            'HX-Location' => '/projects',
        ]);
    }

    public static function removeUser(int $pid, int $uid) {
        $projManager = self::getProjectManager($pid);
        $error = false;

	    // deluid
		if(!$projManager->deleteUserRole('ProjAdmin', $pid, request('uid'))) {
			$error = $projManager->getErrorMessage();
		}

        return self::projectAdminView($pid, [ 'add_user_errors' => $error? new MessageBag([ $error ]): null ], 'managers');
    }

    public static function addUser(int $pid) {
        $projManager = self::getProjectManager($pid);
        $error = false;
        $addUid = request('uid');

        if(!$addUid) {
            $error = 'A user must be selected';
        } else if(!$projManager->insertUserRole($addUid, UserRole::PROJ_ADMIN, 'fmprojects', $pid, request()->user()->uid)) {
            $error = $projManager->getErrorMessage();
        }

        return response(self::projectAdminView($pid, [ 'add_user_errors' => $error? new MessageBag([ $error ]): null ], 'managers'), 201);
    }

    public static function addChecklist(int $pid) {
        $projManager = self::getProjectManager($pid);
        $addClid = request('clid');
        $error = null;

        if(!$addClid) {
			$error = new MessageBag(['A checklist must be selected']);
        } else if(!$projManager->insertChecklistProjectLink($addClid)) {
			$error = new MessageBag([ $projManager->getErrorMessage() ]) ;
        }

        return response(self::projectAdminView($pid, [ 'checklist_form_errors' => $error ], 'checklists'), 201);
    }

    public static function removeChecklist(int $pid) {
        $projManager = self::getProjectManager($pid);
        $delClid = request('clid');
        $error = null;

        if(!$delClid) {
			$error = new MessageBag(['A checklist must be selected']);
        } else if(!$projManager->deleteChecklistProjectLink(request('clid'))) {
			$error = new MessageBag([ $projManager->getErrorMessage() ]) ;
        }

        return response(self::projectAdminView($pid, [ 'checklist_form_errors' => $error ], 'checklists'));
    }
}
