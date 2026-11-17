<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->bigIncrements('id');              // ✅ bigIncrements para tugma sa FK
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('occupation')->nullable();
            $table->string('zone_purok')->nullable();
            $table->text('complete_address')->nullable();
            $table->string('password');
            $table->string('role')->default('resident');
            $table->boolean('is_senior')->default(false);
            $table->boolean('is_indigent')->default(false);
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_solo_parent')->default(false);
            $table->boolean('is_voter')->default(false);
            $table->timestamp('last_viewed_status_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('residents');
    }
};