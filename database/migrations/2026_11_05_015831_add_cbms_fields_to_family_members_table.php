<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            // I-check bago mag-add para hindi mag-error
            if (!Schema::hasColumn('family_members', 'ethnicity')) {
                $table->string('ethnicity')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'religion')) {
                $table->string('religion')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'education')) {
                $table->string('education')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'medical_history')) {
                $table->string('medical_history')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'medical_specify')) {
                $table->string('medical_specify')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'philhealth_member')) {
                $table->string('philhealth_member')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'philhealth_category')) {
                $table->string('philhealth_category')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'philhealth_id')) {
                $table->string('philhealth_id')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'is_4ps')) {
                $table->string('is_4ps')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'is_ip')) {
                $table->string('is_ip')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'water_source')) {
                $table->string('water_source')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'toilet_facility')) {
                $table->string('toilet_facility')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'fp_status')) {
                $table->string('fp_status')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'fp_method')) {
                $table->string('fp_method')->nullable();
            }
            if (!Schema::hasColumn('family_members', 'lmp_date')) {
                $table->string('lmp_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $columns = [
                'ethnicity', 'religion', 'education', 'medical_history', 
                'medical_specify', 'philhealth_member', 'philhealth_category', 
                'philhealth_id', 'is_4ps', 'is_ip', 'water_source', 'toilet_facility',
                'fp_status', 'fp_method', 'lmp_date'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('family_members', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};