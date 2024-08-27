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
            $table->date('created_at');
            $table->date('updated_at');
            $table->datetime('INISubmissao');
            $table->datetime('TERSubmissao');
            $table->date("INIInscricao");
            $table->date('TERInscricoes');
            $table->text('Normas');
            $table->text('Contatos');
            $table->string('Capa',250);
            $table->string('ModeloApresentacao',250);
            $table->text('Categorias');
            $table->text('Modalidades');
            $table->text('Site');
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
