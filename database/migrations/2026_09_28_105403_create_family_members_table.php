<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_profile_id')->constrained('family_profiles')->onDelete('cascade');
            $table->string('relation_to_head')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->string('civil_status')->nullable();
            $table->string('education')->nullable();
            $table->string('religion')->nullable();
            $table->string('ethnicity')->nullable();
            $table->boolean('is_4ps')->default(false);
            $table->boolean('is_ip')->default(false);
            $table->boolean('is_pwd')->default(false);
            $table->string('medical_history')->nullable();
            $table->string('immunization')->nullable();
            $table->string('water_source')->nullable();
            $table->string('toilet_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};