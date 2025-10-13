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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                                             // nome tipo servizio
            $table->string('description');                                                      // descrizione tipo servizio
            $table->integer('position');                                                        // posizione nella selezione
            $table->boolean('mandatory')->default(0);                                           // flag obbligatorietà servizio
            $table->string('ref');                                                              // riferimento tipo servizio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('service_types');
        Schema::enableForeignKeyConstraints();
    }
};
