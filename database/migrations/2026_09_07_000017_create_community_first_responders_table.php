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
        Schema::create('community_first_responders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->json('skills')->nullable(); // e.g. ["CPR", "BLEEDING_CONTROL", "AED", "FIRST_AID"]
            $table->string('certification_level')->default('BASIC_FIRST_AID'); 
            // BASIC_FIRST_AID, CPR_AED_CERTIFIED, MEDICAL_STUDENT, NURSE, DOCTOR, RED_CRESCENT_VOLUNTEER
            $table->string('certification_details')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_available')->default(true);
            $table->string('status', 30)->default('AVAILABLE'); // AVAILABLE, RESPONDING, OFF_DUTY
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city', 100)->default('Dhaka');
            $table->string('area', 100)->nullable();
            $table->unsignedInteger('total_responses')->default(0);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index(['is_available', 'is_verified', 'status']);
            $table->index(['city', 'area']);
            $table->index(['latitude', 'longitude']);
        });

        Schema::create('cfr_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_incident_id')->constrained('emergency_incidents')->cascadeOnDelete();
            $table->foreignId('community_first_responder_id')->constrained('community_first_responders')->cascadeOnDelete();
            $table->string('status', 30)->default('ALERTED'); // ALERTED, ACCEPTED, DECLINED, ON_SCENE, CANCELLED
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->timestamp('alerted_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['emergency_incident_id', 'status']);
            $table->index(['community_first_responder_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cfr_alerts');
        Schema::dropIfExists('community_first_responders');
    }
};
