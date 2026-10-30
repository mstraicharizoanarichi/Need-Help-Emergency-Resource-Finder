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
        Schema::create('emergency_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('public_reference')->unique()->index();
            $table->foreignId('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_phone')->nullable();
            $table->string('emergency_type')->default('MEDICAL')->index();
            $table->text('description')->nullable();
            $table->text('voice_transcript')->nullable();
            $table->text('ai_summary')->nullable();
            
            // Location
            $table->decimal('latitude', 10, 7)->nullable()->index();
            $table->decimal('longitude', 10, 7)->nullable()->index();
            $table->string('address')->nullable();
            $table->string('area')->nullable()->index();
            $table->string('city')->default('Dhaka')->index();
            $table->string('district')->default('Dhaka')->index();
            
            // Triage & Severity
            $table->unsignedSmallInteger('number_of_victims')->default(1);
            $table->enum('severity_level', ['P1', 'P2', 'P3', 'P4'])->default('P2')->index();
            $table->unsignedSmallInteger('priority_score')->default(50)->index(); // 0 to 100
            $table->json('severity_reasons')->nullable();
            $table->string('required_service')->nullable(); // e.g. ambulance, fire-service, police
            $table->json('required_equipment')->nullable(); // e.g. ['oxygen', 'ventilator']
            $table->json('required_skills')->nullable(); // e.g. ['paramedic', 'first_aid']
            
            // Lifecycle Status
            $table->enum('status', [
                'REPORTED',
                'ANALYZING',
                'PRIORITIZED',
                'AWAITING_DISPATCH',
                'DISPATCHED',
                'RESPONDER_ACCEPTED',
                'RESPONDER_EN_ROUTE',
                'RESPONDER_ARRIVED',
                'PATIENT_PICKED_UP',
                'AT_HOSPITAL',
                'RESOLVED',
                'CANCELLED',
            ])->default('REPORTED')->index();
            
            $table->decimal('ai_confidence', 4, 2)->nullable();
            $table->boolean('is_sos')->default(false)->index();
            
            // Assignments
            $table->foreignId('assigned_resource_id')->nullable()->constrained('resources')->nullOnDelete();
            $table->foreignId('assigned_hospital_id')->nullable()->constrained('resources')->nullOnDelete();
            
            // Timestamps for SLA & Analytics
            $table->timestamp('reported_at')->useCurrent()->index();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Composite Indexes for speed
            $table->index(['latitude', 'longitude'], 'idx_incidents_lat_lng');
            $table->index(['status', 'priority_score'], 'idx_incidents_status_priority');
            $table->index(['emergency_type', 'severity_level'], 'idx_incidents_type_severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_incidents');
    }
};
