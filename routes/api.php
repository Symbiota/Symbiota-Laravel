<?php

use App\Http\Controllers\CollectionController;
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

Route::get('/', function () {
    return app()->version();
});

Route::get('/v2', function () {
	//return redirect('/v2/documentation');
	return view('/vendor/l5-swagger/index');
});

Route::group(['prefix' => 'v2'], function () {
    Route::get('collection',  [ CollectionController::class, 'showAllCollections' ]);
    Route::get('collection/{id}', [ CollectionController::class ,'showOneCollection' ]);
/*
	$router->get('collection/{id}', ['uses' => 'CollectionController@showOneCollection']);

	$router->get('occurrence/search',  ['uses' => 'OccurrenceController@showAllOccurrences']);
	//Temporarily keep following route until new documentation is created. The one above will be keep so that I follows GBIF API layout
	$router->get('occurrence',  ['uses' => 'OccurrenceController@showAllOccurrences']);
	$router->get('occurrence/{id}', ['uses' => 'OccurrenceController@showOneOccurrence']);
	$router->get('occurrence/{id}/media', ['uses' => 'OccurrenceController@showOneOccurrenceMedia']);
	$router->get('occurrence/{id}/identification', ['uses' => 'OccurrenceController@showOneOccurrenceIdentifications']);
	$router->get('occurrence/{id}/annotation', ['uses' => 'OccurrenceAnnotationController@showOccurrenceAnnotations']);
	$router->get('occurrence/{id}/reharvest', ['uses' => 'OccurrenceController@oneOccurrenceReharvest']);
	$router->get('occurrence/annotation/search', ['uses' => 'OccurrenceAnnotationController@showAllAnnotations']);
	$router->post('occurrence/skeletal', ['uses' => 'OccurrenceController@skeletalImport']);

	$router->get('installation',  ['uses' => 'InstallationController@showAllPortals']);
	$router->get('installation/ping', ['uses' => 'InstallationController@pingPortal']);
	$router->get('installation/{id}', ['uses' => 'InstallationController@showOnePortal']);
	$router->get('installation/{id}/touch',  ['uses' => 'InstallationController@portalHandshake']);
	$router->get('installation/{id}/occurrence',  ['uses' => 'InstallationController@showOccurrences']);

	$router->get('inventory',  ['uses' => 'InventoryController@showAllInventories']);
	$router->get('inventory/{id}', ['uses' => 'InventoryController@showOneInventory']);
	$router->get('inventory/{id}/taxa', ['uses' => 'InventoryController@showOneInventoryTaxa']);
	$router->get('inventory/{id}/package', ['uses' => 'InventoryPackageController@oneInventoryDataPackage']);

	$router->get('media',  ['uses' => 'MediaController@showAllMedia']);
	$router->get('media/{id}', ['uses' => 'MediaController@showOneMedia']);
	$router->post('media', ['uses' => 'MediaController@insert']);
	$router->patch('media/{id}', ['uses' => 'MediaController@update']);
	$router->delete('media/{id}', ['uses' => 'MediaController@delete']);

	$router->get('taxonomy', ['uses' => 'TaxonomyController@showAllTaxa']);
	$router->get('taxonomy/search', ['uses' => 'TaxonomyController@showAllTaxaSearch']);
	$router->get('taxonomy/{id}', ['uses' => 'TaxonomyController@showOneTaxon']);
	//$router->get('taxonomy/{id}/description',  ['uses' => 'TaxonomyController@showAllDescriptions']);
	//$router->get('taxonomy/{id}/description/{id}',  ['uses' => 'TaxonomyDescriptionController@showOneDescription']);
    */
});
