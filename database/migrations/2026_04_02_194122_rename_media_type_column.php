<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('media_type', 'mediaType');
            $table->renameColumn('media_id', 'mediaID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('mediaType', 'media_type');
            $table->renameColumn('mediaID', 'media_id');
        });
    }
};
