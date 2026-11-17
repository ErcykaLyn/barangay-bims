<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter enum to include 'dispensed' and 'claimed'
        DB::statement("ALTER TABLE equipment_requests MODIFY COLUMN status ENUM(
            'pending', 'approved', 'rejected', 'picked_up', 'returned', 'cancelled', 'dispensed'
        ) DEFAULT 'pending'");

        // Add dispensed_at and dispensed_by columns
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->timestamp('dispensed_at')->nullable()->after('picked_up_at');
            $table->foreignId('dispensed_by')->nullable()->after('dispensed_at')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->dropForeign(['dispensed_by']);
            $table->dropColumn(['dispensed_at', 'dispensed_by']);
        });

        DB::statement("ALTER TABLE equipment_requests MODIFY COLUMN status ENUM(
            'pending', 'approved', 'rejected', 'picked_up', 'returned', 'cancelled'
        ) DEFAULT 'pending'");
    }
};