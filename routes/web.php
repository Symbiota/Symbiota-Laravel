<?php

use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarkdownController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OccurrenceController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\UserProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

/*
|--------------------------------------------------------------------------
| General Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'pages/home')->name('home');
Route::view('Portal/', 'pages/home');
Route::view('/tw', 'tw-components');
Route::get('/sitemap', SitemapController::class);
Route::view('/usagepolicy', 'pages/usagepolicy');

/*
|--------------------------------------------------------------------------
| Taxon Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'taxon'], function () {
    Route::get('/{tid}', [TaxonomyController::class, 'taxon']);
    Route::get('/{tid}/edit', [TaxonomyController::class, 'taxonEdit']);
});

/*
|--------------------------------------------------------------------------
| Checklists Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'checklists'], function () {
    Route::get('/', [ChecklistController::class, 'checklists']);
    Route::get('/dynamicmap', [ChecklistController::class, 'dynamicMapPage']);
    Route::get('/map', [ChecklistController::class, 'mapPage']);
    Route::post('/create', [ChecklistController::class, 'createChecklist']);
    Route::get('/{clid}/admin', [ChecklistController::class, 'getAdminPage']);
    Route::get('/{clid}', [ChecklistController::class, 'checklist']);
});

/*
|--------------------------------------------------------------------------
| Datasets Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'datasets'], function () {
    Route::get('/{dataset_id}', [DatasetController::class, 'datasetProfilePage']);
    Route::view('/', 'pages/datasets/list');
});

/*
|--------------------------------------------------------------------------
| Project Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/projects'], function () {
    Route::get('/{pid}', [ProjectController::class, 'project']);
    Route::get('/{pid}/edit', [ProjectController::class, 'editProject']);
});

/*
|--------------------------------------------------------------------------
| Occurrence Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/occurrence'], function () {
    Route::get('/{occid}', [OccurrenceController::class, 'profilePage']);
    Route::post('/{occid}/comment', [OccurrenceController::class, 'postComment']);
    Route::put('/{occid}/link/checklist', [OccurrenceController::class, 'linkChecklist']);
    Route::put('/{occid}/link/dataset', [OccurrenceController::class, 'linkDataset']);
    Route::delete('/{occid}/comment/{comid}', [OccurrenceController::class, 'deleteComment']);
    Route::patch('/{occid}/comment/{comid}/report', [OccurrenceController::class, 'reportComment']);
    Route::patch('/{occid}/comment/{comid}/public', [OccurrenceController::class, 'publicComment']);
    Route::get('/{occid}/edit', [OccurrenceController::class, 'editPage']);
});

/*
|--------------------------------------------------------------------------
| Tools Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/tools'], function () {

    Route::group(['prefix' => '/map'], function () {
        Route::view('/coordaid', 'pages/tools/map/coordaid');
        Route::view('/pointaid', 'pages/tools/map/pointaid');
    });
});

/*
|--------------------------------------------------------------------------
| Collections Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/collections'], function () {
    Route::get('/', [CollectionController::class, 'profileList']);
    Route::get('/search', [CollectionController::class, 'searchPage']);
    Route::get('/publisher', [CollectionController::class, 'publisherPage']);
    Route::get('/map/search', [CollectionController::class, 'mapSearchPage']);

    Route::get('/download/file', [DownloadController::class, 'downloadFile']);
    Route::get('/download', [DownloadController::class, 'downloadPage']);

    Route::get('/table', [CollectionController::class, 'tablePage']);
    Route::get('/list', [CollectionController::class, 'listPage']);
    Route::get('/{collid}/import', [CollectionController::class, 'importPage']);
    Route::get('/{collid}', [CollectionController::class, 'collection']);
});

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/media'], function () {
    Route::get('/search', [MediaController::class, 'searchPage']);
    Route::get('/library', [MediaController::class, 'libraryPage']);
    Route::get('/contributors', [MediaController::class, 'contributorsPage']);
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/user'], function () {
    Route::get('/profile', [UserProfileController::class, 'getProfile']);
    Route::put('/profile/metadata', [UserProfileController::class, 'updateProfileMetadata']);
    Route::post('/profile/password', [UserProfileController::class, 'updatePassword']);
    Route::post('/profile/dataset', [DatasetController::class, 'createDataset']);
    Route::delete('/profile', [UserProfileController::class, 'deleteProfile']);
});

/*
|--------------------------------------------------------------------------
| User Token Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/token'], function () {
    Route::post('/create', [PersonalAccessTokenController::class, 'create']);
    Route::delete('/delete/{id}', [PersonalAccessTokenController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/logout', [LoginController::class, 'logout']);
Route::group(['prefix' => '/auth'], function () {
    Route::get('/redirect', function (Request $request) {
        return Socialite::driver('orcid')->redirect();
    });
    Route::get('/oauth/orcid', function () {
        $orcid_user = Socialite::driver('orcid')->user();

        $user = User::updateOrCreate([
            'guid' => $orcid_user->id,
            'oauth_provider' => 'orcid',
        ],
            [
                'name' => $orcid_user->name,
                'firstName' => $orcid_user->attributes['firstName'],
                'lastName' => $orcid_user->attributes['lastName'],
                'email' => $orcid_user->email ?? null,
                //'guid' => $orcid_user->id,
                'access_token' => $orcid_user->token,
                'refresh_token' => $orcid_user->refreshToken,
            ]);

        Auth::login($user);

        return redirect('/');
    });
});

/* Documenation */
Route::get('docs/{path}', MarkdownController::class)->where('path', '.*');

