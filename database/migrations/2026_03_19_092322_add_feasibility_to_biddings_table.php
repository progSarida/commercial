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
        Schema::table('biddings', function (Blueprint $table) {
            $table->string('feasibility_type')->after('residents')->nullable();                                                         // tipo fattibilità (enum FeasibilityType)
        });

        Schema::table('bidding_states', function (Blueprint $table) {
            $table->string('feasibility_type')->after('id')->nullable();                                                                // tipo fattibilità associata(enum FeasibilityType)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biddings', function (Blueprint $table) {
            //
        });
    }
};
