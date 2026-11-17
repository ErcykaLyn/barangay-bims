<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->integer('quantity_borrowed')->nullable()->after('quantity');
            $table->integer('quantity_returned')->default(0)->after('quantity_borrowed');
            $table->integer('shortage')->default(0)->after('quantity_returned');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->dropColumn(['quantity_borrowed', 'quantity_returned', 'shortage']);
        });
    }
};