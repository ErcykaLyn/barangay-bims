<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('residents', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('residents', 'is_pwd')) {
                $table->boolean('is_pwd')->default(false)->after('is_indigent');
            }
            if (!Schema::hasColumn('residents', 'is_voter')) {
                $table->boolean('is_voter')->default(false)->after('is_pwd');
            }
        });
    }

    public function down()
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['is_pwd', 'is_voter']);
        });
    }
};