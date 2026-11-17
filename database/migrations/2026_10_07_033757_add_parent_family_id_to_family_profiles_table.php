<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_profiles', function (Blueprint $table) {
            // ✅ Idagdag ang parent_family_id column
            if (!Schema::hasColumn('family_profiles', 'parent_family_id')) {
                $table->unsignedBigInteger('parent_family_id')->nullable()->after('notes');
                
                // Optional: foreign key constraint
                $table->foreign('parent_family_id')
                      ->references('id')
                      ->on('family_profiles')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('family_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('family_profiles', 'parent_family_id')) {
                $table->dropForeign(['parent_family_id']);
                $table->dropColumn('parent_family_id');
            }
        });
    }
};