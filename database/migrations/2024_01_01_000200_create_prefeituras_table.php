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
        Schema::create('prefeituras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj', 18)->unique();
            $table->string('endereco');
            $table->string('cidade');
            $table->string('telefone', 20);
            $table->string('email');
            $table->string('autoridade_competente');
            $table->string('cor_relatorio', 7)->default('#000000');
            $table->string('timbre')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefeituras');
    }
};
