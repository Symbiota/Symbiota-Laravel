<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller {
    function __invoke() { return view('pages/signup'); }

    public static function register(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required'],
            'password' => ['required'],
        ]);

        $user_data = $credentials;

        $credentials['password'] = Hash::make($credentials['password']);

        $errors = [];
        try {
            $name_parts = explode(" ", $credentials['name'], 2);

            if(count($name_parts) >= 1) $credentials['firstName'] = $name_parts[0];
            if(count($name_parts) >= 2) $credentials['lastName'] = $name_parts[1];

            DB::table('users')->insert($credentials);
            $content = view('pages/login');
            return response($content)
                ->header('HX-Replace-URL', '/')
                ->header('HX-Retarget', '#app-body');
        } catch(UniqueConstraintViolationException $e) {
            array_push($errors, 'Email');
        } catch(\Throwable $e) {
            array_push($errors, $e->getMessage());
        }

        return view('pages/signup', array_merge(['errors' => $errors], $user_data))->fragment('signup-form');
    }
}
