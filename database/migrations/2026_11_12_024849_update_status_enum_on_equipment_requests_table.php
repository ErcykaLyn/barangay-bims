<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE equipment_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'picked_up', 'returned', 'dispensed', 'partial_return') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE equipment_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'picked_up', 'returned', 'dispensed') NOT NULL DEFAULT 'pending'");
    }
};