<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable();
            }
            if (!Schema::hasColumn('users', 'civil_status')) {
                $table->string('civil_status')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'zone')) {
                $table->string('zone')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'middle_name', 'last_name', 'dob', 'age',
                'civil_status', 'gender', 'phone', 'zone', 'address'
            ]);
        });
    }
};