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
        Schema::create('estimates', function (Blueprint $table) {                                   // tabella preventivi
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');            // id cliente
            $table->string('contact_type')->nullable();                                             // tipo contatto (enum ContactType)
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');          // id contatto
            $table->date('date')->nullable();                                                       // data richiesta preventivo
            $table->foreignId('request_user_id')->constrained('users')->onUpdate('cascade');        // id utente che ha fatto la richiesta di preventivo
            $table->boolean('done')->default(0);                                                    // flag chiusura preventivo
            $table->unsignedBigInteger('done_user_id')->nullable();                   		        //
            $table->foreign('done_user_id')->references('id')->on('users');           		        // id utente che ha chiuso il preventivo
            $table->string('path')->nullable();                                                     // percorso file preventivo caricato
            $table->string('estimate_state')->nullable();                                           // tipo contatto (enum EstimateState)
            $table->unsignedBigInteger('state_user_id')->nullable();                   		        //
            $table->foreign('state_user_id')->references('id')->on('users');           		        // id utente che ha modificato per ultimo lo stato del preventivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('estimates');
        Schema::enableForeignKeyConstraints();
    }
};
