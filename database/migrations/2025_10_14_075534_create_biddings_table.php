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
        Schema::create('biddings', function (Blueprint $table) {
            $table->id();
            // 'services' => tabella 'bidding_service_type'
            $table->text('description')->nullable();                                                                                        // descrizione gara
            $table->decimal('amount', 15, 2)->nullable();                                                                                   // importo gara
            $table->string('residents', 10)->nullable();                                                                                    // numero residenti
            $table->foreignId('bidding_state_id')->nullable()->constrained('bidding_states')->onDelete('set null');                         // id stato gara
            // 'bidding_processing_state_id' => 'bidding_processig_state'
            $table->string('bidding_processing_state')->nullable();                                                                         // stato lavorazione gara (enum BiddingProcessingState)
            // 'priority_id' => 'bidding_priority_type'
            $table->string('bidding_priority_type')->nullable();                                                                            // priorità gara (enum BiddingPriorityType)
            $table->foreignId('bidding_type_id')->nullable()->constrained('bidding_types')->onDelete('set null');                           // id tipo gara
            $table->foreignId('bidding_adjudication_type_id')->nullable()->constrained('bidding_adjudication_types')->onDelete('set null'); // id tipo aggiudicazione gara
            $table->boolean('mandatory_inspection')->nullable();                                                                            // flag per ispezione obbligatoria
            $table->string('contact', 250)->nullable();                                                                                     // nome contatto
            // entity_type_id' => 'client_type'
            $table->string('client_type')->nullable();                                                                                      // tipo cliente (enum ClientType)
            $table->string('client_name', 150)->nullable();                                                                                 // nome cliente
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');                                        // id cliente
            $table->string('contracting_station', 500)->nullable();                                                                         // gestore appalto
            // $table->foreignId('contracting_station_id')->nullable()->constrained('contracting_stations')->onDelete('set null');             // id gestore appalto ( ?? mai usata ?? )
            $table->string('region_id', 3)->nullable();                                                                                     // id regione
            $table->string('province', 250)->nullable();                                                                                    // nome provincia
            $table->string('province_id', 3)->nullable();                                                                                   // id provincia
            // 'procedure_type_id' => 'bidding_procedure_type'
            $table->string('bidding_procedure_type')->nullable();                                                                           // tipo procedura (enum BiddingProcedureType)
            $table->string('procedure_portal', 500)->nullable();                                                                            // portale procedura
            $table->string('cig', 10)->nullable();                                                                                          // CIG
            $table->string('procedure_id', 25)->nullable();                                                                                 // id procedura
            $table->integer('day')->nullable();                                                                                             // durata appalto in giorni
            $table->integer('month')->nullable();                                                                                           // durata appalto in mesi
            $table->integer('year')->nullable();                                                                                            // durata appalto in anni
            $table->date('renew')->nullable();                                                                                              // data rinnovo
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');                                  // id utente assegnatario
            $table->foreignId('modified_user_id')->nullable()->constrained('users')->onDelete('set null');                                  // id utente ultima modifica
            $table->text('bidding_note')->nullable();                                                                                       // note gara
            $table->text('note')->nullable();                                                                                               // note
            $table->date('deadline_date')->nullable();                                                                                      // data scadenza gara
            $table->time('deadline_time')->nullable()->default('06:00:00');                                                                 // ora scadenza gara
            $table->date('send_date')->nullable();                                                                                          // data invio
            $table->time('send_time')->default('06:00:00');                                                                                 // ora invio
            $table->date('clarification_request_deadline_date')->nullable();                                                                // data scadenza chiarimenti
            $table->time('clarification_request_deadline_time')->default('06:00:00');                                                       // ora scadenza chiarimenti
            $table->date('inspection_deadline_date')->nullable();                                                                           // data scadenza sopralluogo
            $table->time('inspection_deadline_time')->default('06:00:00')->nullable();                                                      // ora scadenza sopralluogo
            $table->date('opening_date')->nullable();                                                                                       // data apertura offerte
            $table->time('opening_time')->nullable();                                                                                       // ora apertura offerte
            $table->foreignId('source1_id')->nullable()->constrained('bidding_data_sources')->onDelete('set null');                         // id prima fonte dati
            $table->foreignId('source2_id')->nullable()->constrained('bidding_data_sources')->onDelete('set null');                         // id seconda fonte dati
            $table->foreignId('source3_id')->nullable()->constrained('bidding_data_sources')->onDelete('set null');                         // id terza fonte dati
            $table->timestamps();
        });

        Schema::create('bidding_service_type', function (Blueprint $table) {                                                                // tabella servizi gara
            $table->id();
            $table->foreignId('bidding_id')->constrained('biddings')->onDelete('cascade');                                                  // id gara
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');                                        // id servizio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('bidding_service_type');
        Schema::dropIfExists('biddings');
        Schema::enableForeignKeyConstraints();
    }
};
