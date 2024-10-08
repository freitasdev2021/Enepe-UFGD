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
        Schema::create('palestrantes',function(Blueprint $table){
            $table->id();
            $table->string('Nome',50)->nullable(false);
            $table->string('Curriculo',100);
            $table->string('Foto',100);
            $table->string('Email',50);
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
