<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserPermissonsController extends Controller {
    private static function getPermissionsManager() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/PermissionsManager.php');

        $userManager = new \PermissionsManager();
        return $userManager;
    }

    public function adminSearchPage(Request $request) {
        $searchterm = request('searchterm') ?? false;

        $userManager = self::getPermissionsManager();
        $users = $userManager->getUsers($searchterm);

        if($request->header('hx-request') && $request->header('hx-target')) {
            return view('pages/user/permissions', ['users' => $users])->fragment('user-list');
        }

        return view('pages/user/permissions', ['users' => $users]);
    }

    public function deletePermisson(int $uid, string $role) {
        $userManager = self::getPermissionsManager();
        $userManager->deletePermission($uid, $role, request('tablePk'));

        //return $this->permissionsProfile($uid);
        if(request()->header('hx-request')) {
            $pageData = [
                'permissions' => $userManager->getUserPermissions($uid),
            ];

            // if(count($errors)) {
            //    $pageData['errors'] = message_bag($errors);
            // } else {
            //    $pageData['info'] = message_bag(['Successfully updated user permissions']);
            // }

            return view('user/KeyedPermissions', $pageData);
        } else {
            $pageData = $this->getPermissionsProfileInfo($uid);
            // $pageData['info'] = message_bag(['Successfully deleted user permissions']);

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
            if(request($role) && !$hasRole) {
                $status = $userManager->addPermission($uid, $role, null);
                if($status) $errors[] = $status;
            } else if(!request($role) && $hasRole) {
                $userManager->deletePermission($uid, $role, null);
            }
        }

        if(request()->header('hx-request')) {
            $pageData = [
                'permissions' => $userManager->getUserPermissions($uid),
            ];

            if(count($errors)) {
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

/*
    public function addPermissions(int $uid) {
        $userManager = self::getPermissionsManager();

        foreach (request('p') as $value) {
            $roleParts= explode('-', $value);
            $role = $roleParts[0];
            $tablePk = $roleParts[1] ?? null;

            if(in_array($role, UserRole::roles())) {
                $userManager->addPermission($uid, $role, $tablePk);
            }
        }

        return redirect('/user/' . $uid . '/permissions/');
    }

*/

    public function addPermission(int $uid) {
        $userManager = self::getPermissionsManager();
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
        if(array_key_exists(UserRole::COLL_ADMIN, $permissions)) {
            $observation_collections = array_diff_key($observation_collections, $permissions[UserRole::COLL_ADMIN]);
            $specimen_collections = array_diff_key($specimen_collections, $permissions[UserRole::COLL_ADMIN]);
        }

        $personal_observation_collections = $userManager->getCollectionMetadata(
            Collection::GeneralObservations
        );
        if(array_key_exists(UserRole::PERSONAL_OBS_ADMIN, $permissions)) {
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
