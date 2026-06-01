<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Collection;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissonsController extends Controller {
    private static function getPermissionsManager() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/PermissionsManager.php');

        $userManager = new \PermissionsManager();

        return $userManager;
    }

    public function adminUserRegister(Request $request) {
        return view('pages/signup');
    }

    public function adminCreateUser(Request $request) {
        $action = new CreateNewUser();

        try {
            $user = $action->create($request->all());
            dd($user);
            return redirect()->route('home');
        } catch(\Throwable $th) {
            $th->getMessage();
        }
    }

    public function loginAs(int $uid) {
        if ($user = User::query()->where('uid', $uid)->first()) {
            Auth::login($user);

            return redirect()->route('home');
        } else {
            return redirect()->back();
        }
    }

    public function adminSearchPage(Request $request) {
        $searchterm = request('searchterm') ?? false;

        $userManager = self::getPermissionsManager();
        $users = $userManager->getUsers($searchterm);

        if ($request->header('hx-request') && $request->header('hx-target')) {
            return view('pages/user/permissions', ['users' => $users])->fragment('user-list');
        }

        return view('pages/user/permissions', ['users' => $users]);
    }

    public function deletePermisson(int $uid, string $role) {
        $userManager = self::getPermissionsManager();
        $userManager->deletePermission($uid, $role, request('tablePk'));

        if (request()->header('hx-request')) {
            $pageData = [
                'permissions' => $userManager->getUserPermissions($uid),
            ];

            return view('user/KeyedPermissions', $pageData);
        } else {
            $pageData = $this->getPermissionsProfileInfo($uid);

            return view('pages/user/permissionsProfile', $pageData);
        }
    }

    // Only for things without tablepks
    public function updatePermissions(int $uid) {
        $userManager = self::getPermissionsManager();
        $permissions = $userManager->getUserPermissions($uid);
        $errors = [];

        foreach ([
            UserRole::SUPER_ADMIN,
            UserRole::TAXONOMY,
            UserRole::TAXON_PROFILE,
            UserRole::GLOSSARY_EDITOR,
            UserRole::KEY_ADMIN,
            UserRole::KEY_EDITOR,
            UserRole::CL_CREATE,
            UserRole::RARE_SPP_ADMIN,
            UserRole::RARE_SPP_READER_ALL,
        ] as $role) {
            $hasRole = $permissions[$role] ?? false;
            if (request($role) && ! $hasRole) {
                $status = $userManager->addPermission($uid, $role, null);
                if ($status) {
                    $errors[] = $status;
                }
            } elseif (! request($role) && $hasRole) {
                $userManager->deletePermission($uid, $role, null);
            }
        }

        if (request()->header('hx-request')) {
            $pageData = [
                'permissions' => $userManager->getUserPermissions($uid),
            ];

            if (count($errors)) {
                $pageData['errors'] = message_bag($errors);
            } else {
                $pageData['info'] = message_bag(['Successfully updated user permissions']);
            }

            return view('user/GeneralPermissionsForm', $pageData);
        } else {
            $pageData = $this->getPermissionsProfileInfo($uid);
            $pageData['info'] = message_bag(['Successfully updated user permissions']);

            return view('pages/user/permissionsProfile', $pageData);
        }
    }

    public function addPermission(int $uid) {
        $userManager = self::getPermissionsManager();

        $requires_table_pk = [
            UserRole::COLL_ADMIN,
            UserRole::COLL_EDITOR,
            UserRole::RARE_SPP_READER,
            UserRole::PROJ_ADMIN,
            UserRole::CL_ADMIN,
        ];

        if (! request('role')) {
            return redirect('/user/' . $uid . '/permissions/')->withErrors('You must select a role for this permission');
        } elseif (! request('tablePk') && in_array(request('role'), $requires_table_pk)) {
            return redirect('/user/' . $uid . '/permissions/')->withErrors('You must select an option for this permisson');
        }

        $userManager->addPermission($uid, request('role'), request('tablePk'));

        return redirect('/user/' . $uid . '/permissions/');
    }

    private function getPermissionsProfileInfo($uid) {
        $userManager = self::getPermissionsManager();
        $user = $userManager->getUser($uid);
        $permissions = $userManager->getUserPermissions($uid);

        $specimen_collections = $userManager->getCollectionMetadata(
            implode(',', [Collection::Specimens, Collection::FossilSpecimens])
        );
        $observation_collections = $userManager->getCollectionMetadata(
            Collection::Observations
        );
        if (array_key_exists(UserRole::COLL_ADMIN, $permissions)) {
            $observation_collections = array_diff_key($observation_collections, $permissions[UserRole::COLL_ADMIN]);
            $specimen_collections = array_diff_key($specimen_collections, $permissions[UserRole::COLL_ADMIN]);
        }

        $personal_observation_collections = $userManager->getCollectionMetadata(
            Collection::GeneralObservations
        );
        if (array_key_exists(UserRole::PERSONAL_OBS_ADMIN, $permissions)) {
            $personal_observation_collections = array_diff_key($personal_observation_collections, $permissions[UserRole::PERSONAL_OBS_ADMIN]);
        }

        $pidArr = [];
        if (array_key_exists('ProjAdmin', $permissions)) {
            $pidArr = array_keys($permissions['ProjAdmin']);
        }
        $projects = $userManager->getProjectArr($pidArr);

        $cidArr = [];
        if (array_key_exists('ClAdmin', $permissions)) {
            $cidArr = array_keys($permissions['ClAdmin']);
        }
        $checklists = $userManager->getChecklistArr($cidArr);

        return [
            'user' => $user,
            'permissions' => $permissions,
            'specimen_collections' => $specimen_collections,
            'observation_collections' => $observation_collections,
            'personal_observation_collections' => $personal_observation_collections,
            'projects' => $projects,
            'checklists' => $checklists,
        ];
    }

    public function permissionsProfile(int $uid) {
        $pageData = $this->getPermissionsProfileInfo($uid);

        return view('pages/user/permissionsProfile', $pageData);
    }
}
