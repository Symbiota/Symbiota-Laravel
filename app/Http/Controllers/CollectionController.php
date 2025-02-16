<?php

namespace App\Http\Controllers;

use App\Models\Occurrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller {
    public static function collection(Request $request) {
        return 'Todo';
    }

    public static function searchPage(Request $request) {
        $collections = DB::table('omcollections')->select('*')->get();

        return view('pages/collections', ['collections' => $collections]);
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

        Cache::forget($request->fullUrl());
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

    public static function downloadPage(Request $request) {
        $params = $request->except(['page', '_token']);

        return view('pages/collections/download');

    }
}
