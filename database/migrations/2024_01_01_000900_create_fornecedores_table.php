<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->bigInteger('empresa_id')->unsigned()->nullable();
            $table->bigInteger('prefeitura_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned();

            // Vínculo atualizado para CONTRATOS
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->onDelete('set null');

            $table->foreignId('secretaria_id')->nullable()->constrained('secretarias');

            // Campos de dados
            $table->text('descricao');

            // Alguns desses campos agora são redundantes pois estão no contrato,
            // mas mantive para não quebrar o legado imediato.
            $table->string('numero_processo', 100)->nullable();
            $table->string('numero_contrato', 100)->nullable();
            $table->string('modalidade_licitacao')->nullable();
            $table->string('contratante')->nullable();

            $table->text('objeto')->nullable();
            $table->text('data')->nullable();
            $table->decimal('valor_total_fornecimento', 15, 2)->nullable();
            $table->string('local_entrega')->nullable();
            $table->string('periodo_entrega')->nullable();
            $table->string('prazo_entrega')->nullable();
            $table->string('arquivo_pdf')->nullable();
            $table->integer('ordem')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fornecedores');
    }
};
