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
        Schema::create('certificados',function(Blueprint $table){
            $table->id();
            $table->longText('Certificado');
            $table->integer('IDInscrito');
            $table->integer('IDModelo');
            $table->integer('IDEvento');
            $table->string('Codigo',30);
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
