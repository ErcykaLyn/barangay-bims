<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Update ang ENUM para kasama ang 'accepted'
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('pending', 'accepted', 'processing', 'ready_for_pickup', 'completed', 'denied') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('pending', 'processing', 'ready_for_pickup', 'completed', 'denied') DEFAULT 'pending'");
    }
};