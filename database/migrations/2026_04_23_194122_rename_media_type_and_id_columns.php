<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /** * Run the migrations. */ public function up(): void {

        Schema::table('imagetag', function (Blueprint $table) {
            $table->dropForeign('imagetag_ibfk_1');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('media_type', 'mediaType');
            $table->renameColumn('media_id', 'mediaID');
        });

        Schema::table('imagetag', function (Blueprint $table) {
            $table->foreign(['imgid'], 'imagetag_ibfk_1')
                ->references(['mediaID'])
                ->on('media')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }
    /** * Reverse the migrations. */ public function down(): void {

        Schema::table('imagetag', function (Blueprint $table) {
            $table->dropForeign('imagetag_ibfk_1');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('mediaType', 'media_type');
            $table->renameColumn('mediaID', 'media_id');
        });

        Schema::table('imagetag', function (Blueprint $table) {
            $table->foreign(['imgid'], 'imagetag_ibfk_1')
                ->references(['media_id'])
                ->on('media')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }
};
