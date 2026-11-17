<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Photo path
            $table->string('photo_path')->nullable()->after('item_name');

            // Stock tracking
            $table->integer('total_stock')->default(0)->after('quantity');
            $table->integer('available_stock')->default(0)->after('total_stock');
            $table->integer('borrowed_stock')->default(0)->after('available_stock');
            $table->integer('damaged_stock')->default(0)->after('borrowed_stock');

            // Tracking
            $table->decimal('unit_price', 10, 2)->nullable()->after('damaged_stock');
            $table->text('description')->nullable()->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path', 'total_stock', 'available_stock',
                'borrowed_stock', 'damaged_stock', 'unit_price', 'description'
            ]);
        });
    }
};