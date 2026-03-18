<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\UserRole;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\MessageBag;

class ProjectController extends Controller {
    private static function getProjectData(int $pid) {
        $project = Project::query()
            ->where('pid', $pid)
            ->first();

        $checklists = $project->checklists();

        return ['project' => $project, 'checklists' => $checklists];
    }

    /* Wrapper to reuse setup of legacy project manager. This should get remove when the models system is integrated into projects */
    private static function getProjectManager(?int $pid = null) {
        include_once legacy_path('/classes/ImInventories.php');
        $projManager = new \ImInventories('write');

        if ($pid) {
            $projManager->setPid($pid);
        }

        return $projManager;
    }

    public static function project(int $pid) {
        $data = self::getProjectData($pid);

        return view('pages/project', $data);
    }

    public static function publicProjects() {
        return view('pages/projects');
    }

    public static function projectAdminView(int $pid, Arrayable|array $data = [], ?string $fragment = null) {
        $viewData = [
            ...self::getProjectData($pid),
            ...$data,
        ];

        if ($fragment) {
            return view('pages/projects/edit', $viewData)->fragment($fragment);
        } else {
            return view('pages/projects/edit', $viewData);
        }
    }

    public static function projectCreate() {
        return view('pages/projects/create');
    }

    public static function create() {
        $projManager = self::getProjectManager();
        $pid = $projManager->insertProject(request()->all());
        if (! $pid) {
            return view('pages/projects/create', [
                'errors' => new MessageBag([$projManager->getErrorMessage()]),
            ]);
        } else {
            return response(null, 201, [
                'HX-Location' => '/projects/' . $pid . '/edit',
            ]);
        }
    }

    public static function projectAdmin(int $pid) {
        return self::projectAdminView($pid);
    }

    public static function update(int $pid) {
        $projManager = self::getProjectManager($pid);
        $projManager->updateProject(request()->all());

        return self::projectAdmin($pid);
    }

    public static function delete(int $pid) {
        $projManager = self::getProjectManager($pid);

        if (! $projManager->deleteProject(request('pid'))) {
            return self::projectAdminView($pid, [
                'delete_errors' => new MessageBag([$projManager->getErrorMessage()]),
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
        if (! $projManager->deleteUserRole('ProjAdmin', $pid, request('uid'))) {
            $error = $projManager->getErrorMessage();
        }

        return self::projectAdminView($pid, ['add_user_errors' => $error ? new MessageBag([$error]) : null], 'managers');
    }

    public static function addUser(int $pid) {
        $projManager = self::getProjectManager($pid);
        $error = false;
        $addUid = request('uid');

        if (! $addUid) {
            // TODO (Logan) translate
            $error = 'A user must be selected';
        } elseif (! $projManager->insertUserRole($addUid, UserRole::PROJ_ADMIN, 'fmprojects', $pid, request()->user()->uid)) {
            $error = $projManager->getErrorMessage();
        }

        return response(self::projectAdminView($pid, ['add_user_errors' => $error ? new MessageBag([$error]) : null], 'managers'), 201);
    }

    public static function addChecklist(int $pid) {
        $projManager = self::getProjectManager($pid);
        $addClid = request('clid');
        $error = null;

        if (! $addClid) {
            // TODO (Logan) translate
            $error = new MessageBag(['A checklist must be selected']);
        } elseif (! $projManager->insertChecklistProjectLink($addClid)) {
            $error = new MessageBag([$projManager->getErrorMessage()]);
        }

        return response(self::projectAdminView($pid, ['checklist_form_errors' => $error], 'checklists'), 201);
    }

    public static function removeChecklist(int $pid) {
        $projManager = self::getProjectManager($pid);
        $delClid = request('clid');
        $error = null;

        if (! $delClid) {
            // TODO (Logan) translate
            $error = new MessageBag(['A checklist must be selected']);
        } elseif (! $projManager->deleteChecklistProjectLink(request('clid'))) {
            $error = new MessageBag([$projManager->getErrorMessage()]);
        }

        return response(self::projectAdminView($pid, ['checklist_form_errors' => $error], 'checklists'));
    }
}
