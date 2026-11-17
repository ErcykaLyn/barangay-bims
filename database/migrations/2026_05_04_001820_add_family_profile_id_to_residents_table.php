<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->foreignId('family_profile_id')
                  ->nullable()
                  ->constrained('family_profiles')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['family_profile_id']);
            $table->dropColumn('family_profile_id');
        });
    }
};