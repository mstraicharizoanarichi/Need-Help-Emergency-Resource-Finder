<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resource_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->unique()->constrained('resources')->cascadeOnDelete();
            
            // Medical & Hospital specifics
            $table->boolean('icu_available')->default(false);
            $table->integer('icu_beds_count')->nullable();
            $table->boolean('ambulance_available')->default(false);
            $table->boolean('oxygen_available')->default(false);
            $table->boolean('blood_bank_available')->default(false);
            $table->boolean('emergency_ward')->default(false);
            $table->integer('general_beds_count')->nullable();
            
            // Ambulance / Vehicle specifics
            $table->string('vehicle_types')->nullable(); // e.g. "AC, Non-AC, ICU, Freezing"
            $table->boolean('paramedic_onboard')->default(false);
            
            // Pharmacy & Diagnostic specifics
            $table->boolean('home_delivery')->default(false);
            $table->boolean('online_reports')->default(false);
            
            // Structured details
            $table->json('services_offered')->nullable(); // list of strings
            $table->json('facilities')->nullable(); // list of strings
            $table->json('accepted_insurances')->nullable();
            $table->text('additional_info')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_details');
    }
};
