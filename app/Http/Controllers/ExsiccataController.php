<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;

class ExsiccataController extends Controller {
    //title list page
    public static function index(Request $request) {
        $ometid = (int) $request->query('ometid', 0);
        $omenid = (int) $request->query('omenid', 0);

        if ($omenid) {
            return self::number($request, $ometid, $omenid);
        }

        if ($ometid) {
            return self::title($request, $ometid);
        }

        $filters = self::filters($request);

        if (in_array($request->query('formsubmit'), ['dlexs', 'dlexs_titleOnly'], true)) {
            return self::downloadIndex($filters, $request->query('formsubmit') === 'dlexs_titleOnly');
        }

        $exsManager = self::exsManager();

        return view('pages.collections.exsiccata', [
            ...$filters,
            'titles' => $exsManager->getTitleArr(
                $filters['searchTerm'],
                $filters['specimenOnly'],
                $filters['imagesOnly'],
                $filters['collId'],
                $filters['sortBy'],
            ),
            'collections' => $exsManager->getCollArr('all'),
            'isEditor' => self::isEditor($request),
            'isDetailPage' => false,
            'isOccurrencePage' => false,
        ]);
    }

    //adding exiccatae on index page
    public static function store(Request $request) {
        $omenid = (int) $request->input('omenid', 0);
        $ometid = (int) $request->input('ometid', 0);

        if ($omenid) {
            return self::storeNumber($request, $ometid, $omenid);
        }

        if ($ometid) {
            return self::storeTitle($request, $ometid);
        }

        self::authorizeEdit($request);

        $exsManager = self::exsManager('write');
        $action = (string) $request->input('formsubmit');
        $status = '';

        if ($action === 'Add Exsiccata Title') {
            $status = $exsManager->addTitle($request->all(), self::editedBy($request));
            $status = $status ?: 'SUCCESS: exsiccata title added';
        }

        return self::redirectWithStatus($request, $status);
    }

    //Displays the exsiccata title
    public static function title(Request $request, int $ometid) {
        $exsManager = self::exsManager();
        $filters = self::filters($request);
        $title = $exsManager->getTitleObj($ometid);

        if (! $title) {
            return self::missingRecordView($request, $ometid, null);
        }

        $selectLookupArr = $exsManager->getSelectLookupArr();
        unset($selectLookupArr[$ometid]);

        return view('pages.collections.exsiccata', [
            ...$filters,
            'ometid' => $ometid,
            'title' => $title,
            'numbers' => $exsManager->getExsNumberArr(
                $ometid,
                $filters['specimenOnly'],
                $filters['imagesOnly'],
                $filters['collId'],
            ),
            'selectLookupArr' => $selectLookupArr,
            'isEditor' => self::isEditor($request),
            'isDetailPage' => true,
            'isOccurrencePage' => false,
        ]);
    }

    // add,edit, delete, merge forms on ometid page
    public static function storeTitle(Request $request, int $ometid) {
        self::authorizeEdit($request);

        $exsManager = self::exsManager('write');
        $action = (string) $request->input('formsubmit');
        $status = '';
        $redirectParams = ['ometid' => $ometid];

        if ($action === 'Save') {
            $data = $request->all();
            $data['ometid'] = $ometid;
            $status = $exsManager->editTitle($data, self::editedBy($request));
            $status = $status ?: 'SUCCESS: exsiccata title updated';
        } elseif ($action === 'Delete Exsiccata') {
            $status = $exsManager->deleteTitle($ometid);
            $status = $status ?: 'SUCCESS: exsiccata title deleted';
            if (! str_starts_with($status, 'ERROR')) {
                $redirectParams = [];
            }
        } elseif ($action === 'Merge Exsiccatae') {
            $targetOmetid = (int) $request->input('targetometid');
            $status = $exsManager->mergeTitles($ometid, $targetOmetid);
            $status = $status ?: 'SUCCESS: exsiccata titles merged';
            if ($targetOmetid && ! str_starts_with($status, 'ERROR')) {
                $redirectParams = ['ometid' => $targetOmetid];
            }
        } elseif ($action === 'Add New Number') {
            $data = $request->all();
            $data['ometid'] = $ometid;
            $status = $exsManager->addNumber($data);
            $status = $status ?: 'SUCCESS: exsiccata number added';
        }

        return self::redirectWithStatus($request, $status, $redirectParams);
    }

