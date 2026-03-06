<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_requisicao')->unique();
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('data_requisicao');
            $table->date('data_prevista_entrega');
            $table->date('data_efetiva_entrega')->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->enum('status', ['pendente', 'aprovado', 'entregue', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};
