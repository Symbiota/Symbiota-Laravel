<?php

use App\Http\Controllers\MarkdownController;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\MockObject\Exception;

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

function getPageView(string $page_name, array $view_options = []) {
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

Route::get('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
});

Route::post('/login', function (Request $request) {
    //$lang = Cookie::get('SymbiotaCrumb');
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $result = DB::select('
        SELECT uid
        FROM users
        WHERE (old_password = CONCAT(\'*\', UPPER(SHA1(UNHEX(SHA1(?)))))) AND
        email = ?',
        [$credentials['password'], $credentials['email']]
    );

    //Check old password
    if(count($result) > 0) {
        //Update Hashing to use new has then clear the old password for security
        DB::table('users')
            ->update([
                'password' => Hash::make($credentials['password']),
                // TODO (Logan) offical verison should include this not doing currently so I don't mess up my login on my local db
                //'old_password' => null,
        ]);
    }

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    } else {
        var_dump('failure');
    }


    //$user = new User($request->post('username'), $request->post('password'));
    /*
    $result = DB::select('
        SELECT uid, firstname, username, email
        FROM users
        WHERE (password = CONCAT(\'*\', UPPER(SHA1(UNHEX(SHA1(?)))))) AND
        (username = ? OR email = ?)',
        [$request->post('password'), $request->post('username'), $request->post('username')]
    );


    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $username_or_email = $request->post('username');

    $user = DB::table('users')
        ->select(['uid', 'firstname', 'username', 'email'])
        ->where(function(Builder $query) use ($username_or_email) {
            $query->where('username', '=', $username_or_email)
                ->orWhere('email', '=', $username_or_email);
        })
        ->get();

    var_dump($user);
    $result = Auth::attempt($credentials);
    var_dump($result);
    if($result) {
        $request->session()->regenerate();

        echo "Success fully logged in\n";
    } else {
        echo "Failed to login\n";
    }
/*
    if(count($result) <= 0) {
        //so some error
    } else {
        var_dump($result[0]);
    }
    $db_user = $result[0];


    try {
        Auth::login(new User(
            $db_user->username,
            $db_user->email,
            $request->post('username')
        ));
        var_dump(Auth::user()->currentAccessToken());
    } catch(Exception $e) {
        var_dump($e);
    }
    /*
    if(Auth::attempt($credentials)) {
        return redirect()->intended('core/pages/welcome');
    }
*/
    return getPageView('login');
});

Route::get('/login', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('login', ['lang' => $lang]);
});
// Page -> view -> html
// view -> raw view (good for swaps)
// view -> fragments -> raw view (good for swaps)
// app view -> contains the business
// app layout -> render app view
// nav_link will grab app view and refresh (solve change base on getting app view and updating header info like with login)

Route::post('/signup', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'name' => ['required'],
        'password' => ['required'],
    ]);

    $user_data = $credentials;

    $credentials['password'] = Hash::make($credentials['password']);

    $errors = [];
    try {
        DB::table('laravel_users')->insert($credentials);
        $content = getPageView('login');
        return response($content)
            ->header('HX-Replace-URL', '/')
            ->header('HX-Retarget', '#app-body');
    } catch(Illuminate\Database\UniqueConstraintViolationException $e) {
        array_push($errors, 'Email');
    } catch(\Throwable $e) {
        array_push($errors, $e->getMessage());
    }

    //return getPageView('signup', array_merge(['errors' => $errors], $credentials));
    return view('core/pages/signup', array_merge(['errors' => $errors], $user_data))->fragment('signup-form');
});

Route::get('/signup', function () {
    return getPageView('signup');
});

Route::get('/tw', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return view('tw-components', ['lang' => $lang]);
});


Route::get('/collections/search', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('collections', ['lang' => $lang]);
});

Route::get('/sitemap', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('sitemap', ['lang' => $lang]);
});

Route::get('/usagepolicy', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('usagepolicy', ['lang' => $lang]);
});

Route::get('/login', function () {
    $lang = Cookie::get('SymbiotaCrumb');
    return getPageView('login', ['lang' => $lang]);
});

Route::get('/media/search', function (Request $request) {
    $form_data = $request->validate([
        'usethes' => 'boolean'
    ]);
    $media = [];
    return getPageView('media/search', ['media' => $media ]);
});

Route::get('docs/{path}', MarkdownController::class)->where('path', '.*');
//Route::any('{path}', LegacyController::class)->where('path', '.*');
