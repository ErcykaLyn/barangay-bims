<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL does not support MODIFY COLUMN with ENUM.
        // Instead, we drop the constraint (if any) and add the new value.
        DB::statement("ALTER TABLE equipment_requests DROP CONSTRAINT IF EXISTS equipment_requests_status_check");
        DB::statement("ALTER TABLE equipment_requests ADD CONSTRAINT equipment_requests_status_check CHECK (status IN ('pending', 'approved', 'rejected', 'picked_up', 'returned', 'cancelled', 'dispensed'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE equipment_requests DROP CONSTRAINT IF EXISTS equipment_requests_status_check");
        DB::statement("ALTER TABLE equipment_requests ADD CONSTRAINT equipment_requests_status_check CHECK (status IN ('pending', 'approved', 'rejected', 'picked_up', 'returned', 'cancelled'))");
    }
};