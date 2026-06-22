<?php

namespace App\Helpers;

use Illuminate\Http\RedirectResponse;

class RedirectResponseHelper {
    public static function backWithError(string $error): RedirectResponse {
        return redirect()->back()->withInput()->withErrors(['error' => $error]);
    }

    public static function routeWithError(string $route, string $error): RedirectResponse {
        return redirect()->route($route)->withErrors(['error' => $error]);
    }
}