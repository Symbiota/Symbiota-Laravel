<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller {
    use PasswordValidationRules;

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

    public static function updatePassword(Request $request) {
        $user = $request->user();
        $input = $request->input();
        /*
                [
                    'current_password' => request('current_password'),
                    'password' => request('password'),
                ];
        */

        $error = null;

        $validator = Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ]);

        if ($validator->fails()) {
            session()->flashInput($request->input());

            return response(
                view('pages/user/profile', ['errors' => $validator->errors()])
                    ->fragment('password')
            );
        }

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        //return new ProfileInformationUpdatedResponse($request);

        /*
        if(empty($new_password)) {
            $error = 'New Password cannot be empty';
        } else if($confirm_password != $new_password) {
            $error = 'New and Confirm Passwords must be equal';
        }*/

        return response(
            view('pages/user/profile')
                ->fragment('password')
        );
    }
}
