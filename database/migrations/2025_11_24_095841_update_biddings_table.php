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
           $table->string('awarded')->nullable()->after('attachment_path');                         // nostra aggiudicazione gara
           $table->date('closure_date')->nullable()->after('awarded');                              // data chiusura procedura
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
