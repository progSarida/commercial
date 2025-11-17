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
        Schema::create('clients', function (Blueprint $table) {                                                 // tabella clienti
            $table->id();
            $table->string('name');                                                                             // nome cliente
            $table->string('client_type');                                                                      // tipo cliente (enum ClientType)
            $table->string('phone')->nullable();                                                                // telefono
            $table->string('email')->nullable();                                                                // email
            $table->string('site')->nullable();                                                                 // sito
            $table->foreignId('state_id')->constrained('states')->onUpdate('cascade');                          // id paese (se paese è italia)
            $table->foreignId('region_id')->constrained('regions')->onUpdate('cascade')->nullable();            // id regione (se paese è italia)
            $table->foreignId('province_id')->constrained('provinces')->onUpdate('cascade')->nullable();        // id provincia (se paese è italia)
            $table->foreignId('city_id')->constrained('cities')->onUpdate('cascade')->nullable();               // id comune (se paese è italia)
            $table->string('place')->nullable();                                                                // luogo (se paese diverso da italia)
            $table->string('zip_code')->nullable();                                                             // cap
            $table->string('address')->nullable();                                                              // indirizzo
            $table->string('civic')->nullable();                                                                // nunero civico
            $table->text('note')->nullable();                                                                   // note
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {                                                // tabella contatti
            $table->id();
            $table->string('contact_type')->nullable();                                                         // tipo contatto (enum ContactType)
            $table->foreignId('client_id')->constrained('clients')->onUpdate('cascade')->onDelete('cascade');   // id cliente
            $table->date('date')->nullable();                                                                   // data
            $table->time('time')->nullable();                                                                   // orario
            $table->string('note')->nullable();                                                                 // note
            $table->string('outcome_type')->nullable();                                                         // tipo esito (enum OutcomeType)
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');                            // id utente
            $table->timestamps();
        });

        Schema::create('client_services', function (Blueprint $table) {                                         // tabella servizi cliente
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');                        // id cliente
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');            // id tipo servizio offerto
            $table->text('note')->nullable();                                                                   // note
            $table->string('service_state')->nullable();                                                        // stato del servizio (enum ServiceState)
            $table->string('referent')->nullable();                                                             // referente servizio
            $table->string('phone')->nullable();                                                                // telefono referente servizio
            $table->string('email')->nullable();                                                                // email referente servizio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('client_services');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('clients');
        Schema::enableForeignKeyConstraints();
    }
};
