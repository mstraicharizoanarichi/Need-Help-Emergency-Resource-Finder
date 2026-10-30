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
        Schema::table('emergency_incidents', function (Blueprint $table) {
            $table->boolean('is_mci')->default(false)->after('is_sos')->index();
            $table->string('mci_level', 20)->nullable()->after('is_mci')->index(); // LEVEL_1, LEVEL_2, LEVEL_3, LEVEL_4
            $table->json('mci_triage_counts')->nullable()->after('mci_level'); // {"red": 0, "yellow": 0, "green": 0, "black": 0}
            $table->json('mci_hospital_allocations')->nullable()->after('mci_triage_counts');
            $table->timestamp('mci_declared_at')->nullable()->after('mci_hospital_allocations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_incidents', function (Blueprint $table) {
            $table->dropColumn([
                'is_mci',
                'mci_level',
                'mci_triage_counts',
                'mci_hospital_allocations',
                'mci_declared_at',
            ]);
        });
    }
};
