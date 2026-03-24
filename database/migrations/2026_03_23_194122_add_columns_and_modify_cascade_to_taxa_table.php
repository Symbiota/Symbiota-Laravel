<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('taxa', function (Blueprint $table) {
            $table->dropForeign('FK_taxa_uid');
        });

        // Clean up any existing taxa records with invalid modifiedUid values
        DB::statement('UPDATE taxa SET modifiedUid = NULL WHERE modifiedUid IS NOT NULL AND modifiedUid NOT IN (SELECT uid FROM users)');

        Schema::table('taxa', function (Blueprint $table) {
            $table->addColumn('string', 'cultivarEpithet', ['length' => 50, 'nullable' => true]);
            $table->addColumn('string', 'tradeName', ['length' => 50, 'nullable' => true]);

            $table->foreign(['modifiedUid'], 'FK_taxa_uid')->references(['uid'])->on('users')->onUpdate('cascade')->onDelete('set null'); // assuming we want to remove the references when we delete a user?
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('taxa', function (Blueprint $table) {
            $table->dropForeign('FK_taxa_uid');
            $table->dropColumn(['cultivarEpithet', 'tradeName']);
            $table->foreign(['modifiedUid'], 'FK_taxa_uid')->references(['uid'])->on('users')->onUpdate('restrict')->onDelete('no action');
        });
    }
};
