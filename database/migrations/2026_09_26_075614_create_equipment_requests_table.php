<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->integer('quantity');
            $table->text('reason')->nullable();
            $table->date('needed_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'picked_up', 'returned', 'cancelled'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('resident_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_requests');
    }
};