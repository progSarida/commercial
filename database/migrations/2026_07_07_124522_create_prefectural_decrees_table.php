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
        // 1. Tabella principale (senza i campi JSON)
        Schema::create('prefectural_decrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');                            // id provincia che ha emesso il decreto
            $table->text('note')->nullable();                                                                           // note
            $table->string('attachment_path')->nullable();                                                              // percorso pdf decreto
            $table->timestamps();
        });

        // 2. Tabella Pivot per i Comuni (Cities)
        Schema::create('city_prefectural_decree', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefectural_decree_id')                                                                  // id del decreto prefettizio
                ->constrained('prefectural_decrees')
                ->onDelete('cascade');
            $table->foreignId('city_id')                                                                                // id del comune
                ->constrained('cities')
                ->onDelete('cascade');
        });

        // 3. Tabella Pivot per i Clienti (Clients)
        Schema::create('client_prefectural_decree', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefectural_decree_id')                                                                  // id del decreto prefettizio
                ->constrained('prefectural_decrees')
                ->onDelete('cascade');
            $table->foreignId('client_id')                                                                              // id del cliente
                ->constrained('clients')
                ->onDelete('cascade');
        });

        // 4. Tabella strade del decreto prefettizio (opzionale, se vuoi tenere traccia delle strade)
        Schema::create('prefectural_decree_streets', function (Blueprint $table) {
            $table->id();
            // Relazione: ogni strada è legata a un decreto
            $table->foreignId('prefectural_decree_id')->constrained()->onDelete('cascade');                             // id del decreto prefettizio
            $table->string('name');                                                                                     // nome della strada (es. "Via Roma", "S.S. 16")  
            $table->foreignId('city_id')                                                                                // id del comune
                ->constrained('cities')
                ->nullable()
                ->onDelete('cascade');                      
            $table->string('note')->nullable();                                                                         // eventuali dettagli (es. "dal km 10 al km 15")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefectural_decree_streets');
        Schema::dropIfExists('client_prefectural_decree');
        Schema::dropIfExists('city_prefectural_decree');
        Schema::dropIfExists('prefectural_decrees');
    }
};
