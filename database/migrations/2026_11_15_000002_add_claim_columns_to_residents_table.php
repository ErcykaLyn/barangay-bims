<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (!Schema::hasColumn('residents', 'claimed_from_import')) {
                // true = ang account na ito ay galing sa imported na record (na-claim ng residente)
                $table->boolean('claimed_from_import')->default(false)->after('status');
            }
            if (!Schema::hasColumn('residents', 'claimed_at')) {
                $table->timestamp('claimed_at')->nullable()->after('claimed_from_import');
            }
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            foreach (['claimed_at', 'claimed_from_import'] as $col) {
                if (Schema::hasColumn('residents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
