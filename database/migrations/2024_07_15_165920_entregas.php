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
        Schema::create('entergas',function(Blueprint $table){
            $table->id();
            $table->integer('IDInscrito');
            $table->integer('IDSubmissao');
            $table->integer('IDAvaliador');
            $table->string('Tematica',45);
            $table->integer('IDApresentador');
            $table->string('Titulo',50);
            $table->mediumText('Descricao');
            $table->string('Autores',100);
            $table->string('palavrasChave',100);
            $table->string('Status');
            $table->bigInteger('NEntrega');
            $table->string('Feedback',100);
            $table->date('created_at');
            $table->date('updated_at');
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
