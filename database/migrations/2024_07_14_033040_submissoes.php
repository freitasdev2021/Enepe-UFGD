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
        Schema::create('submissoes',function(Blueprint $table){
            $table->id();
            $table->integer('IDEvento');
            $table->string('Categoria',45);
            $table->string('Regras',100);
            $table->bigInteger('MaxLength');
            $table->bigInteger('MinLength');
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
