<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('family_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('family_code')->unique();          // e.g., FAM-001
            $table->string('head_first_name');
            $table->string('head_last_name');
            $table->string('zone_purok')->nullable();
            $table->text('complete_address')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('notes')->nullable();                // optional remarks
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('family_profiles');
    }
};