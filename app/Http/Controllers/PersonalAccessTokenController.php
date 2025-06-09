<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nette\Utils\DateTime;

class PersonalAccessTokenController extends Controller {
    public function create(Request $request): string {
        $user = $request->user();

        $token = $user->createToken($request->token_name, ['*'], $request->expiration_date ? new DateTime($request->expiration_date) : null);

        return view(
            'pages/user/profile',
            [
                'user_tokens' => $user->tokens ?? [],
                'created_token' => $token->plainTextToken,
            ])
            ->fragment('tokens');
    }

    public function delete(int $token_id): string {
        $user = request()->user();
        $token = $user->tokens()->where('id', $token_id)->delete();

        return view(
            'pages/user/profile',
            ['user_tokens' => $user->tokens ?? []])
            ->fragment('tokens');
    }
}
