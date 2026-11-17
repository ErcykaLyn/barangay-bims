<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Business Clearance fields — NULLABLE (optional)
            $table->string('business_name')->nullable()->after('purpose');
            $table->string('business_owner')->nullable()->after('business_name');
            $table->text('business_address')->nullable()->after('business_owner');
            $table->string('application_type')->nullable()->after('business_address');
            $table->string('ctc_no')->nullable()->after('application_type');
            
            // Residency fields — NULLABLE (optional)
            $table->string('valid_id')->nullable()->after('ctc_no');
            $table->string('id_ref_no')->nullable()->after('valid_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'business_name', 
                'business_owner', 
                'business_address', 
                'application_type', 
                'ctc_no', 
                'valid_id', 
                'id_ref_no'
            ]);
        });
    }
};