<?php

use App\Http\Controllers\LegacyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarkdownController;
use App\Http\Controllers\RegistrationController;
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
    if($request->query('media_type')) {
        $media = DB::select('Select * from media where tid = 58358 and media_type = "image" LIMIT 100');

        return Blade::render('
            @foreach ($media as $item)
            <div>
            <img class="max-h-72" src="{{$item->thumbnailUrl}}" alt="Image not found"/>
            {{$item->tid}}
            </div>
            @endforeach
            ', ['media' => $media ]);
    }

    return view('pages/media/search', ['media' => $media ]);
});

/* Documenation */
Route::get('docs/{path}', MarkdownController::class)->where('path', '.*');
