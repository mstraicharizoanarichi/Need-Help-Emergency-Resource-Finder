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
        Schema::create('emergency_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('emergency_incidents')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('dispatched_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->enum('status', [
                'PENDING',
                'ACCEPTED',
                'REJECTED',
                'EN_ROUTE',
                'ON_SCENE',
                'TRANSPORTING',
                'COMPLETED',
                'CANCELLED',
            ])->default('PENDING')->index();
            
            $table->text('rejection_reason')->nullable();
            $table->decimal('match_score', 5, 2)->default(0.00); // 0.00 - 100.00
            $table->json('match_explanation')->nullable();
            $table->decimal('estimated_distance_km', 8, 2)->nullable();
            $table->decimal('estimated_eta_minutes', 6, 2)->nullable();
            $table->decimal('actual_arrival_minutes', 6, 2)->nullable();
            
            // Timestamps
            $table->timestamp('dispatched_at')->useCurrent()->index();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['resource_id', 'status'], 'idx_dispatches_resource_status');
            $table->index(['incident_id', 'status'], 'idx_dispatches_incident_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_dispatches');
    }
};
