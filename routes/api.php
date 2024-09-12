<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/taxa/search', function (Request $request) {
    $sciname = $request->query('taxa');
    $format = strtolower($request->query('format', 'html'));
    $result = DB::select("SELECT sciname, tid FROM taxa WHERE sciname LIKE ? LIMIT 20", ["%" . $sciname . "%" ]);

    if($format === 'json') {
        return $result;
    } else {
        return view(
            'core/autocomplete/result',
            ['data' => $result, 'label' => 'sciname', 'value' => 'tid']
        );
    }
});
