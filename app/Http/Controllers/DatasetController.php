<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DatasetController extends Controller {
    public static function createDataset(Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect(url('/login'));
        }

        $new_dataset = new Dataset();
        $new_dataset->fill($request->all());
        $new_dataset->uid = $user->uid;
        $new_dataset->save();

        return view('pages/user/profile', ['user' => $user])->fragment('datasets');
    }

    public static function index(Request $request) {
        if (! $request->user()) {
            return redirect(url('/login'));
        }

        $datasetManager = self::datasetManager($request);

        return view('pages/datasets/list', [
            'datasetArr' => $datasetManager->getDatasetArr(),
            'canCreate' => Gate::check('CL_CREATE'),
        ]);
    }

    public static function publicList(Request $request) {
        $datasetManager = self::datasetManager($request);

        return view('pages/datasets/public-list', [
            'datasets' => $datasetManager->getPublicDatasets(),
        ]);
    }

    public static function store(Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect(url('/login'));
        }

        if (! Gate::check('CL_CREATE')) {
            return redirect(route('datasets.index'))
                ->with('status', __('datasets.NO_PERMISSION'))
                ->with('statusType', 'error')
                ->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:250'],
            'description' => ['nullable', 'string'],
        ]);

        $datasetManager = self::datasetManager($request);
        $isPublic = $request->boolean('ispublic') ? 1 : 0;

        $created = $datasetManager->createDataset(
            $validated['name'],
            $validated['notes'] ?? '',
            $validated['description'] ?? '',
            $isPublic,
            (int) $user->uid
        );

        if (! $created) {
            $status = implode(',', $datasetManager->getErrorArr());

            return redirect(route('datasets.index'))
                ->with('status', $status)
                ->with('statusType', self::legacyStatusType($status))
                ->withInput();
        }

        return redirect(route('datasets.index'))
            ->with('status', __('datasets.CREATE_SUCCESS'))
            ->with('statusType', 'success');
    }

    public static function edit(Request $request, int $dataset_id) {
        if (! $request->user()) {
            return redirect(url('/login'));
        }

        return view('pages/datasets/edit', self::editPageData($request, $dataset_id));
    }

    public static function update(Request $request, int $dataset_id) {
        if (! $request->user()) {
            return redirect(url('/login'));
        }

        $datasetManager = self::datasetManager($request);
        $metadata = $datasetManager->getDatasetMetadata($dataset_id);
        $access = self::datasetAccess($request, $metadata);
        $action = (string) $request->input('submitaction', '');
        $tabIndex = (int) $request->input('tabindex', 0);

        if (! $access['isEditor']) {
            return self::redirectEdit($dataset_id, __('datasets_datasetmanager.NOT_AUTH'), 'error', $tabIndex);
        }

        if ($action === 'Remove Selected Occurrences') {
            if ($access['isEditor'] >= 3) {
                return self::redirectEdit($dataset_id, __('datasets_datasetmanager.NOT_AUTH'), 'error');
            }

            $occids = $request->input('occid', []);
            $occids = is_array($occids) ? $occids : [$occids];

            if (! $occids) {
                return self::redirectEdit($dataset_id, __('datasets_datasetmanager.PLS_SEL_SPC'), 'error');
            }

            if (! $datasetManager->removeSelectedOccurrences($dataset_id, $occids)) {
                return self::redirectEdit($dataset_id, implode(',', $datasetManager->getErrorArr()), 'error');
            }

            return self::redirectEdit($dataset_id, tabIndex: 0);
        }

        if ($access['isEditor'] !== 1) {
            return self::redirectEdit($dataset_id, __('datasets_datasetmanager.NOT_AUTH'), 'error', $tabIndex);
        }

        if ($action === 'Save Edits') {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'notes' => ['nullable', 'string', 'max:250'],
                'description' => ['nullable', 'string'],
            ]);

            $isPublic = $request->boolean('ispublic') ? 1 : 0;

            if (! $datasetManager->editDataset(
                $dataset_id,
                $validated['name'],
                $validated['notes'] ?? '',
                $validated['description'] ?? '',
                $isPublic
            )) {
                return self::redirectEdit(
                    $dataset_id,
                    implode(',', $datasetManager->getErrorArr()),
                    'error',
                    1,
                    true,
                );
            }

            return self::redirectEdit($dataset_id, __('datasets_datasetmanager.DS_EDITS_SAVED'), 'success', 1);
        }

        if ($action === 'Delete Dataset') {
            if (! $datasetManager->deleteDataset($dataset_id)) {
                return self::redirectEdit($dataset_id, implode(',', $datasetManager->getErrorArr()), 'error', 1);
            }

            return redirect(route('datasets.index'));
        }

        if ($action === 'addUser') {
            $uid = $request->input('uid');

            if (! is_numeric($uid)) {
                return self::redirectEdit($dataset_id, __('datasets_datasetmanager.SEL_USER_LIST'), 'error', 2);
            }

            if (! $datasetManager->addUser($dataset_id, (int) $uid, (string) $request->input('role'))) {
                return self::redirectEdit($dataset_id, implode(',', $datasetManager->getErrorArr()), 'error', 2);
            }

            return self::redirectEdit($dataset_id, __('datasets_datasetmanager.USER_ADDED'), 'success', 2);
        }

        if ($action === 'DelUser') {
            if (! $datasetManager->deleteUser(
                $dataset_id,
                (int) $request->input('uid'),
                (string) $request->input('role')
            )) {
                return self::redirectEdit($dataset_id, implode(',', $datasetManager->getErrorArr()), 'error', 2);
            }

            return self::redirectEdit($dataset_id, __('datasets_datasetmanager.USER_REMOVED'), 'success', 2);
        }

        return self::redirectEdit($dataset_id, tabIndex: $tabIndex);
    }

    public static function userSearch(Request $request) {
        if (! $request->user()) {
            return redirect(url('/login'));
        }

        $datasetManager = self::datasetManager($request);
        $term = (string) $request->query('term', $request->query('adduser', ''));

        return view('core/autocomplete/result', [
            'data' => $datasetManager->getUserList($term),
            'label' => 'label',
            'value' => 'id',
        ]);
    }

    public static function datasetProfilePage(int $dataset_id) {
        $user = request()->user();
        $dataset_query = Dataset::query()->where('datasetID', $dataset_id);

        if ($user) {
            $dataset_query
                ->leftJoin('userroles as ur', 'ur.uid', DB::raw($user->uid))
                ->where(function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query
                            ->where('omoccurdatasets.uid', $user->uid)
                            ->orwhere('role', UserRole::SUPER_ADMIN)
                            ->orwhere(function ($query) {
                                $query
                                    ->whereIn('role', [UserRole::DATASET_ADMIN, UserRole::DATASET_EDITOR])
                                    ->where('tablePK', 'datasetID');
                            });
                    })
                        ->orWhere('isPublic', 1);
                });

        } else {
            $dataset_query->where('isPublic', 1);
        }

        $dataset = $dataset_query->first();

        return view('pages/datasets/profile', [
            'dataset' => $dataset,
        ]);
    }

    private static function datasetManager(Request $request) {
        global $SERVER_ROOT, $CLIENT_ROOT, $SYMB_UID, $IS_ADMIN, $USER_RIGHTS;

        $user = $request->user();
        $SERVER_ROOT = legacy_path('');
        $CLIENT_ROOT = config('portal.use_client_root') ? '/' . config('portal.name') : '';
        $SYMB_UID = (int) ($user?->uid ?? 0);
        $IS_ADMIN = Gate::check('SUPER_ADMIN');
        $USER_RIGHTS = [];

        if ($user) {
            foreach ($user->roles as $role) {
                $USER_RIGHTS[$role->role][] = $role->tablePK;
            }
        }

        include_once legacy_path('/classes/OccurrenceDataset.php');

        return new \OccurrenceDataset();
    }

    private static function editPageData(Request $request, int $datasetId): array {
        $datasetManager = self::datasetManager($request);
        $metadata = $datasetId ? $datasetManager->getDatasetMetadata($datasetId) : [];
        $access = self::datasetAccess($request, $metadata);
        $pageNumber = max(1, (int) $request->query('pagenumber', 1));
        $retLimit = 200;
        $occurrences = [];
        $occurrenceCount = 0;
        $pageCount = 1;

        if ($datasetId && $access['isEditor']) {
            $occurrences = $datasetManager->getOccurrences($datasetId, $pageNumber, $retLimit);
            $occurrenceCount = (int) $datasetManager->getOccurrenceCount($datasetId);
            $pageCount = max(1, (int) ceil($occurrenceCount / $retLimit));

            if ($pageNumber > $pageCount) {
                $pageNumber = 1;
                $occurrences = $datasetManager->getOccurrences($datasetId, $pageNumber, $retLimit);
            }
        }

        return array_merge($access, [
            'datasetId' => $datasetId,
            'metadata' => $metadata,
            'occurrences' => $occurrences,
            'occurrenceCount' => $occurrenceCount,
            'pageNumber' => $pageNumber,
            'pageCount' => $pageCount,
            'users' => $access['isEditor'] === 1 ? $datasetManager->getUsers($datasetId) : [],
            'tabIndex' => min((int) $request->query('tabindex', 0), 2),
        ]);
    }

    private static function datasetAccess(Request $request, array $metadata): array {
        $user = $request->user();
        $role = '';
        $roleLabel = '';
        $isEditor = 0;

        if (! $user || ! $metadata) {
            return compact('role', 'roleLabel', 'isEditor');
        }

        if ((int) $user->uid === (int) ($metadata['uid'] ?? 0)) {
            $isEditor = 1;
            $role = __('datasets_datasetmanager.OWNER');
        } elseif (isset($metadata['roles']) && in_array(UserRole::DATASET_ADMIN, $metadata['roles'], true)) {
            $isEditor = 1;
            $role = __('datasets_datasetmanager.ADMIN');
        } elseif (isset($metadata['roles']) && in_array(UserRole::DATASET_EDITOR, $metadata['roles'], true)) {
            $isEditor = 2;
            $role = __('datasets_datasetmanager.EDITOR');
            $roleLabel = __('datasets_datasetmanager.ROLE_LABEL_EDITOR');
        } elseif (isset($metadata['roles']) && in_array('DatasetReader', $metadata['roles'], true)) {
            $isEditor = 3;
            $role = __('datasets_datasetmanager.READ_ACCESS');
        } elseif (Gate::check('SUPER_ADMIN')) {
            $isEditor = 1;
            $role = __('datasets_datasetmanager.SUPERADMIN');
        }

        return compact('role', 'roleLabel', 'isEditor');
    }

    private static function redirectEdit(
        int $datasetId,
        ?string $status = null,
        string $statusType = 'success',
        int $tabIndex = 0,
        bool $withInput = false,
    ) {
        $redirect = redirect(route('datasets.edit', ['dataset_id' => $datasetId]) . '?' . http_build_query([
            'tabindex' => $tabIndex,
        ]));

        if ($status) {
            $redirect->with('status', $status)->with('statusType', $statusType);
        }

        return $withInput ? $redirect->withInput() : $redirect;
    }

    private static function legacyStatusType(string $status): string {
        if (str_contains($status, 'WARNING')) {
            return 'warning';
        }

        if (str_contains($status, 'NOTICE')) {
            return 'notice';
        }

        return str_contains($status, 'ERROR') ? 'error' : 'success';
    }
}