function includeLegacy($path) {
    ob_start();

    $normalizePath = strpos($path, getenv('PORTAL_NAME')) === 0?
        base_path($path):
        legacy_path($path);

    include($normalizePath);

    $output = ob_get_clean();
    return response($output);
}

Route::get('js/{path}', function(Request $request) {
    $path = $request->path();
    $pathInfo = pathinfo($path);
    $response = includeLegacy($path);

    if($pathInfo['extension'] === 'js') {
        $response->header('content-type', 'text/javascript');
    } else if($pathInfo['extension'] === 'css') {
        $response->header('content-type', 'text/css');
    }
    return $response;
})->where('path', '.*');

Route::get('css/{path}', function(Request $request) {
    $path = $request->path();
    return includeLegacy($path)->header('content-type', 'text/css');
})->where('path', '.*');

Route::get('{path}', function(Request $request) {
    global
    // Symbini Variables
    $DEFAULT_LANG,
    $DEFAULT_PROJ_ID,
    $DEFAULTCATID,
    $CLIENT_ROOT,
    $DEFAULT_TITLE,
    $EXTENDED_LANG,
    $TID_FOCUS,
    $ADMIN_EMAIL,
    $SYSTEM_EMAIL,
    $CHARSET,
    $PORTAL_GUID,
    $SECURITY_KEY,
    $SERVER_HOST,
    $CLIENT_ROOT,
    $SERVER_ROOT,
    $TEMP_DIR_ROOT,
    $LOG_PATH,
    $CSS_BASE_PATH,
    $PUBLIC_MEDIA_UPLOAD_ROOT,
    $PUBLIC_IMAGE_UPLOAD_ROOT,
    $MEDIA_DOMAIN,
    $IMAGE_DOMAIN,
    $MEDIA_ROOT_URL,
    $IMAGE_ROOT_URL,
    $MEDIA_ROOT_PATH,
    $IMAGE_ROOT_PATH,
    $IMG_WEB_WIDTH,
    $IMG_TN_WIDTH,
    $IMG_LG_WIDTH,
    $MEDIA_FILE_SIZE_LIMIT,
    $IMG_FILE_SIZE_LIMIT,
    $IPLANT_IMAGE_IMPORT_PATH,
    $TESSERACT_PATH,
    $NLP_LBCC_ACTIVATED,
    $NLP_SALIX_ACTIVATED,
    $OCCURRENCE_MOD_IS_ACTIVE,
    $FLORA_MOD_IS_ACTIVE,
    $KEY_MOD_IS_ACTIVE,
    $GBIF_USERNAME,
    $GBIF_PASSWORD,
    $GBIF_ORG_KEY,
    $DEFAULT_TAXON_SEARCH,
    $GOOGLE_MAP_KEY,
    $MAPBOX_API_KEY,
    $MAP_THUMBNAILS,
    $STORE_STATISTICS,
    $MAPPING_BOUNDARIES,
    $ACTIVATE_GEOLOCATION,
    $GOOGLE_ANALYTICS_KEY,
    $GOOGLE_ANALYTICS_KEY,
    $RECAPTCHA_PUBLIC_KEY,
    $RECAPTCHA_PRIVATE_KEY,
    $TAXONOMIC_AUTHORITIES,
    $QUICK_HOST_ENTRY_IS_ACTIVE,
    $GLOSSARY_EXPORT_BANNER,
    $DYN_CHECKLIST_RADIUS,
    $DISPLAY_COMMON_NAMES,
    $ACTIVATE_DUPLICATES,
    $ACTIVATE_EXSICCATI,
    $ACTIVATE_GEOLOCATE_TOOLKIT,
    $SEARCH_BY_TRAITS,
    $CALENDAR_TRAIT_PLOTS,
    $ACTIVATE_PALEO,
    $IGSN_ACTIVATION,
    $WIKIPEDIA_TAXON_TAB,
    $OVERRIDE_DOWNLOAD_LOGIN_REQUIREMENT,
    $ALLOWEDCHARACTERS,
    $RIGHTS_TERMS,
    $SHOULD_BE_ABLE_TO_CREATE_PUBLIC_USER,
    $SYMBIOTA_LOGIN_ENABLED,
    $SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT,
    $AUTH_PROVIDER,
    $LOGIN_ACTION_PAGE,
    $SHOULD_USE_HARVESTPARAMS,
    $THIRD_PARTY_OID_AUTH_ENABLED,
    $SHOULD_USE_MINIMAL_MAP_HEADER,
    $DATE_DEFAULT_TIMEZONE,
    $PRIVATE_VIEWING_ONLY,
    $PRIVATE_VIEWING_OVERRIDES,
    $COOKIE_SECURE,
    $GEO_JSON_LAYERS,

    // Symbbase Variables
    $CODE_VERSION,
    $PARAMS_ARR,
    $USER_RIGHTS,
    $EXTERNAL_HOSTS,
    $USER_DISPLAY_NAME,
    $USERNAME,
    $SYMB_UID,
    $IS_ADMIN,
    $PORTAL_PRIVATE,
    $ACCESSIBILITY_ACTIVE,
    $AVAILABLE_LANGS,
    $LANG_TAG, $LANG,
    $CSS_VERSION,
    $ALLOWED_MEDIA_MIME_TYPES,
    $MIME_FALL_BACK;

    $path = $request->path();

    ob_start();
    if(!isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['QUERY_STRING'] = '';
    }

    if(strpos($path, getenv('PORTAL_NAME')) === 0) {
        include(base_path($path));
    } else {
        include legacy_path($path);
    }

    $output = ob_get_clean();

    return response($output);
})->where('path', '.*');
