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
    Schema::create('service_requests', function (Blueprint $table) {
        $table->id();
        // Naka-link sa resident na nag-request [cite: 36]
        $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
        
        // Type ng dokumento (Clearance, Indigency, etc.) [cite: 36, 54]
        $table->string('document_type'); 
        
        // Reason para sa request (para sa documentation ng barangay)
        $table->text('purpose')->nullable();
        
        // Status ng request para alam ng resident kung ready na [cite: 54]
        $table->enum('status', ['pending', 'processing', 'ready_for_pickup', 'completed', 'denied'])
              ->default('pending');
        
        $table->timestamp('requested_at')->useCurrent();
        $table->timestamp('processed_at')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
