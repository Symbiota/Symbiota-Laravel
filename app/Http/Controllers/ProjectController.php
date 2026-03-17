<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
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

    public static function project(int $pid) {
        return view('pages/project', self::getProjectData($pid));
    }

    public static function publicProjects() {
        return view('pages/projects');
    }

    public static function create(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

	    // addNewProject
		$pid = $projManager->insertProject(request()->all());
		if(!$pid) $statusStr = $projManager->getErrorMessage();

        return self::projectAdmin($pid);
    }

    public static function update(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

	    // submitEdit
		$projManager->updateProject(request()->all());

        return self::projectAdmin($pid);
    }

    public static function delete(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

        if(!$projManager->deleteProject(request('pid'))) {
            $statusStr = $projManager->getErrorMessage();
            $data = self::getProjectData($pid);
            $data['delete_errors'] = new MessageBag([ $statusStr ]);

            return view('pages/projects/edit', $data)
                ->fragment('project_delete_form');
        }

        return response(null, 204, [
            // TODO (Logan) Should this just be checklists  page,
            'HX-Location' => '/projects',
        ]);
    }

    public static function removeUser(int $pid, int $uid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

	    // deluid
		if(!$projManager->deleteUserRole('ProjAdmin', $pid, request('uid'))) {
			$statusStr = $projManager->getErrorMessage();
		}

        return self::projectAdmin($pid);
    }

    public static function addUser(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

        //Add to Manager List
        if(!$projManager->insertUserRole(request('uid'), UserRole::PROJ_ADMIN, 'fmprojects', $pid, request()->user()->uid)) {
            $statusStr = $projManager->getErrorMessage();
        }

        return self::projectAdmin($pid);
    }

    public static function addChecklist(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

	    // Add Checklist
		if(!$projManager->insertChecklistProjectLink(request('clid'))){
			$statusStr = $projManager->getErrorMessage();
		}

        return self::projectAdmin($pid);
    }

    public static function removeChecklist(int $pid) {
        include_once(legacy_path('/classes/ImInventories.php'));
        $projManager = new \ImInventories('write');
        $projManager->setPid($pid);

	    // Delete Checklist
		if(!$projManager->deleteChecklistProjectLink(request('clid'))){
			$statusStr = $projManager->getErrorMessage();
		}

        return self::projectAdmin($pid);
    }

    // rename to adminView
    public static function projectAdmin(int $pid) {
        return view('pages/projects/edit', self::getProjectData($pid));
    }
}
