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
        Schema::create('eventos',function(Blueprint $table){
            $table->id();
            $table->string('Titulo',50)->nullable(false);
            $table->string('Descricao',250)->nullable(false);
            $table->dateTime('Inicio');
            $table->dateTime('Termino');
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
