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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->nullable()->constrained('resources')->nullOnDelete();
            $table->foreignId('blood_donor_id')->nullable()->constrained('blood_donors')->nullOnDelete();
            $table->foreignId('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('reporter_name');
            $table->string('reporter_email')->nullable();
            $table->string('reporter_phone', 30)->nullable();
            
            $table->enum('report_type', [
                'incorrect_phone',
                'wrong_location',
                'closed_service',
                'outdated_info',
                'duplicate_resource',
                'incorrect_availability',
                'other'
            ])->index();
            
            $table->text('description');
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'rejected'])->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
