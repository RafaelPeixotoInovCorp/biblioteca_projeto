<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes_disponibilidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained('users')->onDelete('cascade');
            $table->boolean('notificado')->default(false);
            $table->timestamp('notificado_em')->nullable();
            $table->timestamps();

            // Garantir que um cidadão só pode ter uma notificação ativa por livro
            $table->unique(['livro_id', 'cidadao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes_disponibilidade');
    }
};
