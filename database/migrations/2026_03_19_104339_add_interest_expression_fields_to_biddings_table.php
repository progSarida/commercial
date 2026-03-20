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
            $table->string('interest_expression_type')->after('bidding_note')->nullable();                                              // tipo manifestazione d'interesse (enum InterestExpressionType)
            $table->date('interest_deadline_date')->after('interest_expression_type')->nullable();                                      // data scadenza presentazione manifestazione d'interessi
            $table->time('interest_deadline_time')->after('interest_deadline_date')->nullable();                                        // ora scadenza presentazione manifestazione d'interessi
            $table->date('interest_send_date')->after('interest_deadline_time')->nullable();                                            // data invio presentazione manifestazione d'interessi
            $table->time('interest_send_time')->after('interest_send_date')->nullable();                                                // ora invio presentazione manifestazione d'interessi
            $table->string('interest_send_mode_type')->after('interest_send_time')->nullable();                                         // tipo invio manifestazione d'interesse (enum SendModeType)

            $table->date('inspection_date')->after('inspection_deadline_time')->nullable();                                             // data sopralluogo
            $table->time('interest_time')->after('inspection_date')->nullable();                                                        // ora sopralluogo

            $table->string('send_mode_type')->after('send_time')->nullable();                                                           // tipo invio offerta (enum SendModeType)
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
