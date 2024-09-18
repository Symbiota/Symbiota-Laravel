<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {

        if(Schema::hasTable('users')) {
            // Add Attributes for Laravel auth to old table
            Schema::table('users', function (Blueprint $table) {
                $table->string('name');
                $table->string('old_password');
                $table->string('password', 255)->change();
                $table->timestamp('email_verified_at')->nullable();
            });

            // Popluate New Columns with old data
            DB::statement('UPDATE users set old_password = password, name = TRIM(CONCAT(last_name," ", first_name)), email_verified_at = now()');
        } else {
            // create user table
            // user table already exists so need to port that table defintion here first
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::statement('UPDATE users set password = old_password where old_password IS NOT null');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('old_password');
            $table->dropColumn('email_verified_at');

            // Could truncate password if old is null and new is 255
            // So gonna leave out for now
            // $table->string('password', 45)->change();
        });
    }
};
