<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
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

function getPageView(string $page_name, array $view_options) {
    if(file_exists(base_path('resources/views/custom/pages/') . $page_name .'.blade.php')) {
        return view('custom/pages/' . $page_name, $view_options);
    } else {
        return view('core/pages/' . $page_name, $view_options);
    }
}

Route::get('/', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('home', ['lang' => $lang]);
});

Route::get('/login', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('login', ['lang' => $lang]);
});

Route::get('/tw', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return view('tw-components', ['lang' => $lang]);
});

Route::get('/collections/search', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    //return view('collections/collections', ['lang' => $lang, 'specArr' => []]);
    global $LANG_TAG;
    global $SERVER_ROOT;
    global $CLIENT_ROOT;
    global $USER_DISPLAY_NAME;
    $USER_DISPLAY_NAME = "why no work";
    $LANG_TAG = App::currentLocale();
    $SERVER_ROOT = base_path('public/' . config('portal.name'));
    $CLIENT_ROOT = config('portal.name');

    return getPageView('collections', ['lang' => $lang]);
});
