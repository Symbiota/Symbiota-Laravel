<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {

    function __invoke() {
        return view('pages/login');
    }

    public static function login(Request $request) {
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

        return view('pages/login');
    }

    public static function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
