<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('valid_id')->nullable()->after('profile_photo_path');
            $table->string('id_type')->nullable()->after('valid_id');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['valid_id', 'id_type']);
        });
    }
};