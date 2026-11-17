<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Idinadagdag ang mga column na galing sa Form2 Excel.
 * Bawat column ay may hasColumn guard, kaya ligtas kahit may ilan nang existing.
 */
return new class extends Migration
{
    private function columns(): array
    {
        return [
            'date_of_visit'       => fn(Blueprint $t) => $t->string('date_of_visit', 20)->nullable(),
            'fourps_household_id' => fn(Blueprint $t) => $t->string('fourps_household_id', 50)->nullable(),
            'philhealth_id'       => fn(Blueprint $t) => $t->string('philhealth_id', 50)->nullable(),
            'philhealth_member'   => fn(Blueprint $t) => $t->string('philhealth_member', 5)->nullable(),
            'philhealth_category' => fn(Blueprint $t) => $t->string('philhealth_category', 50)->nullable(),
            'medical_specify'     => fn(Blueprint $t) => $t->text('medical_specify')->nullable(),
            'classification'      => fn(Blueprint $t) => $t->string('classification', 20)->nullable(),
            'lmp_date'            => fn(Blueprint $t) => $t->date('lmp_date')->nullable(),
            'using_fp'            => fn(Blueprint $t) => $t->boolean('using_fp')->nullable(),
            'fp_method'           => fn(Blueprint $t) => $t->string('fp_method', 50)->nullable(),
            'fp_status'           => fn(Blueprint $t) => $t->string('fp_status', 20)->nullable(),
            'water_source'        => fn(Blueprint $t) => $t->string('water_source')->nullable(),
            'toilet_facility'     => fn(Blueprint $t) => $t->string('toilet_facility')->nullable(),
            'is_deceased'         => fn(Blueprint $t) => $t->boolean('is_deceased')->default(false),
        ];
    }

    public function up(): void
    {
        foreach ($this->columns() as $name => $define) {
            if (!Schema::hasColumn('family_members', $name)) {
                Schema::table('family_members', fn(Blueprint $t) => $define($t));
            }
        }

        // Ang orihinal na table ay may 'toilet_type'; ang blade at importer ay 'toilet_facility'.
        // Kopyahin ang lumang data (hindi binubura ang toilet_type).
        if (Schema::hasColumn('family_members', 'toilet_type') && Schema::hasColumn('family_members', 'toilet_facility')) {
            DB::table('family_members')
                ->whereNull('toilet_facility')
                ->whereNotNull('toilet_type')
                ->update(['toilet_facility' => DB::raw('toilet_type')]);
        }
    }

    public function down(): void
    {
        // Sinadya na walang drop: baka may existing na data ang ilang column bago ang migration na ito.
    }
};
