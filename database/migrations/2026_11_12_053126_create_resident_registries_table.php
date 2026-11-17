<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_registries', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('zone_purok')->nullable();
            $table->string('complete_address')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_registries');
    }
};