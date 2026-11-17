<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resident_registries', function (Blueprint $table) {
            if (!Schema::hasColumn('resident_registries', 'gender')) {
                $table->string('gender')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('resident_registries', 'civil_status')) {
                $table->string('civil_status')->nullable()->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resident_registries', function (Blueprint $table) {
            $table->dropColumn(['gender', 'civil_status']);
        });
    }
};