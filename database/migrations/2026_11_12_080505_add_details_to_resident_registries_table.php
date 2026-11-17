<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resident_registries', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('civil_status')->nullable()->after('gender');
            $table->string('complete_address')->nullable()->after('zone_purok');
        });
    }

    public function down(): void
    {
        Schema::table('resident_registries', function (Blueprint $table) {
            $table->dropColumn(['gender', 'civil_status', 'complete_address']);
        });
    }
};