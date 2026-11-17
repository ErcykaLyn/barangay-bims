<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('announcement_user', function (Blueprint $table) {
            // Drop the existing foreign key (find its name; usually 'announcement_user_user_id_foreign')
            $table->dropForeign(['user_id']);
            
            // Re-add the foreign key referencing the residents table
            $table->foreign('user_id')
                  ->references('id')
                  ->on('residents')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('announcement_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};