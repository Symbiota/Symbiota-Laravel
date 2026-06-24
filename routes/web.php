<?php

use App\Http\Controllers\ChecklistAdminController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionTraitController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ExsiccataController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarkdownController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OccurrenceCommentController;
use App\Http\Controllers\OccurrenceController;
use App\Http\Controllers\OccurrenceEditorController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\UserPermissonsController;
use App\Http\Controllers\UserProfileController;
use App\Models\User;
use App\Models\UserRole;
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
    Route::get('/', [TaxonomyController::class, 'show']);
    Route::get('/create', [TaxonomyController::class, 'createTaxon'])->name('taxon.createview')->middleware('auth'); // Note that I think we ought to name more routes to make them easier to change
    Route::post('/store', [TaxonomyController::class, 'store'])->name('taxon.store'); // @TODO gate
    Route::post('/update', [TaxonomyController::class, 'update'])->name('taxon.update'); // @TODO gate
    Route::get('/{tid}', [TaxonomyController::class, 'taxon'])->name('taxon.view');
    Route::get('/{tid}/profileEdit', [TaxonomyController::class, 'editTaxonProfile'])->name('taxon.profileEdit')->middleware('auth'); // @TODO gate
    Route::get('/{tid}/edit', [TaxonomyController::class, 'editTaxon'])->name('taxon.editview')->middleware('auth'); // @TODO gate
    Route::delete('/delete', [TaxonomyController::class, 'delete'])->name('taxon.delete')->middleware('auth'); // @TODO gate
    Route::post('/remap', [TaxonomyController::class, 'remap'])->name('taxon.remap')->middleware('auth'); // @TODO gate
});

