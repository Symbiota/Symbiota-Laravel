<?php

namespace App\Http\Controllers;

use App\Models\Collection;

class UserPermissonsController extends Controller {
    public function adminSearchPage() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/PermissionsManager.php');

        $userManager = new \PermissionsManager();
        $users = $userManager->getUsers(request('searchTerm') ?? false);

        return view('pages/user/permissions', ['users' => $users]);
    }

    public function permissionsProfile(int $uid) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/PermissionsManager.php');

        $userManager = new \PermissionsManager();
        $user = $userManager->getUser($uid);

        $specimen_collections = $userManager->getCollectionMetadata(
            implode(',', [Collection::Specimens, Collection::FossilSpecimens])
        );

        $observation_collections = $userManager->getCollectionMetadata(
            Collection::Observations
        );

        $personal_observation_collections = $userManager->getCollectionMetadata(
            Collection::GeneralObservations
        );

        // $collArr = array_diff_key($collArr,$userPermissions["CollAdmin"]);
        // $obsArr = array_diff_key($obsArr,$userPermissions["CollAdmin"]);
        // $personalObsArr = array_diff_key($personalObsArr,$userPermissions['PersonalObsAdmin']);
        // Error handle no user
        $permissions = $userManager->getUserPermissions($uid);

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

        return view('pages/user/permissionsProfile', [
            'user' => $user,
            'permissions' => $permissions,
            'specimen_collections' => $specimen_collections,
            'observation_collections' => $observation_collections,
            'personal_observation_collections' => $personal_observation_collections,
            'projects' => $projects,
            'checklists' => $checklists,
        ]);
    }
}
