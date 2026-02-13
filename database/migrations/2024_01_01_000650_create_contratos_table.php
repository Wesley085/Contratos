<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();

            // Relacionamentos Principais
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('prefeitura_id')->constrained('prefeituras')->onDelete('cascade');
            $table->foreignId('secretaria_id')->constrained('secretarias')->onDelete('cascade'); // O "Contratante"

            // Dados de Identificação (Vindos do antigo Processo)
            $table->string('numero_processo');
            $table->string('numero_contrato')->nullable();
            $table->string('modalidade')->nullable();

            // Definição do Fluxo (Novo)
            $table->enum('tipo_contrato', ['Fornecimento', 'Serviço'])->default('Fornecimento');

            $table->text('objeto');

            // Financeiro e Vigência (Vindos do antigo Contrato)
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->date('data_assinatura')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_finalizacao')->nullable(); // CRÍTICO: Usado para calcular "Vigente/Vencido"

            // Arquivos e Controle
            $table->string('arquivo_contrato')->nullable();
            $table->string('situacao_manual')->nullable(); // Para forçar "Cancelado" se precisar

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
