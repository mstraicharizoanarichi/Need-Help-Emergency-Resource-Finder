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
        Schema::create('blood_donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->index();
            $table->string('phone', 30);
            $table->string('alternative_phone', 30)->nullable();
            $table->string('email')->nullable();
            
            // Approximate Location
            $table->string('district')->default('Dhaka')->index();
            $table->string('city')->default('Dhaka')->index();
            $table->string('area')->nullable()->index();
            $table->decimal('latitude', 10, 7)->index();
            $table->decimal('longitude', 10, 7)->index();
            
            // Status & Privacy
            $table->boolean('is_available')->default(true)->index();
            $table->date('last_donation_date')->nullable();
            $table->unsignedInteger('total_donations')->default(0);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->index();
            $table->enum('privacy_setting', ['contact_request', 'show_on_request', 'direct_call'])->default('contact_request');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['blood_group', 'is_available', 'verification_status'], 'idx_donor_group_avail_ver');
            $table->index(['latitude', 'longitude'], 'idx_donor_lat_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donors');
    }
};
