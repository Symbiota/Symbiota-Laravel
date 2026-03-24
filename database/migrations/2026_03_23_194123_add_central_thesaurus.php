<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        DB::table('taxauthority')->insertOrIgnore([
            'taxauthid' => 1,
            'isprimary' => 1,
            'name' => 'Central Thesaurus',
            'description' => null,
            'editors' => null,
            'contact' => null,
            'email' => null,
            'url' => null,
            'notes' => null,
            'isactive' => 1,
            'initialtimestamp' => '2026-03-23 20:45:32',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::table('taxauthority')->where('taxauthid', 1)->delete();
    }
};
