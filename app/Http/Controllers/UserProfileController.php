<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller {
    public static function getProfile(Request $request) {
        $tokens = $request->user()->tokens;
        return view('pages/user/profile', ['user_tokens' => $tokens]);
    }

    public static function deleteProfile(Request $request) {
        $request->user()->delete();

        return response(
            view('pages/user/profile')
        )->header('HX-Location', '/');
    }
}

