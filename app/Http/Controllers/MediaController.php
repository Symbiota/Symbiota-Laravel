<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller {
    public static function search(Request $request) {
        $start = $request->query('start') ?? 0;
        $media_query = DB::table('media as m')
            ->leftJoin('taxa as t', 't.tid', '=', 'm.tid')
            ->leftJoin('users as u', 'u.uid', '=', 'm.creatoruid')
            ->leftJoin('omoccurrences as o', 'o.occid', '=', 'm.occid')
            ->when($request->query('media_type'), function (Builder $query, $type) {
                $query->where('m.mediaType', '=', $type);
            })
            ->when($request->query('tid'), function (Builder $query, $tid) {
                $query->leftJoin('taxstatus as ts', 'ts.tid', '=', 'm.tid');
                $query->whereIn('ts.tidaccepted', is_array($tid) ? $tid : [$tid]);
                $query->where('ts.taxauthid', 1);
            })
            ->when($request->query('taxon_sort_order'), function (Builder $query) {
                $query->orderBy('m.sortsequence', 'ASC');
                $query->orderBy('m.sortOccurrence', 'ASC');
            })
            ->when($request->query('taxa'), function (Builder $query, $taxa) {
                $query->whereIn('t.sciName', array_map('trim', explode(',', $taxa)));
            })
            ->when($request->query('uid'), function (Builder $query, $uid) {
                $query->where('u.uid', '=', $uid);
            })
            ->when($request->query('collId'), function (Builder $query, $collId) {
                $query->where('o.collId', '=', $collId);
            })
            ->when($request->query('tag'), function (Builder $query, $tag) {
                $query->leftJoin('imagetag as tag', 'tag.imgid', '=', 'm.media_id')
                    ->leftJoin('imagetagkey as imgkey', 'imgkey.tagkey', '=', 'tag.keyvalue')
                    ->where('imgkey.tagkey', '=', $tag);
            })
            /* Requires strict mode currently
            ->when($request->query('resource_counts'), function(Builder $query, $group) {
                if($group === 'one_per_taxon') {
                    $query->groupBy('t.tid');
                } else if($group = 'one_per_specimen') {
                    $query->groupBy('o.occid');
                }
            })
            */
            ->select('m.url', 'm.thumbnailUrl', 't.sciName', 'o.occid')
            ->limit(30)
            ->offset($start);

        $media = $media_query->get();

        return $media;
    }

    public static function searchPage(Request $request) {
        $media = [];
        if (count($request->all()) > 0) {
            $media = self::search($request);

            if ($request->query('partial')) {
                $query_params = $request->except('partial');
                $query_params['start'] = $request->query('start') ?? 0;

                $base_url = $request->header('referer') ?? url()->current();
                $base_url = substr($base_url, 0, strpos('?', $base_url));

                $new_url = $base_url .
                    '?' .
                    http_build_query($query_params);

                return response(view('media/item', ['media' => $media]))
                    ->header('HX-Replace-URL', $new_url);
            }
        }
        $creators = DB::table('users as u')
            ->join('media as m', 'm.creatoruid', '=', 'u.uid')
            ->select('uid', 'name')
            ->distinct()
            ->get();

        $tag_options = DB::table('imagetagkey as key')
            ->select('tagkey')
            ->get();

        return view('pages/media/search', ['media' => $media, 'creators' => $creators, 'tags' => $tag_options]);
    }

    public static function getMediaData() {}

    public static function add() {}

    public static function delete() {}

    public static function edit() {}

    public static function libraryPage(Request $request) {
        $genus = [];
        $families = [];

        $select = [$request->query('taxa-type') === 'family'? 'ts.family as name': 't.UnitName1 as name'];

        $taxa_query = DB::table('media as m')
                ->join('taxstatus as ts', 'ts.tid', 'm.tid')
            ->join('taxa as t', 't.tid', 'ts.tidaccepted')
            ->distinct()
            ->whereRaw('ts.taxauthid = 1')
            ->whereRaw('t.RankId > 219');

        if($request->query('target') === 'genus') {
            $taxa_query->select('t.UnitName1 as name')
                ->orderBy('t.UnitName1');

            if($request->query('taxa')) {
                $taxa_query->whereLike('ts.family', $request->query('taxa') . '%');
            }
        } elseif($request->query('taxa')) {
            $taxa_query->select('t.sciName as name', 't.tid')
                ->orderBy('t.sciName')
                ->whereLike('t.sciName', $request->query('taxa') . '%');

        } else {
            $taxa_query->selectRaw('ts.family as name')
                ->whereNotNull('ts.family')
                ->orderBy('ts.family');
        }

        $view = view('pages/media/library', [
            'taxa' => $taxa_query->get(),
        ]);

        if($request->query('fragment') === 'taxa_list') {
            return $view->fragment('taxa_list');
        } else {
            return $view;
        }
    }

    public static function contributorsPage() {
        $creators = DB::table('media')
            ->join('users', 'uid', 'creatorUid')
            ->groupBy('creatorUid')
            ->selectRaw('creatorUid, firstName, lastName, count(*) as media_count')
            ->orderBy('lastName')
            ->get();

        $collections = DB::table('omcollections as c')
            ->join('omcollectionstats as s', 's.collId', 'c.collId')
            ->select('c.collId', 'collectionName', 's.dynamicProperties', 'collType')
            ->whereLike('s.dynamicProperties', '%imgcnt%')
            ->orderBy('collectionName')
            ->get();

        return view('pages/media/contributors', [
            'creators' => $creators,
            'collections' => $collections,
        ]);
    }
}
