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
        Schema::create('bidding_states', function (Blueprint $table) {                          // tabella stati gara
            $table->id();
            $table->string('name');                                                             // nome stato gara
            $table->string('description')->nullable();                                          // descrizione stato gara
            $table->integer('position');                                                        // posizione nella selezione
            $table->timestamps();
        });

        Schema::create('bidding_types', function (Blueprint $table) {                           // tabella tipi gara
            $table->id();
            $table->string('name');                                                             // nome tipo gara
            $table->string('description')->nullable();                                          // descrizione tipo gara
            $table->integer('position');                                                        // posizione nella selezione
            $table->timestamps();
        });

        Schema::create('bidding_adjudication_types', function (Blueprint $table) {              // tabella tipi aggiudicazione
            $table->id();
            $table->string('name');                                                             // nome tipo aggiudicazione
            $table->string('description')->nullable();                                          // descrizione tipo aggiudicazione
            $table->integer('position');                                                        // posizione nella selezione
            $table->timestamps();
        });

        Schema::create('bidding_data_sources', function (Blueprint $table) {                    // tabella fonti di dati
            $table->id();
            $table->string('name');                                                             // nome fonte di dati
            $table->string('description')->nullable();                                          // descrizione fonte di dati
            $table->integer('position');                                                        // posizione nella selezione
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('bidding_data_sources');
        Schema::dropIfExists('bidding_adjudication_types');
        Schema::dropIfExists('bidding_types');
        Schema::dropIfExists('bidding_states');
        Schema::enableForeignKeyConstraints();
    }
};
