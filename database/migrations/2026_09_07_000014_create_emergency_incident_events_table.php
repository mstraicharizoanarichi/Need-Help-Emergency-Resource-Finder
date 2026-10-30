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
        Schema::create('emergency_incident_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('emergency_incidents')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index();
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['incident_id', 'created_at'], 'idx_events_incident_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_incident_events');
    }
};
