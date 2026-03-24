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
            // Drop the existing problematic foreign key constraint
            $table->dropForeign('FK_taxa_uid');
        });

        // Clean up any existing taxa records with invalid modifiedUid values
        DB::statement('UPDATE taxa SET modifiedUid = NULL WHERE modifiedUid IS NOT NULL AND modifiedUid NOT IN (SELECT uid FROM users)');

        Schema::table('taxa', function (Blueprint $table) {
            // Add the new columns
            $table->addColumn('string', 'cultivarEpithet', ['length' => 50, 'nullable' => true]);
            $table->addColumn('string', 'tradeName', ['length' => 50, 'nullable' => true]);

            // Recreate the foreign key with proper handling
            $table->foreign(['modifiedUid'], 'FK_taxa_uid')->references(['uid'])->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('taxa', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign('FK_taxa_uid');

            // Drop the new columns
            $table->dropColumn(['cultivarEpithet', 'tradeName']);

            // Recreate the original foreign key constraint
            $table->foreign(['modifiedUid'], 'FK_taxa_uid')->references(['uid'])->on('users')->onUpdate('restrict')->onDelete('no action');
        });
    }
};
