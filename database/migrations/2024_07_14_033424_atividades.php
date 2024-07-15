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
        Schema::create('atividades',function(Blueprint $table){
            $table->id();
            $table->intger('IDSala');
            $table->string('Titulo',30);
            $table->string('Descricao',100);
            $table->date('Data');
            $table->time('Inicio');
            $table->time('Termino');
            $table->json('Chat');
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
