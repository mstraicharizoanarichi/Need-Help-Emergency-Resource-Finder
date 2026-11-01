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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('specialization'); // e.g. Cardiology, Neurology, Emergency Medicine, Orthopedics, Pediatrics, etc.
            $table->string('department')->nullable();
            $table->string('designation')->nullable(); // e.g. Professor & Head of Department, Senior Consultant
            $table->text('qualifications'); // e.g. MBBS, FCPS, MD (Cardiology), FACC (USA)
            $table->integer('experience_years')->nullable()->default(5);
            $table->json('visiting_days')->nullable(); // e.g. ["Sun", "Mon", "Tue", "Wed", "Thu", "Sat"]
            $table->string('visiting_hours')->nullable(); // e.g. "05:00 PM - 09:00 PM"
            $table->string('appointment_phone')->nullable(); // Hotline / direct booking number
            $table->decimal('consultation_fee', 8, 2)->nullable(); // e.g. 1000.00
            $table->boolean('is_emergency_available')->default(false); // On-duty emergency doctor
            $table->boolean('is_available_today')->default(true);
            $table->string('room_number')->nullable(); // e.g. Room 402, 4th Floor
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->integer('total_reviews')->default(12);
            $table->string('gender')->nullable(); // male, female
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index(['resource_id', 'specialization']);
            $table->index(['specialization', 'is_emergency_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
