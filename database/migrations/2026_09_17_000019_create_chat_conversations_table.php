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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('language', 10)->default('en');
            $table->string('status', 30)->default('active')->index();
            $table->string('detected_intent', 50)->nullable()->index();
            $table->string('severity', 10)->nullable();
            $table->boolean('emergency_detected')->default(false)->index();
            $table->boolean('emergency_mode')->default(false)->index();
            $table->string('current_question_key', 100)->nullable();
            $table->json('collected_data')->nullable();
            $table->foreignId('emergency_incident_id')->nullable()->constrained('emergency_incidents')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
