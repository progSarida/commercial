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
        Schema::table('clients', function (Blueprint $table) {
           $table->json('surname')->nullable()->after('id');                                    // cognome/denominazione cliente
        });

        Schema::create('referents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');        // id cliente
            $table->string('name');                                                             // nome referente
            $table->string('title')->nullable();                                                // qualifica
            $table->string('phone')->nullable();                                                // telefono
            $table->string('fax')->nullable();                                                  // fax
            $table->string('smart')->nullable();                                                // cell
            $table->string('email')->nullable();                                                // email
            $table->text('note')->nullable();                                                   // note
            $table->timestamps();
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
