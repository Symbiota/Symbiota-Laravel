<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserProfileController extends Controller {
    public static function getProfile(Request $request) {
        $user = $request->user();
        $tokens = $user->tokens;

        return view('pages/user/profile', ['user_tokens' => $tokens, 'user' => $user]);
    }

    public static function updateProfileMetadata(Request $request) {
        $user = $request->user();
        $params = $request->all();

        $params['dynamicProperties'] = $user->dynamicProperties ?? [];
        if ($params['accessibilityPref'] ?? false) {
            $params['dynamicProperties']['accessibilityPref'] = true;
        } else {
            $params['dynamicProperties']['accessibilityPref'] = false;
        }

        $user->update($params);

        return view('pages/user/profile', ['user' => $user])
            ->fragment('profile');
    }

    public static function deleteProfile(Request $request) {
        $request->user()->delete();

        return response(
            view('pages/user/profile')
        )->header('HX-Location', '/');
    }
}
