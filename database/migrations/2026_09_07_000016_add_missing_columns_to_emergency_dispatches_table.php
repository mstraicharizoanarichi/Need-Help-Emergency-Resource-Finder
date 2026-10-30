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
        Schema::table('emergency_dispatches', function (Blueprint $table) {
            if (!Schema::hasColumn('emergency_dispatches', 'match_rank')) {
                $table->unsignedTinyInteger('match_rank')->nullable()->after('match_score');
            }
            if (!Schema::hasColumn('emergency_dispatches', 'traffic_multiplier_applied')) {
                $table->decimal('traffic_multiplier_applied', 4, 2)->default(1.00)->after('estimated_eta_minutes');
            }
            if (!Schema::hasColumn('emergency_dispatches', 'en_route_at')) {
                $table->timestamp('en_route_at')->nullable()->after('responded_at');
            }
            if (!Schema::hasColumn('emergency_dispatches', 'notes')) {
                $table->text('notes')->nullable()->after('rejection_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_dispatches', function (Blueprint $table) {
            $table->dropColumn(['match_rank', 'traffic_multiplier_applied', 'en_route_at', 'notes']);
        });
    }
};
