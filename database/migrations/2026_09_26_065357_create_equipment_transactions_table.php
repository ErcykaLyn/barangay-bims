<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['borrow', 'return', 'damage', 'restock', 'adjustment']);
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->date('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_transactions');
    }
};