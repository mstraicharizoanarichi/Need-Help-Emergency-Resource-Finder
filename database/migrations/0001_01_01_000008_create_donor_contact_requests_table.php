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
        Schema::create('donor_contact_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_donor_id')->constrained('blood_donors')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_name');
            $table->string('requester_phone', 30);
            $table->string('patient_condition')->nullable();
            $table->string('hospital_name');
            $table->string('hospital_location');
            $table->enum('urgency_level', ['urgent', 'very_urgent', 'emergency'])->default('urgent');
            $table->unsignedSmallInteger('units_needed')->default(1);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined', 'cancelled'])->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_contact_requests');
    }
};
