<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SitemapController extends Controller {
    public function __invoke() {

        $schema_version = DB::table('schemaversion')
            ->selectRaw('versionnumber')
            ->orderBy('dateapplied', 'DESC')
            ->first();

        $user = request()->user();

        return view('pages/sitemap', [
            'schema_version' => $schema_version->versionnumber ?? false,
            'user' => $user,
            'projects' => DB::table('fmprojects')->get(),
            'user_collections' => $user ? $user->collections() : [],
            'user_checklists' => $user ? $user->checklists() : [],
        ]);
    }
}
