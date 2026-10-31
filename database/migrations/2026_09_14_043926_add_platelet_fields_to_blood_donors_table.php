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
        Schema::table('blood_donors', function (Blueprint $table) {
            $table->date('last_platelet_donation_date')->nullable()->after('last_donation_date');
            $table->boolean('is_apheresis_donor')->default(false)->after('last_platelet_donation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_donors', function (Blueprint $table) {
            $table->dropColumn(['last_platelet_donation_date', 'is_apheresis_donor']);
        });
    }
};