    //Display omenid page
    public static function number(Request $request, int $ometid, int $omenid) {
        $exsManager = self::exsManager();
        $filters = self::filters($request);
        $number = $exsManager->getExsNumberObj($omenid);

        if (! $number || ($ometid && (int) ($number['ometid'] ?? 0) !== $ometid)) {
            return self::missingRecordView($request, $ometid, $omenid);
        }

        $ometid = (int) ($number['ometid'] ?? $ometid);

        $occurrenceGroups = $exsManager->getExsOccArr($omenid);
        $selectLookupArr = $exsManager->getSelectLookupArr();
        unset($selectLookupArr[$ometid]);

        return view('pages.collections.exsiccata', [
            ...$filters,
            'ometid' => $ometid,
            'omenid' => $omenid,
            'title' => [
                'ometid' => $number['ometid'],
                'title' => $number['title'],
                'abbreviation' => $number['abbreviation'],
                'editor' => $number['editor'],
                'exsrange' => $number['exsrange'],
                'sourceidentifier' => $number['sourceidentifier'] ?? null,
            ],
            'number' => $number,
            'occurrences' => $occurrenceGroups[$omenid] ?? [],
            'collections' => $exsManager->getCollArr(),
            'selectLookupArr' => $selectLookupArr,
            'isEditor' => self::isEditor($request),
            'isDetailPage' => true,
            'isOccurrencePage' => true,
        ]);
    }

    // add,edit, delete, merge linked specimen forms on ometid page
    public static function storeNumber(Request $request, int $ometid, int $omenid) {
        self::authorizeEdit($request);

        $exsManager = self::exsManager('write'); //needed for updating data permission

        $action = (string) $request->input('formsubmit');
        $status = '';
        $redirectParams = ['ometid' => $ometid, 'omenid' => $omenid];

        if ($action === 'Save Edits') {
            $data = $request->all();
            $data['omenid'] = $omenid;
            $status = $exsManager->editNumber($data);
            $status = $status ?: 'SUCCESS: exsiccata number updated';
        } elseif ($action === 'Delete Number') {
            $status = $exsManager->deleteNumber($omenid);
            $status = $status ?: 'SUCCESS: exsiccata number deleted';
            if (! str_starts_with($status, 'ERROR')) {
                $redirectParams = ['ometid' => $ometid];
            }
        } elseif ($action === 'Transfer Number') {
            $targetOmetid = (int) trim((string) $request->input('targetometid'), 'k'); // Added "k" prefix to key so that Chrom would maintain the correct sort order (from legacy code)
            $status = $exsManager->transferNumber($omenid, $targetOmetid);
            $status = $status ?: 'SUCCESS: exsiccata number transferred';
            if ($targetOmetid && ! str_starts_with($status, 'ERROR')) {
                $redirectParams = ['ometid' => $targetOmetid];
            }
        } elseif ($action === 'Add Specimen Link') {
            $data = $request->all();
            $data['omenid'] = $omenid;
            $status = $exsManager->addOccLink($data);
        } elseif ($action === 'Save Specimen Link Edit') {
            $data = $request->all();
            $data['omenid'] = $omenid;
            $status = $exsManager->editOccLink($data);
            $status = $status ?: 'SUCCESS: specimen link updated';
        } elseif ($action === 'Delete Link to Specimen') {
            $status = $exsManager->deleteOccLink($omenid, (int) $request->input('occid'));
            $status = $status ?: 'SUCCESS: specimen link deleted';
        } elseif ($action === 'Transfer Specimen') {
            $targetOmetid = (int) trim((string) $request->input('targetometid'), 'k');
            $targetExsNumber = (string) $request->input('targetexsnumber');
            $status = $exsManager->transferOccurrence(
                $omenid,
                (int) $request->input('occid'),
                $targetOmetid,
                $targetExsNumber,
            );
            $status = $status ?: 'SUCCESS: specimen transferred';
        }

        return self::redirectWithStatus($request, $status, $redirectParams);
    }

