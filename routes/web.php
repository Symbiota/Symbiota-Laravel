<?php

use App\Http\Controllers\LegacyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarkdownController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* Simple View Routes */
Route::view('/', 'pages/home');
Route::view('Portal/', 'pages/home');
Route::view('/tw', 'tw-components');
Route::view('/sitemap', 'pages/sitemap');
Route::view('/usagepolicy', 'pages/usagepolicy');

/* In Progress Skeletons */
Route::view('/collections/search', 'pages/collections');
Route::view('/collections/list', 'pages/collections/list');
Route::view('/occurrence', 'pages/occurrence/profile');
Route::view('/taxon', 'pages/taxon/profile');

Route::get('/checklist/{clid}', function(int $clid) {
    $checklist = DB::table('fmchecklists as c')
        ->select('*')
        ->where('c.clid', '=', $clid)
        ->first();

    return view('pages/checklist/profile', ['checklist' => $checklist]);
});

Route::get('/checklists', function (Request $request){
    $checklists = DB::table('fmchecklists as c')
        ->select('proj.pid', 'c.clid', 'c.name', 'projname', 'mapChecklist')
        ->leftJoin('fmchklstprojlink as link', 'link.clid', '=', 'c.clid')
        ->leftJoin('fmprojects as proj', 'proj.pid', '=', 'link.pid')
        ->orderByRaw('-proj.pid DESC')
        ->get();

    return view('pages/checklists', ['checklists' => $checklists]);
});

Route::get('/project', function (Request $request){
    $project = DB::table('fmprojects')
        ->select('pid', 'projname', 'managers')
        ->where('pid', '=', request('pid'))
        ->first();

    $checklists = DB::table('fmchecklists as c')
        ->select('link.pid', 'c.clid', 'c.name', 'mapChecklist')
        ->leftJoin('fmchklstprojlink as link', 'link.clid', '=', 'c.clid')
        ->where('link.pid', '=', request('pid'))
        ->orderByRaw('-link.pid DESC')
        ->get();

    return view('pages/project', ['project' => $project, 'checklists' => $checklists]);
});

/* Login/out routes */
Route::get('/login', LoginController::class);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/signup', [RegistrationController::class, 'register']);
Route::get('/signup', RegistrationController::class);

Route::get('/media/search', function (Request $request) {
    $media = [];
    $start = $request->query('start') ?? 0;
    if(count($request->all()) > 0) {
        $media = DB::table('media as m')
            ->leftJoin('taxa as t', 't.tid', '=', 'm.tid')
            ->leftJoin('users as u', 'u.uid', '=', 'm.creatoruid')
            ->leftJoin('omoccurrences as o', 'o.occid', '=', 'm.occid')
            ->when($request->query('media_type'), function(Builder $query, $type) {
                $query->where('m.media_type', '=', $type);
            })
            ->when($request->query('tid'), function(Builder $query, $tid) {
                $query->whereIn('t.tid', is_array($tid)? $tid: [$tid]);
            })
            ->when($request->query('taxa'), function(Builder $query, $taxa) {
                $query->whereIn('t.sciName', array_map('trim', explode(',', $taxa)));
            })
            ->when($request->query('uid'), function(Builder $query, $uid) {
                $query->where('u.uid', '=', $uid);
            })
            ->when($request->query('tag'), function(Builder $query, $tag) {
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
        if($request->query('partial')) {
            $query_params = $request->except('partial');
            $query_params['start'] = $start;

            $base_url = $request->header('referer') ?? url()->current();
            $base_url = substr($base_url, 0, strpos('?', $base_url));

            $new_url = $base_url .
                '?' .
                http_build_query($query_params);
            return response(view('media/item', ['media' => $media ]))
                ->header('HX-Replace-URL', $new_url);
        }
    }

    $creators = DB::table('users as u')
        ->join('media as m', 'm.creatoruid', '=', 'u.uid')
        ->select('uid','name')
        ->distinct()
        ->get();

    $tag_options = DB::table('imagetagkey as key')
        ->select('tagkey')
        ->get();

    return view('pages/media/search', ['media' => $media, 'creators' => $creators, 'tags' => $tag_options]);
});

/* Documenation */
Route::get('docs/{path}', MarkdownController::class)->where('path', '.*');