/*
|--------------------------------------------------------------------------
| Checklists Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'checklists'], function () {
    Route::controller(ChecklistController::class)->group(function () {
        Route::get('/', 'checklists');
        Route::get('/map', 'mapPage');
        Route::get('/dynamicmap', 'dynamicMapPage');
        Route::post('/dynamicmap', 'buildDynChecklist');
        Route::get('/{clid}/pdf', 'browserPrint')->whereNumber('clid');
        Route::match(['GET', 'POST'], '/{clid}', 'checklist')->whereNumber('clid');
        Route::post('/create', 'createChecklist');
    });

    Route::view('/{clid}/key', 'pages/checklist/key');

    Route::controller(ChecklistAdminController::class)
        ->prefix('/{clid}/')
        ->middleware('can:CL_ADMIN,clid')
        ->whereNumber(['clid'])
        ->group(function () {
            Route::get('admin', 'getAdminPage');
            Route::post('admin', 'getAdminPage');
            Route::post('editor/{uid}', 'addEditor')->whereNumber('uid');
            Route::post('project/{pid}', 'addProject')->whereNumber('pid');
            Route::delete('/', 'delete');
        });
});

/*
|--------------------------------------------------------------------------
| Datasets Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'datasets'], function () {
    Route::get('/rss', [RssController::class, 'show']);
    Route::get('/{dataset_id}', [DatasetController::class, 'datasetProfilePage']);
    Route::view('/', 'pages/datasets/list');
});

/*
|--------------------------------------------------------------------------
| Project Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/projects'], function () {
    Route::get('/', [ProjectController::class, 'publicProjects'])->where('pid', '[0-9]+');
    Route::get('/{pid}', [ProjectController::class, 'project'])->where('pid', '[0-9]+')->can('PROJ_VIEW', 'pid');

    /* Admin Routes */
    Route::get('/create', [ProjectController::class, 'projectCreate'])->can('SUPER_ADMIN');
    Route::post('/create', [ProjectController::class, 'create'])->can('SUPER_ADMIN');

    Route::post('/{pid}/edit', [ProjectController::class, 'update'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');
    Route::delete('/{pid}/edit', [ProjectController::class, 'delete'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');
    Route::post('/{pid}/managers', [ProjectController::class, 'addUser'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');
    Route::delete('/{pid}/managers/{uid}', [ProjectController::class, 'removeUser'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');

    Route::post('/{pid}/checklists', [ProjectController::class, 'addChecklist'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');
    Route::delete('/{pid}/checklists/{clid}', [ProjectController::class, 'removeChecklist'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');

    Route::get('/{pid}/edit', [ProjectController::class, 'projectAdminView'])->where('pid', '[0-9]+')->can('PROJ_ADMIN', 'pid');
});

/*
|--------------------------------------------------------------------------
| Occurrence Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/occurrence'], function () {
    Route::get('/{occid}', [OccurrenceController::class, 'profilePage']);
    Route::get('/{occid}/edit', [OccurrenceEditorController::class, 'editPage']);
    /* Linked Resources */
    Route::put('/{occid}/link/checklist', [OccurrenceController::class, 'linkChecklist']);
    Route::put('/{occid}/link/dataset', [OccurrenceController::class, 'linkDataset']);

    /* Comments */
    Route::controller(OccurrenceCommentController::class)->prefix('/{occid}/comment')->group(function () {
        Route::post('/', 'post');
        Route::delete('/{comid}', 'delete');
        Route::patch('/{comid}/report', 'report');
        Route::patch('/{comid}/public', 'public');
    });
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
    Route::view('/', 'pages/collections/profile-list');
    Route::get('/search', [CollectionController::class, 'searchPage']);
    Route::get('/publisher', [CollectionController::class, 'publisherPage']);
    Route::get('/map/search', [CollectionController::class, 'mapSearchPage']);

    Route::get('/download/file', [DownloadController::class, 'downloadFile']);
    Route::get('/download', [DownloadController::class, 'downloadPage']);

    Route::get('/table', [CollectionController::class, 'tablePage']);
    Route::get('/list', [CollectionController::class, 'listPage']);
    Route::get('/{collid}/import', [CollectionController::class, 'importPage']);
    Route::patch('/{collid}/stats', [CollectionController::class, 'updateStats']);
    Route::get('/{collid}/skeletal', [CollectionController::class, 'skeletalView'])->can('COLL_EDIT', 'collid');
    Route::post('/{collid}/skeletal', [CollectionController::class, 'skeletalAdd'])->can('COLL_EDIT', 'collid');
    Route::get('/{collid}', [CollectionController::class, 'collection']);
    Route::match(['GET', 'POST'], '/{collid}/comments', [CollectionController::class, 'comments'])->can('COLL_GENERAL_OBSERVATION_ADMIN', 'collid')->whereNumber('collid');

    Route::controller(CollectionTraitController::class)->group(function () {
        Route::get('/{collid}/traits/edit', 'editor')->can('COLL_EDIT', 'collid');
        Route::post('/{collid}/traits/edit', 'getImages')->can('COLL_EDIT', 'collid');
        Route::patch('/{collid}/traits/edit', 'save')->can('COLL_EDIT', 'collid');
    });
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
    Route::get('/{media_id}', [MediaController::class, 'profilePage']);
    Route::post('/{media_id}/transfer/taxa', [MediaController::class, 'profileTaxaTransfer']);
    Route::post('/{media_id}', [MediaController::class, 'profileUpdate']);
    Route::delete('/{media_id}', [MediaController::class, 'profileDelete']);
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

    Route::controller(UserPermissonsController::class)->middleware('can:SUPER_ADMIN')->group(function () {
        Route::get('/permissions', 'adminSearchPage')->name('user.management');

        if (app()->environment('local')) {
            Route::get('/loginas/{uid}', 'loginAs')->name('user.admin.login.as');
        }

        Route::get('/create', 'adminUserRegister')
            ->whereNumber('uid')
            ->name('user.admin.create');
        Route::post('/create', 'adminCreateUser')
            ->whereNumber('uid')
            ->name('user.admin.create');

        Route::get('/{uid}/permissions', 'permissionsProfile')
            ->whereNumber('uid')
            ->name('user.permissions');
        Route::put('/{uid}/permissions', 'updatePermissions')
            ->whereNumber('uid')
            ->name('user.permissions.update');
        Route::delete('/{uid}/permissions/{role}', 'deletePermisson')
            ->whereNumber('uid')
            ->whereIn('role', UserRole::roles())
            ->name('user.permissions.delete');
        Route::post('/{uid}/permissions', 'addPermission')
            ->whereNumber(['uid'])
            ->name('user.permissions.add');
    });
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
| Exsiccata Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/exsiccata'], function () {

    Route::get('/', [ExsiccataController::class, 'index'])->name('exsiccata.index');
    Route::post('/', [ExsiccataController::class, 'store'])
        ->name('exsiccata.store')
        ->can('EXSICCATAE_ADMIN');

    Route::get('/{ometid}', [ExsiccataController::class, 'title'])
        ->name('exsiccata.title')
        ->whereNumber('ometid');
    Route::post('/{ometid}', [ExsiccataController::class, 'storeTitle'])
        ->name('exsiccata.title.store')
        ->whereNumber('ometid')
        ->can('EXSICCATAE_ADMIN');
    Route::get('/{ometid}/{omenid}', [ExsiccataController::class, 'number'])
        ->name('exsiccata.number')
        ->whereNumber('ometid')
        ->whereNumber('omenid');
    Route::post('/{ometid}/{omenid}', [ExsiccataController::class, 'storeNumber'])
        ->name('exsiccata.number.store')
        ->whereNumber('ometid')
        ->whereNumber('omenid')
        ->can('EXSICCATAE_ADMIN');
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

        $user = User::updateOrCreate(
            [
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
            ]
        );

        Auth::login($user);

        return redirect('/');
    });
});

/* Documenation */
Route::get('docs/{path}', MarkdownController::class)->where('path', '.*');
