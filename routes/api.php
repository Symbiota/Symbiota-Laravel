<?php

use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryPackageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OccurrenceAnnotationController;
use App\Http\Controllers\OccurrenceController;
use App\Http\Controllers\TaxonomyController;
use App\Models\Occurrence;
use App\Models\Taxonomy;
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

/**
 * @OA\Info(title="My First API", version="0.1")
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/taxa/search', function (Request $request) {
    $sciname = $request->query('taxa');
    $format = strtolower($request->query('format', 'html'));
    $result = DB::select('SELECT sciname, tid FROM taxa WHERE sciname LIKE ? ORDER BY sciname = ? DESC, sciname LIKE ? DESC, sciname LIMIT 20', ['%' . $sciname . '%', $sciname, $sciname . '%']);

    if ($format === 'json') {
        return $result;
    } else {
        return view(
            'core/autocomplete/result',
            ['data' => $result, 'label' => 'sciname', 'value' => 'tid']
        );
    }
});

Route::get('/geographic/search', function (Request $request) {
    $geo_term = $request->query('geoterm');
    $geo_level = $request->query('geolevel');

    $parent = $request->query('parent');
    $distinct = $request->query('distinct');
    $format = strtolower($request->query('format', 'html'));

    $query = DB::table('geographicthesaurus as g')
        ->leftJoin('geographicthesaurus as parent', 'parent.geoThesID', 'g.parentID')
        ->whereLike('g.geoterm', '%' . $geo_term . '%');

    if ($geo_level) {
        $query->where('g.geolevel', '=', $geo_level);
    }

    if ($parent) {
        $query->whereLike('parent.geoterm', '%' . $parent . '%');
    }

    if ($distinct) {
        $query->groupBy('g.geoterm');
    }

    $result = $query
        ->orderByRaw('g.geoterm = ? DESC, g.geoterm LIKE ? DESC, g.geoterm, CHAR_LENGTH(g.geoterm)', [$geo_term, $geo_term . '%'])
        ->select([
        'g.geoThesID', 'g.geoterm', 'g.geoLevel', 'g.parentID',
        'parent.geoterm AS parentterm', 'parent.geoLevel AS parentlevel',
    ])->take(30)->get();

    if ($format === 'json') {
        return $result;
    } else {
        return view(
            'core/autocomplete/result',
            ['data' => $result, 'label' => 'geoterm', 'value' => 'geoterm']
        );
    }
});

Route::get('/', function () {
    return app()->version();
});

Route::group(['prefix' => 'v3'], function () {
    /*
    |--------------------------------------------------------------------------
    | Occurrence API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'occurrence'], function () {
        Route::get('search', function (Request $request) {
            $record_limit = $request->query('limit') > 1000 ? 1000 : $request->query('limit');

            $query = Occurrence::buildSelectQuery($request->all());

            return $query->select('*')->limit(100)->get();
        });
        Route::get('{id}', function (int $occid) {
            $query = Occurrence::buildSelectQuery(['occid' => $occid]);

            return $query->select('*')->first();
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Token API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'tokens'], function () {
        Route::post('create', function (Request $request) {
            $token = $request->user()->createToken($request->token_name);

            return ['token' => $token->plainTextToken];
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Collections API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'collection'], function () {});

    /*
    |--------------------------------------------------------------------------
    | Checklist API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'checklists'], function () {
        Route::get('/', [ChecklistController::class, 'getChecklistsData']);
        Route::get('/{clid}', [ChecklistController::class, 'getChecklistData']);
    });

    /*
    |--------------------------------------------------------------------------
    | Inventory API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'inventory'], function () {});

    /*
    |--------------------------------------------------------------------------
    | Installation API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'installation'], function () {});

    /*
    |--------------------------------------------------------------------------
    | Media API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'media'], function () {});

    /*
    |--------------------------------------------------------------------------
    | Taxonomy API
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'taxonomy'], function () {
        Route::get('{tid}/children', [TaxonomyController::class, 'getDirectChildren']);
    });
});

/*
Route::group(['prefix' => 'v2'], function () {
    Route::get('collection', [CollectionController::class, 'showAllCollections']);
    Route::get('collection/{id}', [CollectionController::class, 'showOneCollection']);

    Route::get('occurrence/search', [OccurrenceController::class, 'showAllOccurrences']);

    //Temporarily keep following route until new documentation is created. The one above will be keep so that I follows GBIF API layout
    Route::get('occurrence', [OccurrenceController::class, 'showAllOccurrences']);
    Route::get('occurrence/{id}', [OccurrenceController::class, 'showOneOccurrence']);
    Route::get('occurrence/{id}/media', [OccurrenceController::class, 'showOneOccurrenceMedia']);
    Route::get('occurrence/{id}/identification', [OccurrenceController::class, 'showOneOccurrenceIdentifications']);
    Route::get('occurrence/{id}/annotation', [OccurrenceAnnotationController::class, 'showOccurrenceAnnotations']);
    Route::get('occurrence/{id}/reharvest', [OccurrenceController::class, 'oneOccurrenceReharvest']);
    Route::get('occurrence/annotation/search', [OccurrenceAnnotationController::class, 'showAllAnnotations']);
    Route::post('occurrence/skeletal', [OccurrenceController::class, 'skeletalImport']);
    Route::get('installation', [InstallationController::class, 'showAllPortals']);
    Route::get('installation/ping', [InstallationController::class, 'pingPortal']);
    Route::get('installation/{id}', [InstallationController::class, 'showOnePortal']);
    Route::get('installation/{id}/touch', [InstallationController::class, 'portalHandshake']);
    Route::get('installation/{id}/occurrence', [InstallationController::class, 'showOccurrences']);
    Route::get('inventory', [InventoryController::class, 'showAllInventories']);
    Route::get('inventory/{id}', [InventoryController::class, 'showOneInventory']);
    Route::get('inventory/{id}/taxa', [InventoryController::class, 'showOneInventoryTaxa']);
    Route::get('inventory/{id}/package', [InventoryPackageController::class, 'oneInventoryDataPackage']);
    Route::get('media', [MediaController::class, 'showAllMedia']);
    Route::get('media/{id}', [MediaController::class, 'showOneMedia']);
    Route::get('media/{id}', [MediaController::class, 'showOneMedia']);
    Route::post('media', [MediaController::class, 'insert']);
    Route::patch('media/{id}', [MediaController::class, 'update']);
    Route::patch('media/{id}', [MediaController::class, 'update']);
    Route::delete('media/{id}', [MediaController::class, 'delete']);
    Route::delete('media/{id}', [MediaController::class, 'delete']);
    Route::get('taxonomy', [TaxonomyController::class, 'showAllTaxa']);
    Route::get('taxonomy/search', [TaxonomyController::class, 'showAllTaxaSearch']);
    Route::get('taxonomy/{id}', [TaxonomyController::class, 'showOneTaxon']);

    //Route::get('taxonomy/{id}/description',  [TaxonomyController::class, 'showAllDescriptions']);
    //Route::get('taxonomy/{id}/description/{id}',  [TaxonomyDescriptionController::class, 'showOneDescription']);
});
*/
