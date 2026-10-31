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
        Schema::create('blood_donor_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_incident_id')->constrained('emergency_incidents')->cascadeOnDelete();
            $table->foreignId('blood_donor_id')->constrained('blood_donors')->cascadeOnDelete();
            $table->decimal('distance_km', 8, 2);
            $table->enum('status', ['pending', 'accepted', 'declined', 'expired'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donor_alerts');
    }
};
