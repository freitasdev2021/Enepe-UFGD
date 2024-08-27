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
            $table->integer('IDEvento');
            $table->text('IDMeeting');
            $table->string('URLMeeting',100);
            $table->string('Titulo',30);
            $table->string('Descricao',100);
            $table->dateTime('Inicio');
            $table->string('PWMeeting',45);
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
