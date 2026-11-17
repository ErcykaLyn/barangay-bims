<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('equipment', function (Blueprint $table) {
        $table->id();
        $table->string('item_name'); // Siguraduhing 'item_name' ito
        $table->integer('quantity')->default(1); // Idinagdag para sa dami
        $table->string('status')->default('available');
        $table->unsignedBigInteger('current_borrower_id')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
