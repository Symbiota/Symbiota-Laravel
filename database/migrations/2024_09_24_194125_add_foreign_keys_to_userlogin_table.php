<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('userlogin', function (Blueprint $table) {
            $table->foreign(['uid'], 'FK_login_user')->references(['uid'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('userlogin', function (Blueprint $table) {
            $table->dropForeign('FK_login_user');
        });
    }
};
