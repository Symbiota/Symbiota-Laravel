<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    /**
     * @OA\Get(
     *     path="/login",
     *
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function __invoke() {
        return response(view('pages/login'))->header('HX-Replace-URL', '/login');
    }

    public static function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
