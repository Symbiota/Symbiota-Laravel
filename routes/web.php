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
Route::view('/collections/search', 'pages/collections');
Route::view('/sitemap', 'pages/sitemap');
Route::view('/sitemap', 'pages/sitemap');
Route::view('/usagepolicy', 'pages/usagepolicy');

/* Login/out routes */
Route::get('/login', LoginController::class);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/signup', [RegistrationController::class, 'register']);
Route::get('/signup', RegistrationController::class);

Route::get('/media/search', function (Request $request) {
    $media = [];
    $start = $request->query('start') ?? 0;
    if($request->query('media_type')) {
        $media = DB::table('media')
            ->leftJoin('taxa', 'taxa.tid', '=', 'media.tid')
            ->leftJoin('users', 'users.uid', '=', 'media.creatoruid')
            ->when($request->query('media_type'), function(Builder $query, $type) {
                $query->where('media.media_type', '=', $type);
            })
            ->when($request->query('taxa'), function(Builder $query, $taxa) {
                $query->whereIn('taxa.sciName', array_map('trim', explode(',', $taxa)));
            })
            ->limit(30)
            ->offset($start)
            ->get();
        if($request->query('partial')) {
            $query_params = $request->except('partial');
            $query_params['start'] = $start;
            $new_url = url()->current() .
                '?' .
                http_build_query($query_params);
            return response(view('media/item', ['media' => $media ]))
                ->header('HX-Replace-URL', $new_url);
        }
    }

    return view('pages/media/search', ['media' => $media ]);
});

/* Documenation */
Route::get('docs/{path}', MarkdownController::class)->where('path', '.*');
