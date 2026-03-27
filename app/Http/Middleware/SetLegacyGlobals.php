<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLegacyGlobals {
    /**
     * Set legacy portal globals from the authenticated Laravel session so that Portal submodule classes (e.g. TaxonomyEditorManager) can read the current user's UID via $GLOBALS['SYMB_UID'].
     */
    public function handle(Request $request, Closure $next): Response {
        $user = Auth::user();
        if ($user) {
            $GLOBALS['SYMB_UID'] = $user->uid;
        }

        return $next($request);
    }
}
