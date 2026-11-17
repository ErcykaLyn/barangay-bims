<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Para makapag-import ng residente na walang (o sirang) birthday
     * at maiayos na lang sa Edit ("Needs action").
     * Laravel 10+: gumagana ang ->change() nang walang doctrine/dbal.
     */
    public function up(): void
    {
        if (Schema::hasColumn('residents', 'date_of_birth')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->date('date_of_birth')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Sinadyang walang revert: may mga record nang maaaring walang birthday.
    }
};
