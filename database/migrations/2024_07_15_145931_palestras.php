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
        Schema::create('palestras',function(Blueprint $table){
            $table->id();
            $table->string('Titulo',50)->nullable(false);
            $table->string('Palestra',100);
            $table->date('Data');
            $table->integer('IDEvento');
            $table->integer('IDPalestrante');
            $table->time('Inicio');
            $table->time('Termino');
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
