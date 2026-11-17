<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};