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
            $table->string('Titulo',50);
            $table->string('Descricao',100);
            $table->string('Trabalho',100);
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
