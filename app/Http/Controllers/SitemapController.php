<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller {
    public function __invoke() {

        $schema_version = DB::table('schemaversion')
            ->selectRaw('versionnumber')
            ->orderBy('dateapplied', 'DESC')
            ->first();

        return view('pages/sitemap', [
            'schema_version' => $schema_version->versionnumber ?? false,
            //TODO (Logan) only ones with perms
            'projects' => DB::table('fmprojects')->get(),
            //TODO (Logan) only ones with perms
            'collections' => Collection::query()->orderBy('collectionName')->get(),
            //TODO (Logan) only ones with perms
            'checklists' => DB::table('fmchecklists')->get(),
        ]);
    }
}