    //get the exiccatae manager from legacy class
    private static function exsManager(string $type = 'readonly') {
        include_once legacy_path('/classes/OccurrenceExsiccatae.php');

        return new \OccurrenceExsiccatae($type);
    }

    //Normalize the filtering variables,
    private static function filters(Request $request) {
        return [
            'searchTerm' => trim((string) $request->input('searchterm', '')),
            'specimenOnly' => (int) $request->boolean('specimenonly'),
            'imagesOnly' => (int) $request->boolean('imagesonly'),
            'collId' => (int) $request->input('collid', 0),
            'sortBy' => (int) $request->input('sortby', 0),
        ];
    }

    //check user, isEditor
    private static function isEditor(Request $request) {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        //todo test if user not super admin and has collection editor permission
        return $user->hasOneRoles([UserRole::SUPER_ADMIN]) || $user->roles()->where('role', UserRole::COLL_ADMIN)->exists();
    }

    // check isEditor if attempted direct request (todo tes)
    private static function authorizeEdit(Request $request) {
        //
        if (! self::isEditor($request)) {
            abort(403);
        }
    }

    //get the user name for lagacy class
    private static function editedBy(Request $request) {

        $user = $request->user();

        return (string) ($user?->username ?? $user?->name ?? '');
    }

    //keep the status/error after form submit
    private static function redirectWithStatus(Request $request, string $status, array $redirectParams = []) {
        //keep ccurrent filters(?) in queryString instead of url
        return redirect(route('exsiccata.index') . self::queryString($request, $redirectParams))
            ->with('status', $status)
            ->with('statusType', str_starts_with($status, 'SUCCESS') ? 'success' : 'error');
    }

    //helper function to preserve the filter values, and normalize checkbox/select/int cast
    private static function queryString(Request $request, array $redirectParams = []) {
        $query = array_filter([
            'ometid' => $redirectParams['ometid'] ?? null,
            'omenid' => $redirectParams['omenid'] ?? null,
            'searchterm' => $request->input('searchterm'),
            'specimenonly' => $request->input('specimenonly'),
            'imagesonly' => $request->input('imagesonly'),
            'collid' => $request->input('collid'),
            'sortby' => $request->input('sortby'),
        ], static fn ($value) => $value !== null && $value !== '' && $value !== 0 && $value !== '0');

        return $query ? '?' . http_build_query($query) : '';
    }

    // function for missing record display
    private static function missingRecordView(Request $request, ?int $ometid, ?int $omenid) {
        return view('pages.collections.exsiccata', [
            ...self::filters($request),
            'ometid' => $ometid,
            'omenid' => $omenid,
            'title' => [],
            'number' => [],
            'titles' => [],
            'numbers' => [],
            'occurrences' => [],
            'collections' => [],
            'selectLookupArr' => [],
            'isEditor' => self::isEditor($request),
            'isDetailPage' => true,
            'isOccurrencePage' => (bool) $omenid,
            'unableLocateRecord' => true,
        ]);
    }

    //download handle ->legacy function
    private static function downloadIndex(array $filters, bool $titleOnly) {
        $exsManager = self::exsManager();
        $fileName = 'exsiccatiOutput_' . time() . '.csv';
        ob_start();
        $exsManager->exportExsiccatiAsCsv(
            $filters['searchTerm'],
            $filters['specimenOnly'],
            $filters['imagesOnly'],
            $filters['collId'],
            $titleOnly,
        );
        $content = ob_get_clean();

        //get the file as csv
        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
