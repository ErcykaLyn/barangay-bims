<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // Add email column after username
            $table->string('email')->nullable()->unique()->after('username');
        });

        // Optional: I-copy ang existing username sa email (kung valid email format)
        // DB::table('residents')->whereNull('email')->update([
        //     'email' => DB::raw('CONCAT(username, "@bactadeast.gov.ph")')
        // ]);
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};