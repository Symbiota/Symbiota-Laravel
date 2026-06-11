<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Occurrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller {
    public static function collection(int $collid) {
        $collection = Collection::query()
            ->where('omcollections.collID', $collid)
            ->first();

        return view('pages/collections/profile', ['collection' => $collection, 'stats' => $collection->stats()]);
    }

    public static function searchPage(Request $request) {
        $collections = DB::table('omcollections')->select('*')->get();

        return view('pages/collections/search', ['collections' => $collections]);
    }

    public static function tablePage(Request $request) {
        $collection = DB::table('omcollections')
            ->where('collid', '=', $request->query('collid'))
            ->select('*')
            ->first();

        $query = Occurrence::buildSelectQuery($request->all());

        $view = view('pages/collections/table', [
            'occurrences' => $query->select('*')->paginate(100),
            'collection' => $collection,
            'page' => $request->query('page') ?? 0,
        ]);

        if ($request->header('HX-Request')) {
            if ($request->query('fragment') === 'rows') {
                return $view->fragment('rows');
            } elseif ($request->query('fragment') === 'table') {
                return $view->fragment('table');
            }
        }

        return $view;
    }

    public static function listPage(Request $request) {
        $params = $request->except(['page', '_token']);

        $occurrences = Cache::remember($request->fullUrl(), now()->addMinutes(1), function () use ($params, $request) {

            /* Also Works but pagination would need to be manual because of subquery stuff
         * Fix would be to save the img_cnt and audio_cnt when their values are created
        $sub = Occurrence::buildSelectQuery($request)
            ->select('o.*', DB::raw('0 as image_cnt'), DB::raw('0 as audio_cnt'))
            ->take(30);

        $query = DB::query()->fromSub($sub, 'o')
            ->leftJoin('media as m', 'm.occid', '=', 'o.occid')
            ->select(
                'o.*',
                DB::raw('sum(if(mediaType = "image", 1, 0)) as image_cnt'),
                DB::raw('sum(if(mediaType = "audio", 1, 0)) as audio_cnt')
            )
            ->groupBy('o.occid');

        return $query->get();
        */

            /* Works but can be slow */
            return Occurrence::buildSelectQuery($request->all())
                ->select('o.*', 'c.*', DB::raw('0 as image_cnt'), DB::raw('0 as audio_cnt'))
                ->paginate(30)->appends($params);
        });

        return view('pages/collections/list', ['occurrences' => $occurrences]);
    }

    public static function importPage(int $collId) {
        $params = request()->except(['page', '_token']);
        $collection = self::collection($collId);
        $uploadProfiles = DB::table('uploadspecparameters')
            ->select(['uspid', 'uploadtype', 'title'])
            ->where('collid', $collId)
            ->orderByRaw('uploadtype, title')
            ->get();
        var_dump($uploadProfiles);

        return view('pages/collections/import', ['collection' => $collection, 'uploadProfiles' => $uploadProfiles]);
    }

    public static function publisherPage() {
        return view('pages/collections/publisher');
    }

    public static function mapSearchPage() {
        return view('pages/collections/map-search');
    }

    public static function updateStats(int $collId) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/OccurrenceCollectionProfile.php');
        $collManager = new \OccurrenceCollectionProfile();
        $collManager->setCollid($collId);

        return response()->stream(function () use ($collManager) {
            $collManager->updateStatistics(true);
        }, 200, ['X-Accel-Buffering' => 'no']);
    }

    public static function skeletalView(int $collId) {
        return view('pages/collections/skeletal-submit', [
            'collection' => Collection::query()->where('collid', $collId)->first(),
        ]);
    }

    public static function skeletalAdd(int $collId) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/OccurrenceEditorManager.php');
        include_once legacy_path('/classes/GeographicThesaurus.php');

        $occurrenceEditor = new \OccurrenceEditorManager();
        $occurrenceEditor->setCollId($collId);
        $occurrence = request()->all();
        $occurrence['collid'] = $collId;

        $occid = true;
        $status = true;
        $error = false;

        if (request('stateprovince') && request('country')) {
            $occurrence['country'] = \GeographicThesaurus::getCountryByState(request('stateprovince'));
        }

        if (request('catalognumber') && $occurrenceEditor->catalogNumberExists(request('catalognumber'))) {
            $occid = $occurrenceEditor->getOccId();
            if (request('addaction') == '1') {
                $status = false;
                $error = 'dupeCatalogNumber';
            } elseif (request('addaction') == '2') {
                // Editor is always an editor because route has auth middleware for editors
                if (! $occurrenceEditor->editOccurrence($occurrence, true)) {
                    $error = $occurrenceEditor->getErrorStr();
                }
            }
        } else {
            if ($occurrenceEditor->addOccurrence($occurrence)) {
                $occid = $occurrenceEditor->getOccId();
            } else {
                $error = $occurrenceEditor->getErrorStr();
            }
        }

        if ($error) {
            return response(
                Blade::render(
                    '<x-errors :errors="$errors" />',
                    ['errors' => message_bag([$error])]
                )
            )->header('HX-Retarget', 'div[id=form-errors]')
                ->header('HX-Reswap', 'innerHTML');
        } else {
            $url = legacy_url('collections/editor/occurrenceeditor.php?occid=' . $occid . '&collid=' . $collId);

            return response(
                Blade::render(
                    '<div class="flex gap-2">
                        <x-link target="_blank" :href="$url">{{ $occid }}</x-link>
                        <x-link target="_blank" :href="$url . \'&tabtarget=2\'"><i class="fa-solid fa-file-image"></i></x-link>
                    </div>',
                    ['occid' => $occid, 'url' => $url]
                )
            );
        }
    }

    public static function comments() {
        return view('pages/collections/comments');
    }

    public static function batchDeterminations(int $collId) {
        $detManager = self::batchDeterminationManager($collId);

        return view('pages/collections/batch-determinations', [
            'collid' => $collId,
            'collectionName' => $detManager->getCollName(),
        ]);
    }

    public static function storeBatchDeterminations(Request $request, int $collId) {
        $detManager = self::batchDeterminationManager($collId);
        $occids = array_filter((array) $request->input('occid', []), 'is_numeric');
        $status = '';

        if ($request->input('formsubmit') === 'Add New Determinations') {
            if (! $occids) {
                $status = __('editor_batchdeterminations.SELECT_ONE');
            } else {
                foreach ($occids as $occid) {
                    $detManager->setOccId((int) $occid);
                    $detManager->addDetermination($request->all(), 1);
                }
                $status = 'SUCCESS: ' . count($occids) . ' annotations submitted';
            }
        }

        return redirect(url('collections/' . $collId . '/batchdeterminations'))
            ->with('status', $status)
            ->with('statusType', str_starts_with($status, 'SUCCESS') ? 'success' : 'error');
    }

    public static function batchDeterminationRecords(Request $request, int $collId) {
        $detManager = self::batchDeterminationManager($collId);

        return response()->json(
            $detManager->getNewDetItem(
                (string) $request->input('catalognumber', ''),
                (string) $request->input('sciname', ''),
                $request->boolean('allcatnum') ? 1 : 0,
            )
        );
    }

    public static function verifyBatchDeterminationTaxon(Request $request, int $collId) {
        $term = trim((string) $request->input('term', ''));

        if (! $term) {
            return response()->json(null);
        }

        return response()->json(self::rpcOccurrenceEditor()->getTaxonArr($term) ?: null);
    }

    // helper function for building determination manager
    private static function batchDeterminationManager(int $collId) {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/OccurrenceEditorDeterminations.php');

        $detManager = new \OccurrenceEditorDeterminations();
        $detManager->setCollId($collId);
        $detManager->getCollMap();

        return $detManager;
    }

    //helper function to call legacy rpc occurrence editor
    private static function rpcOccurrenceEditor() {
        global $SERVER_ROOT;
        include_once legacy_path('/classes/RpcOccurrenceEditor.php');

        return new \RpcOccurrenceEditor();
    }
}

enum UploadTypes {
    case DIRECTUPLOAD; //1
    case FILEUPLOAD; // 3
    case STOREDPROCEDURE; // 4
    case SCRIPTUPLOAD; //5
    case DWCAUPLOAD; // 6
    case IPTUPLOAD; // 8
    case NFNUPLOAD; // 9
    case RESTOREBACKUP; // 10
    case SYMBIOTA; // 13
}
