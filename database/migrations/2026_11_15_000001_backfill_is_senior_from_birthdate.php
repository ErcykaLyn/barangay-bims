<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cutoff = now()->subYears(60)->toDateString();

        // 60+ -> senior
        DB::table('residents')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '<=', $cutoff)
            ->update(['is_senior' => true]);

        // mas bata sa 60 -> hindi senior
        DB::table('residents')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '>', $cutoff)
            ->update(['is_senior' => false]);
    }

    public function down(): void
    {
        // walang rollback: hindi na maibabalik ang dating manual na flags
    }
};
