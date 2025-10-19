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
        Schema::create('tenders', function (Blueprint $table) {                                                     // tabella appalti
            $table->id();
            // dati generali
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');                // id cliente
            $table->foreignId('bidding_id')->nullable()->constrained('biddings')->onDelete('cascade');              // id gara
            $table->string('manage_current')->nullable();                                                           // gestione attuale
            $table->string('manage_offer')->nullable();                                                             // gestione offerta
            $table->decimal('revenue', 15, 2)->nullable();                                                          // gettito
            $table->string('conditions')->nullable();                                                               // condizioni
            $table->boolean('invitation_require_check')->default(0);                                                // richiesta di invito necessaria
            $table->string('mode')->nullable();                                                                     // modalità
            // tipo procedura
            $table->boolean('open_procedure_check')->default(0);                                                    // procedura aperta
            $table->boolean('invitation_request_check')->default(0);                                                // richiesta di invito inviata
            $table->date('invitation_request_date')->nullable();                                                    // data invio richiesta di invito
            $table->string('invitation_request_processing_state')->nullable();                                      // stato lavorazione richiesta di invito (enum TenderItemProcessingState)
            $table->boolean('reliance_require_check')->default(0);                                                  // necessità avvalimento
            $table->boolean('reliance_admit_check')->default(0);                                                    // ammissione avvalimento
            $table->string('reliance_company')->nullable();                                                         // partner avvalimento
            $table->date('reliance_date')->nullable();                                                              // data avvalimento
            $table->string('reliance_processing_state')->nullable();                                                // stato lavorazione avvalimento (enum TenderItemProcessingState)
            $table->string('reliance_qualification')->nullable();                                                   // requisiti richiesti
            $table->boolean('partnership_require_check')->default(0);                                               // Associazione Temporanea di Imprese necessaria
            $table->string('partnership_company')->nullable();                                                      // partner Associazione Temporanea di Imprese
            $table->string('partnership_processing_state')->nullable();                                             // stato lavorazione Associazione Temporanea di Imprese (enum TenderItemProcessingState)
            $table->string('partnership_activities')->nullable();                                                   // attività Associazione Temporanea di Imprese
            $table->boolean('collection_require_check')->default(0);                                                // richiesta di invito inviata
            $table->date('collection_request_date')->nullable();                                                    // data invio richiesta di invito
            $table->string('collection_request_processing_state')->nullable();                                      // stato lavorazione richiesta di invito (enum TenderItemProcessingState)
            // documenti richiesti
            $table->boolean('service_reference_require_check')->default(0);                                         // necessità di referenze relative a svolgimento servizi analoghi
            $table->integer('service_reference_number')->nullable();                                                //
            $table->string('service_reference_processing_state')->nullable();                                       // stato lavorazione referenze servizi (enum TenderItemProcessingState)
            $table->string('service_reference_1')->nullable();                                                      // referenza servizi 1
            $table->date('service_reference_date_1')->nullable();                                                   // data referenza servizi 1
            $table->string('service_reference_2')->nullable();                                                      // referenza servizi 2
            $table->date('service_reference_date_2')->nullable();                                                   // data referenza servizi 2
            $table->boolean('bank_reference_require_check')->default(0);                                            // necessità di referenze bancarie
            $table->integer('bank_reference_number')->nullable();                                                   //
            $table->string('bank_reference_processing_state')->nullable();                                          // stato lavorazione referenze bancarie (enum TenderItemProcessingState)
            $table->string('bank_reference_1')->nullable();                                                         // referenza bancaria 1
            $table->date('bank_reference_date_1')->nullable();                                                      // data referenza bancaria1
            $table->string('bank_reference_2')->nullable();                                                         // referenza bancaria 2
            $table->date('bank_reference_date_2')->nullable();                                                      // data referenza bancaria 2
            $table->boolean('pass_oe_require_check')->default(0);                                                   // previsto PASS OE
            $table->date('pass_oe_require_deadline_date')->nullable();                                              // data PASS OE
            $table->string('pass_oe_require_processing_state')->nullable();                                         // stato lavorazione PASS OE (enum TenderItemProcessingState)
            $table->string('inspection_processing_state')->nullable();                                              // stato lavorazione sopralluogo (enum TenderItemProcessingState)
            $table->boolean('deposit_require_check')->default(0);                                                   // richiesta cauzione provvisoria
            $table->string('deposit_require_amount')->nullable();                                                   // importo cauzione provvisoria
            $table->date('deposit_require_date')->nullable();                                                       // data richiesta cauzione provvisoria
            $table->string('deposit_require_processing_state')->nullable();                                         // stato richiesta cauzione provvisoria (enum TenderItemProcessingState)
            $table->boolean('authority_tax_require_check')->default(0);                                             // richiesta versamento contributo autorità di vigilanza
            $table->decimal('authority_tax_require_amount', 15, 2)->nullable();                                     // importo contributo autorità di vigilanza
            $table->date('authority_tax_payment_date')->nullable();                                                 // data versamento contributo autorità di vigilanza
            $table->string('authority_tax_processing_state')->nullable();                                           // stato versamento contributo autorità di vigilanza (enum TenderItemProcessingState)
            $table->boolean('project_require_check')->default(0);                                                   // richiesta realizzazione progetto di gestione
            $table->string('tender_project_format')->nullable();                                                    // formato progetto di gestione
            $table->string('project_processing_state')->nullable();                                                 // stato lavorazione progetto di gestione (enum TenderItemProcessingState)
            $table->text('project_points')->nullable();                                                             // punti principali del progetto di gestione
            $table->integer('project_max_page')->nullable();                                                        // numero massimo pagine progetto di gestione
            $table->string('project_format')->nullable();                                                           // formato progetto di gestione
            $table->string('project_character')->nullable();                                                        // carattere progetto di gestione
            $table->string('project_dimension')->nullable();                                                        // dimensioni progetto di gestione
            $table->string('project_spacing')->nullable();                                                          // interlinea progetto di gestione
            $table->string('project_printed')->nullable();                                                          // stampa progetto di gestione
            $table->string('security_utility')->nullable();                                                         // utilità oneri di sicurezza
            $table->string('security_method')->nullable();                                                          // metodo oneri di sicurezza
            $table->string('security_processing_state')->nullable();                                                // stato lavorazione oneri di sicurezza (enum TenderItemProcessingState)
            $table->string('staff_utility')->nullable();                                                            // utilità costo del personale
            $table->string('staff_method')->nullable();                                                             // metodo costo del personale
            $table->string('staff_processing_state')->nullable();                                                   // stato lavorazione costo del personale (enum TenderItemProcessingState)
            $table->string('other')->nullable();                                                                    // altro contenuto obbligatorio dell'offerta
            $table->string('other_utility')->nullable();                                                            // utilità altro
            $table->string('other_method')->nullable();                                                             // metodo altro
            $table->string('other_processing_state')->nullable();                                                   // stato lavorazione altro (enum TenderItemProcessingState)
            $table->text('note')->nullable();                                                                       // note
            $table->foreignId('modified_user_id')->nullable()->constrained('users')->onDelete('cascade');           // id ultimo utente che ha modificato l'appalto
            $table->date('modified_date')->nullable();                                                              // data ultima modifica appalto
            $table->timestamps();
        });

        Schema::create('tender_necessary_docs', function (Blueprint $table) {                                       // tabella documenti necessari appalto
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');                            // id gara
            $table->string('doc')->nullable();                                                                      // documento
            $table->string('doc_processing_state')->nullable();                                                     // stato lavorazione documento (enum TenderItemProcessingState)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tender_necessary_docs');
        Schema::dropIfExists('tenders');
        Schema::enableForeignKeyConstraints();
    }
};
