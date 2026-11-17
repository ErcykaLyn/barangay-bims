<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // ✅ Idagdag ang mga nawawalang columns
            if (!Schema::hasColumn('service_requests', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('processed_at');
            }
            if (!Schema::hasColumn('service_requests', 'denied_at')) {
                $table->timestamp('denied_at')->nullable()->after('accepted_at');
            }
            if (!Schema::hasColumn('service_requests', 'ready_at')) {
                $table->timestamp('ready_at')->nullable()->after('denied_at');
            }
            if (!Schema::hasColumn('service_requests', 'denial_reason')) {
                $table->text('denial_reason')->nullable()->after('completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'accepted_at',
                'denied_at',
                'ready_at',
                'denial_reason',
            ]);
        });
    }
};