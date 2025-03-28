<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('ommaterialsample', function (Blueprint $table) {
            $table->foreign(['occid'], 'FK_ommatsample_occid')->references(['occid'])->on('omoccurrences')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['preparedByUid'], 'FK_ommatsample_prepUid')->references(['uid'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('ommaterialsample', function (Blueprint $table) {
            $table->dropForeign('FK_ommatsample_occid');
            $table->dropForeign('FK_ommatsample_prepUid');
        });
    }
};
