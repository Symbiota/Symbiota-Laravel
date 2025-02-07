<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller {
    public static function search(Request $request) {
        $start = $request->query('start') ?? 0;
        $media = DB::table('media as m')
            ->leftJoin('taxa as t', 't.tid', '=', 'm.tid')
            ->leftJoin('users as u', 'u.uid', '=', 'm.creatoruid')
            ->leftJoin('omoccurrences as o', 'o.occid', '=', 'm.occid')
            ->when($request->query('media_type'), function (Builder $query, $type) {
                $query->where('m.media_type', '=', $type);
            })
            ->when($request->query('tid'), function (Builder $query, $tid) {
                $query->whereIn('t.tid', is_array($tid) ? $tid : [$tid]);
            })
            ->when($request->query('taxa'), function (Builder $query, $taxa) {
                $query->whereIn('t.sciName', array_map('trim', explode(',', $taxa)));
            })
            ->when($request->query('uid'), function (Builder $query, $uid) {
                $query->where('u.uid', '=', $uid);
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
            ->offset($start)
            ->get();
    }

    public static function searchPage(Request $request) {
        $media = [];
        if (count($request->all()) > 0) {
            $media = self::search($request);

            if ($request->query('partial')) {
                $query_params = $request->except('partial');
                $query_params['start'] = $start;

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

    public static function getMediaData() {

    }

    public static function add() {

    }

    public static function delete() {

    }

    public static function edit() {

    }
}
