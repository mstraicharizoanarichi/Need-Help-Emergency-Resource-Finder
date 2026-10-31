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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained('resource_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            
            // Location
            $table->string('address');
            $table->string('area')->nullable()->index();
            $table->string('city')->default('Dhaka')->index();
            $table->string('district')->default('Dhaka')->index();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->index();
            $table->decimal('longitude', 10, 7)->index();
            
            // Contacts
            $table->string('phone', 50);
            $table->string('emergency_phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // Status & Verification
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'suspended'])->default('pending')->index();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            
            $table->enum('availability_status', ['available', 'unavailable', 'busy', 'unknown'])->default('available')->index();
            $table->boolean('is_24_7')->default(false)->index();
            $table->string('operating_hours_text')->nullable();
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Composite Indexes for MySQL performance
            $table->index(['latitude', 'longitude'], 'idx_resources_lat_lng');
            $table->index(['category_id', 'verification_status', 'availability_status'], 'idx_resources_cat_ver_avail');
            $table->index(['district', 'city'], 'idx_resources_district_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
