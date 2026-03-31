<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens_carrinho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrinho_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2);
            $table->timestamps();

            $table->unique(['carrinho_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_carrinho');
    }
};
