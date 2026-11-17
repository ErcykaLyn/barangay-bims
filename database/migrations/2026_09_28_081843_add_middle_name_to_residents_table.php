<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // ✅ Idagdag ang middle_name column, nullable at nasa pagitan ng first_name at last_name
            $table->string('middle_name')->nullable()->after('first_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // ✅ Tanggalin ang middle_name column kapag nag-rollback
            $table->dropColumn('middle_name');
        });
    }
};