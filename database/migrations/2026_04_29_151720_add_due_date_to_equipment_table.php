<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->timestamp('due_date')->nullable()->after('current_borrower_id');
        });
    }

    public function down()
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};